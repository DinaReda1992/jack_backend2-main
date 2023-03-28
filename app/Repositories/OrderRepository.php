<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Orders;
use App\Models\Balance;
use App\Models\CartItem;
use App\Models\Settings;
use App\Models\OrderShipments;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class OrderRepository
{

    public static function saveOrder($orderId, $user, $payment_method)
    {
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select('cart_items.shop_id', 'cart_items.user_id',
            'cart_items.type', 'users.username as shop_name', 'users.shipment_price', 'users.taxes', 'users.shipment_days')
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where(function ($query) use ($orderId) {
                $query->where('cart_items.order_id', 0);
                $query->orWhere('cart_items.order_id', $orderId);
            })
            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')
            ->get();
        if ($objects) {
            $order = Orders::find(intval($orderId));
            if (!$order) {
                return false;
            }

            foreach ($objects as $object) {

                $cart_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where(function ($query) use ($orderId) {
                        $query->where('cart_items.order_id', 0);
                        $query->orWhere('cart_items.order_id', $orderId);
                    })
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
            $order->payment_method = $payment_method;
            $order->save();
            OrderRepository::send_invoice($order->id);
            if ($payment_method == 8) {
                $tmara = new TmaraRepository();
                $tmara->capturePayment($order, $order->tmara_order_id, $order->final_price);
            }
            return true;
        } else {
            return false;
        }
    }

    private static function send_invoice($id)
    {
        $order = Orders::where('id', $id)->first();
        if ($order->short_code == null) {
            $order->short_code = $order->id . str_random(4);
            $order->save();
        }
        $order->sent_sms = 1;
        $order->save();

        $user = User::find($order->user_id);
        $smsMessage = 'مرحباً
فاتورة طلبك  لدى الطريق الذهبي
 : ' . url('/i/' . $order->short_code);


        $phone = self::convertNum(ltrim($user->phone, '0'));
        $phone_number = '+' . $user->phonecode . $phone;
        $customer_id = Settings::find(25)->value;
        $api_key = Settings::find(26)->value;
        $message_type = "OTP";
        $resp = self::send4SMS($customer_id, $api_key, $smsMessage, $phone_number, 'GoldenRoad');
    }

    public static function convertNum($number)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $english = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        return str_replace($arabic, $english, $number);
    }

    public static function get_data($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public static function send4SMS($oursmsusername, $oursmspassword, $messageContent, $mobileNumber, $senderName)
    {
        $user = $oursmsusername;
        $password = $oursmspassword;
        $sendername = $senderName;
        $text = $messageContent;
        $to = $mobileNumber;
        $getdata = http_build_query(
            $fields = array(
                "username" => $user,
                "password" => $password,
                "message" => $text,
                "numbers" => $to,
                "sender" => $sendername,
                "unicode" => 'e',
                "return" => 'json'
            ));
        $opts = array('http' =>
            array(
                'method' => 'GET',
                'header' => 'Content-Type: text/html; charset=utf-8',
            )
        );
        $context = stream_context_create($opts);
        $response = self::get_data('https://www.4jawaly.net/api/sendsms.php?' . $getdata);

        return $response;
    }


    public static function complete_order($order)
    {
//        $order = Orders::find($request->order_id);
        $final_price = $order->final_price;
        $remaining_money = 0;
        $payed_money = 0;
        if ($order->balance != null) {
            $remaining_money = round(($final_price + $order->balance->price), 2);
            $payed_money = ($order->balance->price * -1);
        } else {
            $remaining_money = round($final_price, 2);
        }
        if ($remaining_money == 0 && $order->payment_method != 5) {
            return [
                'status' => 400,
                'message' => 'لا يوجد طلب'
            ];
        }

        return [
            'status' => 200,
            'message' => '',
            'remaining_money' => $remaining_money,
            'payed_money' => $payed_money,
        ];

    }

    public static function withBalance($request, $user, $order, $ifByOrder = false)
    {
        $total = CartItem::where(function ($q) use ($ifByOrder, $order, $user) {
            $q->where(['order_id' => $order->id, 'user_id' => $user->id]);
            $q->orWhere(['order_id' => 0, 'user_id' => $user->id]);
        })
            ->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))
            ->first()->total;
        $money_transfered = 0;
        if ($request->money_transfered) {
            $money_transfered = round(floatval($request->money_transfered), 2);
        } else {
        }
        $get_balance = Balance::where('user_id', $user->id)->sum('price');
        $payed_balance = $total - $money_transfered;//100 -10
        if ($get_balance < $payed_balance) {
            $payed_balance = $get_balance;
        }
        if (round($get_balance,2) <= 0) {
            return [
                'status' => 400,
                'message' => 'الرصيد غير كافي',
                'get_balance' => round($get_balance,2),
                'payed_balance' => round($payed_balance,2),
                'total' => round($total,2),
            ];
        } else {
            return [
                'status' => 200,
                'get_balance' => round($get_balance,2),
                'payed_balance' => round($payed_balance,2),
                'total' => round($total,2),
            ];
        }
    }

    public static function payWithBalance($request, $order, $user)
    {
        $get_balance = self::withBalance($request, $user, $order, true);

        $finalPrice = $order->final_price;

        if ($get_balance['status'] == 400) {
            return $finalPrice;
        } else {
            if ($get_balance['get_balance'] >= $get_balance['total']) {
                $price = $get_balance['total'];
            } else {
                $price = $get_balance['get_balance'];
            }

            $balance = new Balance();
            $balance->user_id = $user->id;
            $balance->price = -$price;
            $balance->balance_type_id = 12;
            $balance->order_id = $order->id;
            $balance->status = 1;
            $balance->notes = 'استخدام جزئي للمحفظة فى شراء منتجات ' . $order->id;
            $balance->method_name = 'tamara' ;
            $balance->save();
            $order->with_balance = 1;
            $order->save();
            return $finalPrice - $price;
        }
        return $finalPrice;
    }
}
