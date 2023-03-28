<?php

namespace App\Http\Controllers\Providers;

use App\Models\Balance;
use App\Models\BankTransfer;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\RequestMoney;
use App\Models\RequestRepresentative;
use App\Models\RequestUserService;
use App\Models\UserServices;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Orders;


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
            $this->check_provider_settings(481);
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
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;

        $objects=RequestMoney::where('user_id',$provider_id)->get();
        return view('providers.withdraw.all', ['objects' =>$objects]);
    }

    public function cancel_request_money($id)
    {
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;

        $object=RequestMoney::where('user_id',$provider_id)->where('id',$id)->first();
if($object){
    $object->status=2;
    $object->save();
    return redirect()->back()->with('success','تم الغاء الطلب بنجاح');
}
        return redirect()->back()->with('error','لم تتمكن من الغاء الطلب ');
    }
    public function bank_transfer_order()
    {
        return view('providers.banks.new_transfer_order', ['objects' => BankTransfer::where('type', "order")->orderBy('id', 'DESC')->get()]);
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







    public function cancel_request($id = 0, Request $request)
    {
        $order = RequestRepresentative::find($id);
        $order->status = 2;
        $order->reason_of_cancel = $request->reason_of_cancel;
        $order->save();


        $notification55 = new Notification();
        $notification55 -> sender_id = 1 ;
        $notification55 -> reciever_id = $order->user_id;
//        $notification55 -> message_id = $object->id;
        $notification55 -> type = 4;
        $notification55 -> message =   "رفضت الادارة طلبك بالعمل كمندوب بسبب " . $request->reason_of_cancel;
        $notification55 -> message_en =  " Administration cancelled your request because " . $request->reason_of_cancel ;

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







}
