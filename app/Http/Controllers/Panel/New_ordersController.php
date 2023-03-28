<?php

namespace App\Http\Controllers\Panel;

use FCM;
use Psy\Util\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Requests;
use App\Models\Orders;
use App\Models\Balance;
use App\Models\CartItem;
use App\Models\Messages;
use App\Models\Products;
use App\Models\Settings;
use App\Models\Shipment;
use App\Models\Addresses;
use App\Models\BankTransfer;
use App\Models\Notification;
use App\Models\OrdersOrders;
use App\Models\OrdersPhotos;
use App\Models\UsersRegions;
use Illuminate\Http\Request;
use App\Models\OrderShipments;

use Illuminate\Http\UploadedFile;
use App\Services\SendNotification;
use Illuminate\Support\Facades\DB;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use App\Helpers\SendFcmNotification;
use App\Http\Controllers\Controller;
use LaravelFCM\Message\OptionsBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\MyOrdersResources;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use App\Http\Controllers\Website\Auth\LoginController;

class New_ordersController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
        /*$this->middleware(function ($request, $next) {
            $this->check_settings(44);
            return $next($request);
        });*/
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //        $total= CartItem::where('order_id',301)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
        $objects = Orders::where('status', 1)
            ->when(request()->order_id, function ($query) {
                $query->where('id', request()->order_id);
            })
            //            ->where('marketed_date','!=',null)
            ->where('financial_date', '!=', null)
            ->orderBy('financial_date', 'desc')
            ->paginate(50);
        return view('admin.orders.all', ['objects' => $objects]);
    }

    public function schedule_orders()
    {
        $objects = Orders::where('is_schedul', 1)
            ->where('payment_method', '<>', 0)
            //            ->where('marketed_date','!=',null)

            //                 ->whereIn('region_id', function ($query)  {
            //                    $query->select('region_id')
            //                        ->from(with(new UsersRegions())->getTable())
            //                        ->where('user_id', auth()->id());
            //                })


            ->when(auth()->user()->user_type_id != 1, function ($query) {
                $query->whereHas('user', function (Builder $query) {
                    return $query->whereIn('region_id', function ($query) {
                        $query->select('region_id')
                            ->from(with(new UsersRegions())->getTable())
                            ->where('user_id', auth()->id());
                    });
                });
            })

            ->when(request()->order_id, function ($query) {
                $query->where('id', request()->order_id);
            })
            ->when(!request()->status, function ($query) {
                $query->where('status', 0);
            })
            ->when(request()->status, function ($query) {
                $query->where('status', request()->status);
            })
            ->latest()
            ->paginate(50);
        return view('admin.orders.all', ['objects' => $objects,'type'=>2]);
    }


    public function uploadOne(UploadedFile $file, $folder = null, $filename = null, $disk = 'public')
    {
        $name = !is_null($filename) ? $filename : \Illuminate\Support\Str::random(25);

        $file->storeAs(
            $folder,
            $name . "." . $file->getClientOriginalExtension(),
            $disk
        );
        return $name . "." . $file->getClientOriginalExtension();
    }

    public function approve_order($id = 0, Request $request)
    {
        $order = Orders::find($id);
        $order->status = 2;
        $order->warehouse_date = Carbon::now();
        $order->reviewd_by = auth()->id();
        $order->save();
        OrderShipments::where('order_id', $order->id)->update(['status' => 2]);
        $items = CartItem::where('order_id', $order->id)->select('id', 'item_id', 'quantity')->get();
        foreach ($items as $item) {
            Products::where('id', $item->item_id)->decrement('quantity', $item->quantity);
        }



        $notification_for_client = new Notification();
        $notification_for_client->sender_id = 1;
        $notification_for_client->reciever_id = $order->user_id;
        $notification_for_client->order_id = $order->id;
        $notification_for_client->type = 3;
        $notification_for_client->message = ' جاري تجهيز طلبك رقم ' . $order->id;
        $notification_for_client->message_en = ' Your order num. #' . $order->id . ' On progress ';

        if ($order->user->lang == "en") {
            $notification_title = "Your order On progress";
            $notification_message = $notification_for_client->message_en;
        } else {
            $notification_title = "جاري تجهيز طلبك";
            $notification_message = $notification_for_client->message;
        }
        $notification_for_client->save();
        $select_status = App::getLocale() == "ar" ? 'order_status.name as status_name' : 'order_status.name_en as status_name';
        $order = Orders::where('orders.id', $order->id)->select('orders.id', 'orders.final_price', 'orders.marketed_date', 'orders.is_edit as has_second_order', 'orders.parent_order as has_parent_order', 'orders.status', $select_status, 'order_status.color', 'orders.payment_method', 'payment_methods.name as payment_method_name')
            ->selectRaw('(CONCAT ("' . url('/') . '/i/", orders.short_code)) as download_url')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.order_id =orders.id) as products_count')
            ->leftJoin('order_status', 'order_status.id', 'orders.status')
            ->join('payment_methods', 'orders.payment_method', 'payment_methods.id')
            ->where('orders.payment_method', '<>', 0)
            ->with('transfer_photo.to_bank', 'balance', 'transaction')->first();
        SendFcmNotification::send_fcm_notification($notification_title, $notification_message, $notification_for_client, new MyOrdersResources($order));
        $order->NewOrderNeedToPrepareNotification($order);
        SendNotification::order($order->id);
        return redirect()->back()->with('success', 'تمت الموافقة على الطلب بنجاح .');
    }
    public function cancle_order($id = 0, Request $request)
    {
        $order = Orders::find($id);
        $order->status = 5;
        $order->save();
        OrderShipments::where('order_id', $order->id)->update(['status' => 5]);
        SendNotification::order($order->id);
        return redirect()->back()->with('success', 'تمت الغاء بنجاح .');
    }




    public function show($id)
    {
        OrderShipments::where('order_id', $id)->doesntHave('cart_items')->delete();
        $object = Orders::where('id', $id)->with('shipments.cart_items', 'address', 'user')->first();
        return view('admin.orders.order-show', compact('object'));
    }
    public function edit($id)
    {
        $order = Orders::where('id', $id)->with('shipments.cart_items', 'address', 'user')->first();

        /*
        $total= CartItem::where('order_id',$order->id)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
        $shipment_price=Settings::find(22)->value;
        $taxs=Settings::find(38)->value;
        $order->final_price = $total+$shipment_price+$taxs;
        $order->order_price = $total;
        $order->delivery_price = $shipment_price;
        $order->taxes = $taxs;
        $order->save();*/


        $object = $order;

        return view('admin.orders.invoice', compact('object'));
    }

    public function upload_invoice($id, Request $request)
    {
        $order = Orders::where('id', $id)->first();
        $transfer = BankTransfer::where('order_id', $order->id)->first();
        if (!$transfer) {
            $transfer = new BankTransfer();
            $transfer->order_id = $order->id;
            $transfer->user_id = $order->user_id;
        }
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $name = 'transfer-' . time() . '-' . uniqid();
            $destinationPath = 'uploads';
            $fileName = $this->uploadOne($file, $destinationPath, $name);
            $transfer->photo = $fileName;
            $transfer->save();
            $order->payment_method = 4;
            $order->save();
            return redirect()->back()->with('success', 'تم اضافة صورة التحويل بنجاح');
        } else {
            return redirect()->back()->with('error', 'لا يوجد صورة!');
        }
    }
    public function send_invoice($id)
    {
        $order = Orders::where('id', $id)->first();
        if ($order->short_code == null) {
            $order->short_code = $order->id . str_random(4);
            $order->save();
        }
        //        $order->sent_sms=1;
        //        $order->save();

        $user = User::find($order->user_id);
        $smsMessage = 'مرحباً
تم إنشاء طلب لك لدى الطريق الذهبي.
لعرض تفاصيل الطلب:
 : ' . url('/i/' . $order->short_code);


        $phone = $this->convertNum(ltrim($user->phone, '0'));
        $phone_number = '+' . $user->phonecode . $phone;
        $customer_id = Settings::find(25)->value;
        $api_key = Settings::find(26)->value;
        $message_type = "OTP";
        //            $phone_number = '+' . $user->phonecode . '590002951';
        $resp = $this->send4SMS($customer_id, $api_key, $smsMessage, $phone_number, 'GoldenRoad');
        $resp = json_decode($resp);
        if ($resp->Code == '100') {
            $order->sent_sms = 1;
            $order->save();
            return redirect()->back()->with('success', 'تمت إرسال الفاتورة للعميل .');
        } else {
            return redirect()->back()->with('error', $resp->MessageIs);
        }
        //            return $resp;


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
