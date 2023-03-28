<?php

namespace App\Http\Controllers\Panel;

use App\Models\Balance;
use App\Models\BankTransfer;
use App\Models\CartItem;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\RequestMoney;
use App\Models\RequestRepresentative;
use App\Models\RequestUserService;
use App\Models\UserServices;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Orders;
use App\Models\OrdersOrders;
use App\Models\OrdersPhotos;


use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;


class RequestMoneyController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            $this->check_settings(257);
            return $next($request);
        });

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($type="")
    {
        if($type=="new"){
         $objects = RequestMoney::
         where('status',0)->orderBy('id','DESC')->paginate(50);
         $title = "طلبات السحب الجديدة" ;
         $type=0;

        }elseif ($type=="old"){
            $objects = RequestMoney::where('status',1)->orderBy('id','DESC')->paginate(50);
            $title = "طلبات السحب المعالجة" ;
            $type=1;

        }else{
            $objects = RequestMoney::orderBy('id','DESC')->paginate(50);
            $title = "كل الطلبات" ;
            $type=1;

        }
        return view('admin.withdraw.all', ['objects' =>$objects , 'title'=>$title, 'type'=>$type,]);
    }
    public function getBalanceDetails($user_id)
    {
        $shop=User::find($user_id);
if(!$shop){
    return abort(404);
}
        $objects = Balance::where('user_id', $user_id)->get();
        $all_sales = CartItem::join('order_shipments', 'order_shipments.id', 'cart_items.shipment_id')
            ->whereNotIn('order_shipments.status', [1, 5])
            ->where('cart_items.status', '<>', 5)
            ->where('cart_items.shop_id', $user_id)
            ->sum('cart_items.price');
        $finished_sales = CartItem::join('order_shipments', 'order_shipments.id', 'cart_items.shipment_id')
            ->whereIn('order_shipments.status', [4])
            ->where('cart_items.status', '<>', 5)
            ->where('cart_items.shop_id', $user_id)
            ->sum('cart_items.price');

        $current_balance = $objects->sum('price');
        $site_profits = $objects->sum('site_profits');
//        profit_rate
        return view('admin.withdraw.user_balance', ['objects' => $objects, 'all_sales' => $all_sales, 'finished_sales' => $finished_sales,
            'current_balance' => $current_balance, 'site_profits' => $site_profits,'shop'=>$shop]);
    }

    public function cancelled_requests()
    {
        return view('admin.request-money.cancelled', ['objects' => RequestRepresentative::where('status',2)->orderBy('id','DESC')->paginate(50)]);
    }
    public function bank_transfer_order()
    {
        return view('admin.banks.new_transfer_order', ['objects' => BankTransfer::where('type', "order")->orderBy('id', 'DESC')->get()]);
    }

    public function bank_transfer_member()
    {
        return view('admin.banks.new_transfer_member', ['objects' => BankTransfer::where('type', "membership")->orderBy('id', 'DESC')->get()]);
    }

    public function new_orders()
    {
        return view('admin.request-money.new_orders', ['objects' => Orders::where('status', 0)->orderBy('id', 'DESC')->paginate(50)]);
    }

    public function cancelled_orders()
    {
        return view('admin.request-money.cancelled_orders', ['objects' => Orders::where('status', 2)->orderBy('id', 'DESC')->get()]);
    }

    public function approved_orders()
    {
        return view('admin.request-money.approved_orders', ['objects' => Orders::where('status', 1)->orderBy('id', 'DESC')->get()]);
    }

    public function payed_orders()
    {
        return view('admin.request-money.payed_orders', ['objects' => Orders::where('status', 2)->orderBy('id', 'DESC')->get()]);
    }

    public function on_progress_orders()
    {
        return view('admin.request-money.on_progress_orders', ['objects' => Orders::where('status', 3)->orderBy('id', 'DESC')->get()]);
    }

    public function done_orders()
    {
        return view('admin.request-money.done_orders', ['objects' => Orders::where('status', 4)->orderBy('id', 'DESC')->get()]);
    }


    public function normal_ads()
    {
        return view('admin.request-money.normal', ['objects' => Orders::where('adv', 0)->get()]);
    }


    public function adv_ads($id = 0)
    {
        $ads = Orders::find($id);
        if (!$ads) {
            return redirect()->back()->with('error', 'لا يوجد اعلان بهذا العنوان');
        }
        if ($ads->adv == 0) {
            $ads->adv = 1;
            $ads->save();
            return redirect()->back()->with('success', 'تم تثبيت الاعلان في الرئيسية بنجاح .');
        } else {
            $ads->adv = 0;
            $ads->save();
            return redirect()->back()->with('success', 'تم ازالة التثبيت من الرئيسية بنجاح .');
        }

    }


    public function approve_request($id = 0)
    {
        $order = RequestMoney::find($id);
        $order->status=1;
        $order->save();


        $object = new Balance();
        $object->user_id = $order->user_id;
        $object->price = $order->price;
        $object->balance_type_id = 3;
        $object->notes = "تحويل رصيد لحسابه البنكي بعد طلبه المرسل للادارة" ;
        $object->save();



        $notification55 = new Notification();
        $notification55 -> sender_id = 1 ;
        $notification55 -> reciever_id = $order->user_id;
        $notification55 -> type = 14;
        $notification55 -> message =   "قامت الادارة بتحويل المبلغ الذي طلبتموه وهو ".$order->price." ريال الى حسابكم" ;
        $notification55 -> message_en =  "Administration sent to you 50 SAR as you requested in your bank account";
        $notification55->save();
        if(@$notification55->getReciever->lang=="en"){
            $notification_title = "Send you money";
            $notification_body = $notification55 -> message_en;
        }else{
            $notification_title = "تحويل رصيد لحسابكم";
            $notification_body =$notification55 -> message;
        }

        $notification55 ->save();

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification_body)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');


        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' =>[
            'notification_type'=> $notification55 -> type,
            'notification_title'=> $notification_title ,
            'notification_message'=> $notification_body ,
            'notification_data' => null
            ]
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = @$notification55->getReciever->devices->count();
        $tokens = DeviceTokens::where('user_id',$notification55->reciever_id)->pluck('device_token')->toArray();
        $notification_ = @$notification55->getReciever->notification;
        if($token > 0 && $notification_) {
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }

        return redirect()->back()->with('success', 'تمت ارسال اشعار للمستخدم بالتحويل الى حسابه بنجاح .');
    }

    public function on_progress_order($id = 0)
    {
        $order = Orders::find($id);
        $order->status = 3;
        $order->save();

        $order = Orders::where('id', $order->id)
            ->with(['getService' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getYear' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getBrand' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getModel' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getStatus' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getCurrency' => function ($query) {
                $query->select('id', 'name');
            }])
            ->first();
        $order->{"created_time"} = Carbon::parse($order->created_at)->diffForHumans();
        $order->{"message"} = "يتم الان العمل على تقرير طلبك رقم " . $order->id . " ";

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setBody($order->message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');


        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' => $order, 'apps' => ['badge' => Notification::where('reciever_id', $order->user_id)->where('status', 0)->count()], 'type' => 1]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = @$order->getUser->device_token;
        if (@$order->getUser->device_type == "ios") {
            $downstreamResponse = FCM::sendTo($token, null, $notification, $data);
            @$downstreamResponse->numberSuccess();
            @$downstreamResponse->numberFailure();
            @$downstreamResponse->numberModification();

        } elseif (@$order->getUser->device_type) {
            $downstreamResponse = FCM::sendTo($token, null, null, $data);
            @$downstreamResponse->numberSuccess();
            @$downstreamResponse->numberFailure();
            @$downstreamResponse->numberModification();

        }

//        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
//        $downstreamResponse = FCM::sendTo($token, null, null, $data);

        $notification = new Notification();
        $notification->sender_id = 1;
        $notification->reciever_id = $order->user_id;
        $notification->order_id = $order->id;
//        $notification->url = "/admin/orders/".$order->id;
        $notification->message = "جاري العمل طلبك رقم " . $order->id;
        $notification->message_en = "Your order " . $order->id . " is in progress ";

        $notification->type = 3;
        $notification->save();


        return redirect()->back()->with('success', 'تمت تحويل الطلب الى جاري العمل عليه لاصدار التقرير بنجاح .');
    }






    public function cancel_request($id = 0, Request $request)
    {
        $order = RequestMoney::find($id);
        $order->status = 2;
        $order->save();


        $notification55 = new Notification();
        $notification55 -> sender_id = 1 ;
        $notification55 -> reciever_id = $order->user_id;
//        $notification55 -> message_id = $object->id;
        $notification55 -> type = 7;
        $notification55 -> url = '/provider-panel/withdraw';

        $notification55 -> message =   "قامت الادارة برفض طلب سحب الرصيد الخاص بك رقم :" . $order->id;
        $notification55 -> message_en =  " Administration cancelled your withdraw request no " . $order->id ;

        $notification55 ->save();

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        if(@$notification55->getReciever->lang=="en"){
            $notification_title = "Message from administration";
            $notification_body = $notification55 -> message_en;
        }else{
            $notification_title = "رسالة من الادارة";
            $notification_body =$notification55 -> message;
        }

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification_body)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');


        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' =>[
            'notification_type'=> $notification55 -> type,
            'notification_title'=> $notification_title ,
            'notification_message'=> $notification_body ,
            'notification_data' => null
        ]
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = @$notification55->getReciever->devices->count();
        $tokens = DeviceTokens::where('user_id',$notification55->reciever_id)->pluck('device_token')->toArray();
        $notification_ = @$notification55->getReciever->notification;
        if($token > 0 && $notification_) {
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }

//        $order = Orders::where('id', $order->id)
//            ->with(['getService' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getYear' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getBrand' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getModel' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getStatus' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getCurrency' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->first();
//        $order->{"created_time"} = Carbon::parse($order->created_at)->diffForHumans();
//        $order->{"message"} = "تم الغاء طلبك رقم " . $order->id . " ";
//
//        $optionBuilder = new OptionsBuilder();
//        $optionBuilder->setTimeToLive(60 * 20);
//
//        $notificationBuilder = new PayloadNotificationBuilder();
//        $notificationBuilder->setBody($order->message)
//            ->setSound('default');
//
//        $dataBuilder = new PayloadDataBuilder();
//        $dataBuilder->addData(['data' => $order, 'apps' => ['badge' => Notification::where('reciever_id', $order->user_id)->where('status', 0)->count()], 'type' => 1]);
//
//        $option = $optionBuilder->build();
//        $notification = $notificationBuilder->build();
//        $data = $dataBuilder->build();
//
//        $token = @$order->getUser->device_token;
//
////        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
//        if (@$order->getUser->device_type == "ios") {
//            $downstreamResponse = FCM::sendTo($token, null, $notification, $data);
//            @$downstreamResponse->numberSuccess();
//            @$downstreamResponse->numberFailure();
//            @$downstreamResponse->numberModification();
//
//        } elseif (@$order->getUser->device_type) {
//            $downstreamResponse = FCM::sendTo($token, null, null, $data);
//            @$downstreamResponse->numberSuccess();
//            @$downstreamResponse->numberFailure();
//            @$downstreamResponse->numberModification();
//
//        }


        return redirect()->back()->with('success', 'تمت رفض الطلب بنجاح .');


    }
public function withdraw_order($id,Request $request){
        $order=RequestMoney::select('request_money.*','users.username','users.photo')->where('request_money.id',$id)
            ->join('users','users.id','request_money.user_id')->first();
        return view('admin.withdraw.send_balance',['order'=>$order]);
}
    public function postSendBalance($id,Request $request){
        $this->validate($request, [
            'bank_id' => 'required',
            'price' => 'required',

        ]);

        $order=RequestMoney::where('id',$id)->where('status',0)->first();
        if($order){
            $object=new Withdraw();
            $object->user_id=$order->id;
            $object->bank_id=$request->bank_id;
            $object->price=$request->price;
            $object->order_id=$order->id;
            $file = $request->file('photo');
            if ($request->hasFile('photo')) {
                $fileName = 'withdraw-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $request->file('photo')->move($destinationPath, $fileName);
                $object->photo=$fileName;
            }
            $object->save();
            $balance=new Balance();
            $balance->user_id=$order->user_id;
            $balance->price=-$request->price;
            $balance->order_id=$object->id;
            $balance->balance_type_id=7;
            $balance->notes='عملية سحب من الرصيد';
            $balance->save();
            $order->status=1;
            $order->save();
            $notification55 = new Notification();
            $notification55 -> sender_id = 1 ;
            $notification55 -> reciever_id = $order->user_id;
//        $notification55 -> message_id = $object->id;
            $notification55 -> type = 7;
            $notification55 -> url = '/provider-panel/withdraw';

            $notification55 -> message =   "قامت الادارة بقبول طلب سحب الرصيد الخاص بك رقم :" . $order->id;
            $notification55 -> message_en =  " Administration accepted your withdraw request no " . $order->id ;

            $notification55 ->save();

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60*20);

            if(@$notification55->getReciever->lang=="en"){
                $notification_title = "Message from administration";
                $notification_body = $notification55 -> message_en;
            }else{
                $notification_title = "رسالة من الادارة";
                $notification_body =$notification55 -> message;
            }

            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification_body)
                ->setSound('default');
            $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');


            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['data' =>[
                'notification_type'=> $notification55 -> type,
                'notification_title'=> $notification_title ,
                'notification_message'=> $notification_body ,
                'notification_data' => null
            ]
            ]);
            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $token = @$notification55->getReciever->devices->count();
            $tokens = DeviceTokens::where('user_id',$notification55->reciever_id)->pluck('device_token')->toArray();
            $notification_ = @$notification55->getReciever->notification;
            if($token > 0 && $notification_) {
                $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }
            return redirect('/admin-panel/request-money/old')->with('success','تم اجراء التحويل بنجاح وخصم من الرصيد');
        }
        return view('admin.withdraw.send_balance',['order'=>$order]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.request-money.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories|max:100|min:3',
        ]);
        $object = new \App\Models\User();
        $object->name = $request->name;
        $object->save();
        return redirect()->back()->with('success', 'تم اضافة العضو بنجاح .');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.request-money.add', ['object' => Orders::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $object = Orders::find($id);
        $this->validate($request, [
            'name' => 'required|max:100|min:3|unique:categories,name,' . $object->id . ',id',
        ]);

        $object->name = $request->name;
        $object->save();
        return redirect()->back()->with('success', 'تم تعديل العضو بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ads = RequestMoney::find($id);
        if ($ads != false) {
            $ads = RequestMoney::find($ads->id);
            $ads->delete();
        }
    }

    public function delete_order($id = 0)
    {
        $ads = OrdersOrders::find($id);
        if ($ads != false) {
            $ads = OrdersOrders::find($ads->id);
            $ads->delete();
        }
        return redirect()->back('success', 'تم حذف الطلب بنجاح');
    }
}
