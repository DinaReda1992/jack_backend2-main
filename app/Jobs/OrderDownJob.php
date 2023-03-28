<?php

namespace App\Jobs;

use App\Models\User;
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

class OrderDownJob implements ShouldQueue
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
        $orders = Orders::where('per_payment_id', '!=', null)->where('reference_id', null)->get();
        foreach ($orders as $order) {
            $response = Http::withHeaders([
                "authorization" => "Bearer " . config('tab-payments.TAP_SECRET_KEY'),
            ])->get('https://api.tap.company/v2/charges/' . $order->per_payment_id)->json();
            if (isset($response['status']) && $response['status'] == "CAPTURED") {
                $result = [
                    'status' => 200,
                    'success' => true,
                    'payment_id' => $order->per_payment_id,
                    'process_data' => $response
                ];
                $user = User::find($result['process_data']['metadata']['udf4']);
                $return_order = $this->saveOrder($result, $user);
                $s = new PaymentLog();
                $s->data = json_encode($result['process_data']['metadata']);
                $s->user_id = $user->id;
                $s->order_id = intval($result['process_data']['metadata']['udf1']);
                $s->platform = $result['process_data']['metadata']['udf2'];
                $s->amount = floatval($result['process_data']['metadata']['udf3']);
                $s->save();

                if ($result['process_data']['metadata']['udf6'] == "1") {
                    $balance = new Balance();
                    $balance->user_id = $user->id;
                    $balance->price = -floatval($result['process_data']['metadata']['udf7']);
                    $balance->balance_type_id = 12;
                    $balance->order_id = $order->id;
                    $balance->status = 1;
                    $balance->notes = 'استخدام جزئي للمحفظة فى شراء منتجات ' . $order->id;
                    $balance->method_name = $result['process_data']['metadata']['udf2'] . '-tap-payment';
                    $balance->save();
                    $order->update(['with_balance' => 1]);
                }
            } else {
                Log::info("not Fount Payment");
            }
        }
    }

    public function saveOrder($result, $user)
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

            $order = Orders::find(intval($result['process_data']['metadata']['udf1']));
            if (!$order) {
                return [400, 'لا يوجد طلب بهذا الرقم'];
                return \response()->json([
                    'status' => 400,
                    'message' => 'لا يوجد طلب بهذا الرقم'
                ]);
            }
            $order->reference_id =  $result['process_data']['id'];
            $order->trackId =  $result['process_data']['reference']['track'];

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
            $order->payment_method = 9;
            $order->save();
        }
    }
}
