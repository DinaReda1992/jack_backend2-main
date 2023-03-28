<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Arbpg;
use App\Models\Orders;
use App\Models\Balance;
use App\Models\CartItem;
use App\Models\PaymentLog;
use Illuminate\Bus\Queueable;
use App\Models\OrderShipments;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class OrderMadaDownJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = Orders::where('per_track_id', '!=', null)->where('trackId', null)->get();
        foreach ($orders as $order) {
            $with_balance = $order->check_mada_with_balance && $order->check_mada_with_balance == 1 ? 1 : 0;
            $balance = Balance::where('user_id', $order->user->id)->sum('price');
            if ($order && $order->balance != null) {
                $remaining_money = round(($order->final_price + $order->balance->price), 2);
            } elseif ($order && $with_balance == 1 &&  round($balance, 2) > 0) {
                $remaining_money = round($order->final_price, 2) - round($balance, 2);
            } else {
                $remaining_money = round($order->final_price, 2);
            }
            $amount = $remaining_money;

            $responseChack = (new Arbpg())->checkPayment($order->per_track_id, $amount);

            if ($responseChack['status'] == '1') {

                $response = (new Arbpg())->getresult($responseChack['trandata']);
                if (isset($response['status']) == 200 && $response['data']['result'] == "CAPTURED") {
                    $user = User::find($response['data']['udf4']);
                    $return_order = $this->saveOrder($response, $user);
                    $s = new PaymentLog();
                    $s->data = json_encode($response['data']);
                    $s->user_id = $user->id;
                    $s->order_id = intval($response['data']['udf1']);
                    $s->platform = $response['data']['udf2'];
                    $s->amount = floatval($response['data']['udf3']);
                    $s->save();

                    if ($response['data']['udf6'] == "1") {
                        $balance = new Balance();
                        $balance->user_id = $user->id;
                        $balance->price = -floatval($response['data']['udf7']);
                        $balance->balance_type_id = 12;
                        $balance->order_id = $order->id;
                        $balance->status = 1;
                        $balance->notes = 'استخدام جزئي للمحفظة فى شراء منتجات ' . $order->id;
                        $balance->method_name = $response['data']['udf2'] . '-payment';
                        $balance->save();
                        $order->update(['with_balance' => 1]);
                    }
                } else {
                    Log::info("not Fount Mada Payment");
                }
            }
        }
    }

    public function saveOrder($response, $user)
    {
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.type',
            'users.username as shop_name',
            'users.shipment_price',
            'users.taxes',
            'users.shipment_days'
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where('cart_items.order_id', 0)
            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')->get();
        if ($objects) {

            $order = Orders::find(intval($response['data']['udf1']));
            if (!$order) {
                return [400, 'لا يوجد طلب بهذا الرقم'];
                return \response()->json([
                    'status' => 400,
                    'message' => 'لا يوجد طلب بهذا الرقم'
                ]);
            }
            $order->reference_id =  $response['data']['ref'];
            $order->trackId =  $response['data']['trackId'];

            foreach ($objects as $object) {

                $cart_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where('cart_items.order_id', 0)
                    ->where('shop_id', $object->shop_id)
                    ->where('cart_items.user_id', $object->user_id)
                    ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                    ->join('products', 'cart_items.item_id', 'products.id')->get();

                $shipment = new OrderShipments();
                $shipment->order_id = $order->id;
                $shipment->user_id = $user->id;
                $shipment->shop_id = $object->shop_id;
                $shipment->delivery_date = ' بعد ' . $object->shipment_days . ' يوم';
                $shipment->delivery_date_en = ' after ' . $object->shipment_days . ' days';

                $shipment->delivery_price = $object->shipment_price;
                $shipment->taxes = $object->taxes;

                $shipment->status = 1;
                $shipment->save();
                foreach ($cart_items as $item) {
                    $cart_item = CartItem::find($item->id);
                    if ($cart_item) {
                        $cart_item->order_id = $order->id;
                        $cart_item->shipment_id = $shipment->id;
                        $cart_item->status = 1;
                        $cart_item->save();
                    }
                }
            }
            $order->payment_method = 2;
            $order->save();
        }
    }
}
