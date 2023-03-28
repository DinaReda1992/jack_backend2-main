<?php

namespace App\Http\Controllers\Panel;

use App\Models\BankTransfer;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\RequestRepresentative;
use App\Models\RequestUserService;
use App\Models\UserServices;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Orders;
use App\Models\OrdersOrders;
use App\Models\OrdersPhotos;


use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;


class RepresentativesController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            $this->check_settings(187);
            return $next($request);
        });

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('admin.representatives.all', ['objects' => RequestRepresentative::where('status',0)->orderBy('id','DESC')->paginate(50)]);
    }

    public function cancelled_requests()
    {
        return view('admin.representatives.cancelled', ['objects' => RequestRepresentative::where('status',2)->orderBy('id','DESC')->paginate(50)]);
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
        return view('admin.representatives.new_orders', ['objects' => Orders::where('status', 0)->orderBy('id', 'DESC')->paginate(50)]);
    }

    public function cancelled_orders()
    {
        return view('admin.representatives.cancelled_orders', ['objects' => Orders::where('status', 2)->orderBy('id', 'DESC')->get()]);
    }

    public function approved_orders()
    {
        return view('admin.representatives.approved_orders', ['objects' => Orders::where('status', 1)->orderBy('id', 'DESC')->get()]);
    }

    public function payed_orders()
    {
        return view('admin.representatives.payed_orders', ['objects' => Orders::where('status', 2)->orderBy('id', 'DESC')->get()]);
    }

    public function on_progress_orders()
    {
        return view('admin.representatives.on_progress_orders', ['objects' => Orders::where('status', 3)->orderBy('id', 'DESC')->get()]);
    }

    public function done_orders()
    {
        return view('admin.representatives.done_orders', ['objects' => Orders::where('status', 4)->orderBy('id', 'DESC')->get()]);
    }


    public function normal_ads()
    {
        return view('admin.representatives.normal', ['objects' => Orders::where('adv', 0)->get()]);
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
        $order = RequestRepresentative::find($id);
        $user = User::find($order->user_id);
        $prev_type = $user->user_type_id;
        $user->full_name = $order->full_name;
        $user->car_plate = $order->car_plate;
        $user->brand = $order->brand ;
        $user->model = $order->model;
        $user->bank_id = $order->bank_id;
        $user->liscense = $order->liscense;
        $user->user_type_id=4;
        $user->bank_account = $order->bank_account;
        if($prev_type == 3){
            $user->date_of_representative = date('Y-m-d H:i:s');
        }

        $user->save();



        $notification55 = new Notification();
        $notification55 -> sender_id = 1 ;
        $notification55 -> reciever_id = $user->id;
//        $notification55 -> message_id = $object->id;
        $notification55 -> type = 3;
        if($prev_type==3){
            $notification55 -> message =   "قامت الادارة بالموافقة على طلبك بالعمل كمندوب معنا" ;
            $notification55 -> message_en =  "Administration approved your order to work as a representative";
        }else{
            $notification55 -> message =   "قامت الادارة بالموافقة تعديل بياناتك كمندوب للعمل معنا" ;
            $notification55 -> message_en =  "Administration approved your request for updating you data";

        }

        if(@$notification55->getReciever->lang=="en"){
            $notification_title = "Message from administration";
            $notification_body = $notification55 -> message_en;
        }else{
            $notification_title = "رسالة من الادارة";
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
            'notification_type'=> (int)$notification55 -> type,
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

        UserServices::where('user_id',$user->id)->delete();

        foreach ($order->getServices  as $service){
            $new_service = new UserServices();
            $new_service-> user_id = $user->id;
            $new_service-> service_id = $service->service_id;
            $new_service->save();
            $service->delete();
        }



        $order->delete();


        return redirect()->back()->with('success', 'تمت الموافقة على الطلب بنجاح .');
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
            'notification_type'=> (int)$notification55 -> type,
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

    public function adv_slider($id = 0)
    {
        $ads = Orders::find($id);
        if (!$ads) {
            return redirect()->back()->with('error', 'لا يوجد اعلان بهذا العنوان');
        }
        if ($ads->adv_slider == 0) {
            $ads->adv_slider = 1;
            $ads->save();
            return redirect()->back()->with('success', 'تم تثبيت الاعلان في القسم بنجاح .');
        } else {
            $ads->adv_slider = 0;
            $ads->save();
            return redirect()->back()->with('success', 'تم ازالة التثبيت من القسم  بنجاح .');
        }

    }


    public function orders_adv()
    {
        return view('admin.representatives.ask_orders', ['objects' => OrdersOrders::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.representatives.add');
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
        return view('admin.representatives.add', ['object' => Orders::find($id)]);
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
        $ads = RequestRepresentative::find($id);
        if ($ads != false) {
            $ads = RequestRepresentative::find($ads->id);
            $old_file = 'uploads/' . $ads->photo;
            if (is_file($old_file)) unlink($old_file);
            $old_file = 'uploads/' . $ads->liscense;
            if (is_file($old_file)) unlink($old_file);
            $old_file = 'uploads/' . $ads->national_photo;
            if (is_file($old_file)) unlink($old_file);
            $old_file = 'uploads/' . $ads->back_car;
            if (is_file($old_file)) unlink($old_file);
            $old_file = 'uploads/' . $ads->front_car;
            if (is_file($old_file)) unlink($old_file);

            foreach (RequestUserService::where('user_id', $ads->id)->get() as $photo) {
                $photo->delete();
            }
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
