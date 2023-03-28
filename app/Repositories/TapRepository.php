<?php


namespace App\Repositories;

use App\Models\User;
use App\Models\Orders;
use App\Models\Balance;
use App\Models\CartItem;
use App\Models\Settings;
use App\Models\PaymentLog;
use App\Services\TapPayment;
use App\Models\OrderShipments;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;


class TapRepository
{
    public function checkout(Request $request, $platform = 'api', $complete_order = 'false')
    {
        $user = $platform == 'api' ? JWTAuth::parseToken()->authenticate() : auth()->user();
        if (($request->complete_order == 1 || $request->complete_order == '1')) {
            $order = Orders::where(['user_id' => $user->id, 'id' => $request->order_id])->first();
            $remining_details = $this->complete_order($request);
            if ($remining_details['status'] == 400) {
                return \response()->json($remining_details, 201);
            }
        } else {
            $order = Orders::where(['user_id' => $user->id, 'id' => $request->order_id, 'status' => 0])->where('payment_method', 0)->first();
        }
        if (!$order) {
            return response()->json(
                [
                    'status' => 201,
                    'message' => trans('messages.there_is_no_order'),
                ],
                201
            );
        };
        $amount = $order->final_price;

        $get_balance = $this->withBalance($request, $user, $order);
        if ($get_balance['status'] == 400) {
            return \response()->json($get_balance);
        }
        if ($request->with_balance == 1 || $request->with_balance == '1') {
            $get_balance = $this->withBalance($request, $user, $order, true);
            if ($get_balance['status'] == 400) {
                return \response()->json($get_balance);
            } else {
                if ($get_balance['get_balance'] >= $get_balance['total']) {
                    $price = $get_balance['total'];
                } else {
                    $price = $get_balance['get_balance'];
                }
                $balance = Balance::where(['order_id' => $order->id, 'user_id' => $user->id])->first();
                if (!$balance) {
                    $balance = new Balance();
                }
                $balance->user_id = $user->id;
                $balance->price = -$price;
                $balance->balance_type_id = 12;
                $balance->order_id = $order->id;
                $balance->status = 1;
                $balance->notes = 'استخدام جزئي للمحفظة فى شراء منتجات ' . $order->id;
                $balance->method_name = 'tap-repository-checkout';
                $balance->save();
                $order->with_balance = 1;
                $order->save();
                $amount = $amount - $price;
            }
        }
        if (($request->complete_order == 1 || $request->complete_order == '1')) {
            $remining_details = $this->complete_order($request);
            if ($remining_details['status'] == 400) {
                return \response()->json($remining_details, 201);
            }
            $amount = round($remining_details['remaining_money'], 2);
        }
        $response = (new TapPayment)->pay($amount, $user->id, $user->username, $user->email, $user->phone, $order->id, $platform, $complete_order);
        if ($response['status'] == 200) {
            $order->update(['per_payment_id' => $response['payment_id']]);
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'سيتم تحويلك لصفحة الدفع',
                    'url' => $response['redirect_url'],
                ],
                200
            );
        } elseif ($response['status'] == 400) {
            return response()->json(
                [
                    'status' => 201,
                    'message' =>  'لا يوجد طلب  بهذا الرقم',
                ],
                201
            );
        }
    }

    public function checkout2(Request $request, $platform = 'api', $complete_order = 'false')
    {
        $user = $platform == 'api' ? JWTAuth::parseToken()->authenticate() : auth()->user();

        $check = (new PaymentRepository)->checkPayment($user);
        if ($check->original['status'] == 200) {
            $amount = $check->original['amount'];
            $order = $check->original['order'];
            $complete_order = $check->original['complete_order'];
            $with_balance = $check->original['with_balance'];
            $balance = $check->original['balance'];
        } else {
            return $check;
        }

        $response = (new TapPayment)->pay2($amount, $user->id, $user->username, $user->email, $user->phone, $order->id, $platform, $complete_order, $with_balance, $balance);

        if ($response['status'] == 200) {
            $order->update(['per_payment_id' => $response['payment_id']]);
            return response()->json(['status' => 200, 'message' => 'سيتم تحويلك لصفحة الدفع', 'url' => $response['redirect_url']], 200);
        } elseif ($response['status'] == 400) {
            return response()->json(['status' => 201, 'message' =>  'لا يوجد طلب  بهذا الرقم'], 201);
        }
    }

    public  function withBalance($request, $user, $order, $ifByOrder = false)
    {
        if ($request->with_balance == 1 || $request->with_balance == '1') {
            $total = CartItem::where(function ($q) use ($ifByOrder, $order, $user) {
                $q->where(['order_id' => $order->id, 'user_id' => $user->id]);
                $q->orWhere(['order_id' => 0, 'user_id' => $user->id]);
            })
                ->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;
            $money_transfered = 0;
            if ($request->money_transfered) {
                $money_transfered = round(floatval($request->money_transfered), 2);
            } else {
            }
            $get_balance = Balance::where('user_id', $user->id)->sum('price');
            $payed_balance = $total - $money_transfered; //100 -10
            if ($get_balance < $payed_balance) {
                $payed_balance = $get_balance;
            }
            if (round($get_balance, 2) <= 0) {
                return [
                    'status' => 400,
                    'message' => 'الرصيد غير كافي',
                    'get_balance' => round($get_balance, 2),
                    'payed_balance' => round($payed_balance, 2),
                    'total' => round($total, 2),
                ];
            } else {
                return [
                    'status' => 200,
                    'get_balance' => round($get_balance, 2),
                    'payed_balance' => round($payed_balance, 2),
                    'total' => round($total, 2),
                ];
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
            if ($order->with_balance == 1) {
                $balance = Balance::where(['order_id' => $order->id, 'user_id' => $user->id])->withoutGlobalScopes()->update(['status' => 1]);
            }
            @$this->send_invoice($order->id);

            return [200, ' تم ارسال الطلب  رقم ' . $order->id . ' بنجاح '];

            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'message' => 'تم ارسال الطلب بنجاح'
            ]);
        } else {
            return [400, 'لا يوجد شئ فى السلة'];
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة'
            ]);
        }
    }

    public function complete_order($request)
    {
        $order = Orders::find($request->order_id);
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

    public function verify(Request $request)
    {
        $result = (new TapPayment())->verify($request);
        if ($result['status'] == 200) {
            $user = User::find($result['process_data']['metadata']['udf4']);
            $return_order = $this->saveOrder($result, $user);
            $s = new PaymentLog();
            $s->data = json_encode($result['process_data']['metadata']);
            $s->user_id = $user->id;
            $s->order_id = intval($result['process_data']['metadata']['udf1']);
            $s->platform = $result['process_data']['metadata']['udf2'];
            $s->amount = floatval($result['process_data']['metadata']['udf3']);
            $s->save();

            if ($result['process_data']['metadata']['udf2'] == 'api') {
                if ($return_order[0] != 400) {
                    return redirect('/api/v1/payment-tap-success');
                } else {
                    return redirect('/api/v1/payment-tap-error')->with('message', @$return_order[1]);
                }
            } else {
                if ($return_order[0] != 400) {
                    return redirect('/my-orders')->with('success', 'تم الدفع بنجاح');
                } else {
                    return redirect('/payment-status')->with('message', @$return_order[1]);
                }
            }
        } else { //error
            // $user = User::find($result['process_data']['metadata']['udf4']);
            // if ($user) {
            //     Balance::where('user_id', $user->id)->withoutGlobalScopes()->where('balance_type_id', 12)->where('order_id', intval($result['process_data']['metadata']['udf1']))->delete();
            // }

            if ($result['process_data']['metadata']['udf2'] == 'api') { //api
                return redirect('/api/v1/payment-error')->with('message', @$result['process_data']['metadata']['errorText']);
            } else { //website
                return redirect('/payment-status')->with('message', @$result['process_data']['metadata']['errorText']);
            }
        }

        if ($result['process_data']['metadata']['udf2'] == 'api') {
            return redirect('/api/v1/payment-error')->with('message', @$result['process_data']['metadata']['errorText']);
        } else {
            return redirect()->to('/my-orders')->with('message', @$result['process_data']['metadata']['errorText']);
        }
    }

    public function send_invoice($id)
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


        $phone = $this->convertNum(ltrim($user->phone, '0'));
        $phone_number = '+' . $user->phonecode . $phone;
        $customer_id = Settings::find(25)->value;
        $api_key = Settings::find(26)->value;
        $message_type = "OTP";

        $resp = @$this->send4SMS($customer_id, $api_key, $smsMessage, $phone_number, 'GoldenRoad');
        //        @Log::alert($resp);
        //        return redirect()->back()->with('success', 'تمت إرسال الفاتورة للعميل .');


    }
    public function convertNum($number)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $english = [0,  1,  2,  3,  4,  5,  6,  7,  8,  9];
        return str_replace($arabic, $english, $number);
    }
    public function get_data($url)
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
    public  function send4SMS($oursmsusername, $oursmspassword, $messageContent, $mobileNumber, $senderName)
    {

        $user = $oursmsusername;
        $password = $oursmspassword;
        $sendername = $senderName;
        $text =  $messageContent;
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
            )
        );

        $opts = array(
            'http' =>
            array(
                'method' => 'GET',
                'header' => 'Content-Type: text/html; charset=utf-8',


            )
        );

        $context = stream_context_create($opts);

        $response = $this->get_data('https://www.4jawaly.net/api/sendsms.php?' . $getdata, false, $context);


        return $response;

        // auth call

        //        $url = "https://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E&return=full";

        //لارجاع القيمه json
        //$url = "https://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text
        //&sender=$sendername&unicode=E&return=json";
        // لارجاع القيمه xml
        //$url = "https://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E&return=xml";
        // لارجاع القيمه string
        //$url = "https://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E";
        // Call API and get return message
        //fopen($url,"r");

        //        $ret = file_get_contents($url);
        //        echo nl2br($ret);
    }
}
