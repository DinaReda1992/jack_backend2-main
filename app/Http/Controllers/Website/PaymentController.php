<?php

namespace App\Http\Controllers\Website;

use DB;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Arbpg;
use App\Models\Banks;
use App\Http\Requests;
use App\Models\Cobons;
use App\Models\Orders;
use App\Models\Balance;
use App\Models\CartItem;
use App\Models\Products;
use App\Models\Settings;
use App\Models\Addresses;
use App\Models\PaymentLog;
use App\Models\Reservation;
use Illuminate\Support\Str;
use App\Models\BankAccounts;
use App\Models\BankTransfer;
use Illuminate\Http\Request;
use App\Models\OrderShipments;
use App\Models\CobonsCategories;
use Illuminate\Http\UploadedFile;
use App\Services\SendNotification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\PaymentRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\User\RequestsController;

class PaymentController extends Controller
{

    public function initiatePayment($id)
    {
        $user = \auth()->user();
        $order = Orders::whereId($id)->where('user_id', $user->id)->first();
        $current_balance = Balance::where('user_id', $user->id)->sum('price');

        return view('payment', compact('order', 'current_balance'));
    }

    public  function withBalance($request, $user, $order, $ifByOrder = false)
    {
        if ($request->with_balance == 1 || $request->with_balance == '1') {
            //            $total = CartItem::where(['order_id' => 0, 'user_id' => $user->id])
            //                ->orWhere(['order_id' => $order->id, 'user_id' => $user->id])
            //                ->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;
            $total = CartItem::where(function ($q) use ($ifByOrder, $order, $user) {
                /* if($ifByOrder){
                    $q->where(['order_id' => $order->id, 'user_id' => $user->id]);
                    $q->orWhere(['order_id' => 0, 'user_id' => $user->id]);
                }else{
                    $q->where(['order_id' => 0, 'user_id' => $user->id]);
                }*/
                $q->where(['order_id' => $order->id, 'user_id' => $user->id]);
                $q->orWhere(['order_id' => 0, 'user_id' => $user->id]);
            })
                ->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;
            $money_transfered = 0;
            if ($request->money_transfered) {
                $money_transfered = round(floatval($request->money_transfered), 2);
            } else {
                //                $money_transfered=$total;
            }
            $get_balance = Balance::where('user_id', $user->id)->sum('price');
            $payed_balance = $total - $money_transfered; //100 -10
            if ($get_balance < $payed_balance) {
                $payed_balance = $get_balance;
            }
            if ($get_balance <= 0) {
                return [
                    'status' => 400,
                    'message' => 'الرصيد غير كافي',
                    'get_balance' => $get_balance,
                    'payed_balance' => $payed_balance,
                    'total' => $total,
                ];
            } else {
                return [
                    'status' => 200,
                    'get_balance' => $get_balance,
                    'payed_balance' => $payed_balance,
                    'total' => $total,
                ];
            }
        }
    }


    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_number' => 'required',
            'expiry_month' => 'required',
            'expiry_year' => 'required',
            'cvv' => 'required',
            'holder_name' => 'required',
            'order_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'message' => __('messages.complete_inputs'),], 201);
        }
        $card_number = $request->card_number;
        $expiry_month = $request->expiry_month;
        $expiry_year = $request->expiry_year;
        $cvv = $request->cvv;
        $card_holder = $request->holder_name;

        $user = auth()->user();
        $check = (new PaymentRepository)->checkPayment($user);
        if ($check->original['status'] == 200) {
            $amount = $check->original['amount'];
            $order = $check->original['order'];
        } else {
            return $check;
        }

        $arbPg = new Arbpg();
        $response = $arbPg->getmerchanthostedPaymentid($card_number, $expiry_month, $expiry_year, $cvv, $card_holder, $order->id, $amount, 'web',);

        if ($response['status'] == 200) {
            return response()->json(['status' => 200, 'message' => trans('messages.payment_done'), 'url' => $response['url'], 'amount' => $amount, 'final_price' => $order->final_price,], 200);
        } elseif ($response['status'] == 400) {
            return response()->json(['status' => 201, 'message' => trans('messages.error_reservation'),], 201);
        } else {
            return response()->json(['status' => 202, 'message' => trans('messages.error_card'), 'response' => $response,], 202);
        }
    }
    public function paymentResult(Request $request)
    {

        $trandata = $request->trandata;
        //        var_dump($trandata);
        $arbPg = new Arbpg();

        $result  = $arbPg->getresult($trandata);
        if ($result['status'] == 200) { //success
            $user = User::find($result['data']['udf4']);
            $order = Orders::find(intval($result['data']['udf1']));
            $return_order = $this->saveOrder($result, $user);
            $s = new PaymentLog();
            $s->data = json_encode($result['data']);
            $s->user_id = $user->id;
            $s->order_id = intval($result['data']['udf1']);
            $s->platform = $result['data']['udf2'];
            $s->amount = floatval($result['data']['udf3']);
            $s->save();
            if ($result['data']['udf6'] == "1") {
                $balance = new Balance();
                $balance->user_id = $user->id;
                $balance->price = -floatval($result['data']['udf7']);
                $balance->balance_type_id = 12;
                $balance->order_id = $order->id;
                $balance->status = 1;
                $balance->notes = 'استخدام جزئي للمحفظة فى شراء منتجات ' . $order->id;
                $balance->method_name = $result['data']['udf2'] . '-payment';
                $balance->save();
                $order->update(['with_balance' => 1]);
            }

            if ($result['data']['udf2'] == 'api') {
                if ($return_order[0] != 400) {
                    return redirect('/api/v1/payment-done')->with('message', $return_order[0]);
                } else {
                    return redirect('/api/v1/payment-error')->with('message', @$return_order[1]);
                }
            } else {
                if ($return_order[0] != 400) {
                    return redirect('/my-orders')->with('message', $return_order[1]);
                } else {
                    return redirect('/payment-status')->with('message', @$return_order[1]);
                }
            }
        } else { //error
            if ($result['data']['udf2'] == 'api') { //api
                return redirect('/api/v1/payment-error')->with('message', @$result['data']['errorText']);
            } else { //website
                return redirect('/payment-status')->with('message', @$result['data']['errorText']);
            }
        }

        if ($result['data']['udf2'] == 'api') {
            return redirect('/api/v1/payment-error')->with('message', @$result['data']['errorText']);
        } else {
            return redirect()->to('/my-orders')->with('message', @$result['data']['errorText']);
        }
    }
    public function paymentStatus()
    {
        return view('payment-status');
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
            //            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')->get();
        if ($objects) {

            $order = Orders::find(intval($result['data']['udf1']));
            //            if($order->final_price < floatval($result['data']['udf3'])){
            //                return [400, 'اجمالي الطلب لا يتوافق مع المبلغ المدفوع'];
            //            }
            if (!$order) {
                return [400, 'لا يوجد طلب بهذا الرقم'];
                return \response()->json([
                    'status' => 400,
                    'message' => 'لا يوجد طلب بهذا الرقم'
                ]);
            }
            $order->reference_id =  $result['data']['ref'];
            $order->trackId =  $result['data']['trackId'];

            foreach ($objects as $object) {

                $cart_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where('cart_items.order_id', 0)
                    ->where('cart_items.calculated', 1)
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
                        //                        $product = Products::find($item->item_id);
                        //                        $product->quantity = $product->quantity - $item->quantity;
                        //                        $product->save();

                    }
                }

                //                $notification55 = new Notification();
                //                $notification55->sender_id = $user->id;
                //                $notification55->reciever_id = $object->shop_id;
                //                $notification55->ads_id = $shipment->id;
                //                $notification55->type = 13;
                //                $notification55->url = "/provider-panel/order-details/" . $shipment->id;
                //                $notification55->message = "قام " . $user->username . " بشراء منتجات من متجرك ";
                //                $notification55->message_en = @$user->username . " bought products from your shop.";
                //                $notification55->save();

            }
            $order->payment_method = 2;
            $order->save();
            if ($order->with_balance == 1) {
                $balance = Balance::where(['order_id' => $order->id, 'user_id' => $user->id])->withoutGlobalScopes()->update(['status' => 1]);
            }
            @$this->send_invoice($order->id);
            SendNotification::newOrder($order->id);
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

    public function paymentError()
    {
        return view('payment-error');
    }
    public function paymentDone()
    {
        return view('payment-done');
    }
}
