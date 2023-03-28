<?php

namespace App\Http\Controllers\Panel;

use App\Models\Addresses;
use App\Models\Balance;
use App\Models\BankTransfer;
use App\Models\CartItem;
use App\Models\Messages;
use App\Models\Notification;
use App\Models\OrderShipments;
use App\Models\Products;
use App\Models\Purchase_item;
use App\Models\Purchase_order;
use App\Models\Purchase_payment_method;
use App\Models\Settings;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Orders;
use App\Models\OrdersOrders;
use App\Models\OrdersPhotos;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Psy\Util\Str;
use App\Http\Controllers\Website\Auth\LoginController;

class DeliveryController extends Controller
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
        $purchases_orders=Orders::
            selectRaw('(SELECT count(*) FROM purchase_orders ) as all_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE  orders.status=3	) as shipping_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE  orders.status=4	) as in_shipment')
            ->selectRaw('(SELECT count(*) FROM orders WHERE  orders.status=5	) as canceled_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE  orders.status=6	) as progress_shipment')
            ->selectRaw('(SELECT count(*) FROM orders WHERE  orders.status=7	) as completed_orders')
            ->first();
           $status=request()->status;
          $objects=Orders::
            where(function ($query) use ($status) {
                if (\request()->order_id) {
                $query->where('id', request()->order_id);
                }

                if ($status == 'shipping' || $status=='') {
                    $query->where('status', 3);
                }
                if ($status == 'in_shipment') {
                    $query->where('status', 4);
                }
                if ($status == 'canceled') {
                    $query->where('status', 5);
                }
                if ($status == 'progress_shipment') {
                    $query->where('status', 6);
                }
                if ($status == 'completed_orders') {
                    $query->where('status', 7);
                }
            })->paginate();
        return view('admin.delivery.all',compact('objects','purchases_orders'));
    }






    public function show($id)
    {
        OrderShipments::where('order_id',$id)->doesntHave('cart_items')->delete();
        $object=Orders::where('id',$id)->with('shipments.cart_items','address','user')->where('status','>=',2)->first();
        return view ('admin.delivery.show',compact('object'));
    }

    public function edit($id)
    {
        $order=Orders::where('id',$id)->with('shipments.cart_items','address','user')->first();

        /*
                $total= CartItem::where('order_id',$order->id)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
                $shipment_price=Settings::find(22)->value;
                $taxs=Settings::find(38)->value;
                $order->final_price = $total+$shipment_price+$taxs;
                $order->order_price = $total;
                $order->delivery_price = $shipment_price;
                $order->taxes = $taxs;
                $order->save();*/


        $object=$order;

        return view ('admin.delivery.invoice',compact('object'));
    }

    public function select_driver($id,Request $request){
        $order=Orders::where('id',$id)->first();
        $driver=User::where('id',$request->driver_id)->where('user_type_id',6)->where('is_archived',0)->first();
        if($driver){
            $order->driver_id=$driver->id;
            $order->save();
            return redirect()->back()->with('success','تم تعيين السائق بنجاح');
        }else{
            return redirect()->back()->with('error','لا يوجد سائق');
        }

    }
    public function send_invoice($id){
        $order=Orders::where('id',$id)->first();
        if($order->short_code==null){
            $order->short_code=$order->id.str_random(4);
            $order->save();
        }
        $order->sent_sms=1;
        $order->save();

        $user=User::find($order->user_id);
            $smsMessage = 'مرحباً
تم إنشاء طلب لك لدى الطريق الذهبي.
لعرض تفاصيل الطلب:
 : ' . url('/i/'.$order->short_code);


            $phone = $this->convertNum(ltrim($user->phone, '0'));
            $phone_number = '+' . $user->phonecode . $phone;
            $customer_id = Settings::find(25)->value;
            $api_key = Settings::find(26)->value;
            $message_type = "OTP";

            $resp =$this->send4SMS($customer_id,$api_key,$smsMessage,$phone_number,'GoldenRoad');
            return redirect()->back()->with('success', 'تمت إرسال الفاتورة للعميل .');


    }
    public function convertNum($number){
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $english = [ 0 ,  1 ,  2 ,  3 ,  4 ,  5 ,  6 ,  7 ,  8 ,  9 ];
        return str_replace($arabic,$english, $number);
    }
    public function get_data($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public  function send4SMS($oursmsusername,$oursmspassword,$messageContent,$mobileNumber,$senderName)
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
                "sender"=>$sendername,
                "unicode"=>'e',
                "return"=>'json'
            ));

        $opts = array('http' =>
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





    public function change_order_status($id = 0, Request $request)
    {
        $order = Orders::find($id);
        if (!$order) {
            return redirect()->back()->with('error', 'هذا الطلب لا يمكنك التعامل معه');
        }
        if ($order->status<3) {
            return redirect()->back()->with('error', 'هذا الطلب لا يمكنك التعامل معه');
        }
        if ($order->status==4) {
            if($order->driver_id==null){
                return redirect()->back()->with('error', 'قم بأختيار سائق لتوصيل الطلب أولا');
            }
        }
        $status=$order->status;
        if($status==4){
            $order->status = $status+2;
            OrderShipments::where('order_id',$order->id)->update(['status'=>$status+2]);
            $shipment = OrderShipments::whereId($id)->update(['status'=>$status+2]);
        }else{
            $order->status = $status+1;
            OrderShipments::where('order_id',$order->id)->update(['status'=>$status+1]);
            $shipment = OrderShipments::whereId($id)->update(['status'=>$status+1]);
        }
        $order->save();

        return redirect()->back()->with('success', 'تمت تغيير حالة الطلب بنجاح .');
    }
    public function approved_shipment($id = 0, Request $request)
    {
        $shipment = OrderShipments::whereId($id)->where('status','>=',1)->first();

        $status=$shipment->status;
        if($status==4){
            $shipment->status = $status+2;
        }else{
            $shipment->status = $status+1;
        }
        $shipment->save();
        $if_shipments=OrderShipments::where('order_id',$shipment->order_id)
            ->where('status',$status)
            ->where('id','!=',$shipment->id)->first();
        if(!$if_shipments){
            if($status==4){
                Orders::whereId($shipment->order_id)->update(['status'=>$status+2]);
            }else{
                Orders::whereId($shipment->order_id)->update(['status'=>$status+1]);
            }
        }
        return redirect()->back()->with('success', 'تمت الموافقة على الشحنة بنجاح .');
    }


}
