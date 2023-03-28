<?php

namespace App\Http\Controllers\Providers;

use App\Models\BankTransfer;
use App\Models\Hall;
use App\Models\Messages;
use App\Models\Notification;
use App\Models\Reservations;
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


class ReservationsController extends Controller
{
    public function __construct()
    {
    //        $this->middleware(function ($request, $next) {
//            $this->check_settings(44);
//            return $next($request);
//        });

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
     if($request->type == "new"){
         $this->check_provider_settings(398);
         return view('providers.reservations.new', ['objects' => Reservations::where('status',0)
             ->whereIn('hall_id', function ($query) {
                 $query->select('id')
                     ->from(with(new Hall())->getTable())
                     ->where('user_id', auth()->id())
                     ->orWhere('user_id', auth()->user()->main_provider);
             })
             ->where('payment_method','!=',0)
             ->orderBy('id','DESC')->paginate(50)]);
     }elseif($request->type == "approved"){
         $this->check_provider_settings(399);
         return view('providers.reservations.approved', ['objects' => Reservations::where('status',1)
             ->whereIn('hall_id', function ($query) {
                 $query->select('id')
                     ->from(with(new Hall())->getTable())
                     ->where('user_id', auth()->id())
                     ->orWhere('user_id', auth()->user()->main_provider);
             })
             ->where('payment_method','!=',0)
             ->orderBy('id','DESC')->paginate(50)]);
     }elseif($request->type == "cancelled"){
         $this->check_provider_settings(400);
         return view('providers.reservations.cancelled', ['objects' => Reservations::where('status',2)
             ->whereIn('hall_id', function ($query) {
                 $query->select('id')
                     ->from(with(new Hall())->getTable())
                     ->where('user_id', auth()->id())
                     ->orWhere('user_id', auth()->user()->main_provider);
             })
             ->where('payment_method','!=',0)
             ->orderBy('id','DESC')->paginate(50)]);
     }

    }




    public function approve_reservation($id = 0, Request $request)
    {
        $this->check_provider_settings(401);

        $order = Reservations::find($id);
        $order->status = 1;

//        if ($order->getService->elegant_service == 1) {
//            $order->price = $request->price;
//            $order->currency_id = $request->currency_id;
//        }

        $order->save();

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
//
//        $notification = new Notification();
//        $notification->sender_id = 1;
//        $notification->reciever_id = $order->user_id;
//        $notification->order_id = $order->id;
////        $notification->url = "/admin/orders/".$order->id;
//        $notification->message = "تم الموافقة على طلبك رقم " . $order->id;
//        $notification->message_en = "Your order " . $order->id . " was approved ";
//
//        $notification->type = 1;
//        $notification->save();
//
//
//        $order->{"created_time"} = Carbon::parse($order->created_at)->diffForHumans();
//        $order->{"message"} = "تم الموافقة على طلبك رقم " . $order->id . " ";
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
//        if (@$order->getUser->device_type == "ios") {
//            $downstreamResponse = FCM::sendTo($token, null, $notification, $data);
//            @$downstreamResponse->numberSuccess();
//            @$downstreamResponse->numberFailure();
//            @$downstreamResponse->numberModification();
//        } elseif (@$order->getUser->device_type) {
//            $downstreamResponse = FCM::sendTo($token, null, null, $data);
//            @$downstreamResponse->numberSuccess();
//            @$downstreamResponse->numberFailure();
//            @$downstreamResponse->numberModification();
//
//        }
////        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
////        @$downstreamResponse = FCM::sendTo($token, null, null, $data);


        return redirect()->back()->with('success', 'تمت إعتماد الحجز بنجاح .');
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

    public function finish_order($id = 0)
    {
        $order = Orders::find($id);
        $order->status = 4;
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
        $order->{"message"} = "تم انهاء طلبك رقم " . $order->id . " ";

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setBody($order->message)
            ->setSound('default');
        $notification = $notificationBuilder->build();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' => $order, 'apps' => ['badge' => Notification::where('reciever_id', $order->user_id)->where('status', 0)->count()], 'type' => 1]);

        $option = $optionBuilder->build();
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


        $notification = new Notification();
        $notification->sender_id = 1;
        $notification->reciever_id = $order->user_id;
        $notification->order_id = $order->id;
//        $notification->url = "/admin/orders/".$order->id;
        $notification->message = "تم الانتهاء من  طلبك رقم " . $order->id;
        $notification->message_en = "Your order " . $order->id . " was finished ";

        $notification->type = 4;
        $notification->save();


        return redirect()->back()->with('success', 'تم انهاء الطلب بنجاح .');
    }


    public function approve_payment($id = 0)
    {
        $order = Orders::find($id);
        $order->status = 2;
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
        $order->{"message"} = "تم التأكيد الدفع على طلبك رقم " . $order->id . " ";
        $order->{"message_en"} = "Your order " . $order->id . " was payed successfully . ";;

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setBody($order->message)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' => $order, 'apps' => ['badge' => Notification::where('reciever_id', $order->user_id)->where('status', 0)->count()], 'type' => 1]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = @$order->getUser->device_token;

        if (@$order->getUser->device_type == "ios") {
            $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
            @$downstreamResponse->numberSuccess();
            @$downstreamResponse->numberFailure();
            @$downstreamResponse->numberModification();

        } else {
            $downstreamResponse = FCM::sendTo($token, null, null, $data);
            @$downstreamResponse->numberSuccess();
            @$downstreamResponse->numberFailure();
            @$downstreamResponse->numberModification();

        }

        $notification = new Notification();
        $notification->sender_id = 1;
        $notification->reciever_id = $order->user_id;
        $notification->order_id = $order->id;
//        $notification->url = "/admin/orders/".$order->id;
        $notification->message = "تم تأكيد الدفع على  طلبك رقم " . $order->id;
        $notification->message_en = "Your order " . $order->id . " was payed successfully . ";

        $notification->type = 2;
        $notification->save();


        return redirect()->back()->with('success', 'تمت تحويل الطلب الى مدفوع .');


    }

    public function cancel_reservation($id = 0, Request $request)
    {
        $this->check_provider_settings(402);

        $order = Reservations::find($id);
        $order->status = 2;
        $order->reason_of_cancel = $request->reason_of_cancel;
        $order->save();

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


        return redirect()->back()->with('success', 'تم الغاء الحجز بنجاح .');


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
        return view('providers.orders.ask_orders', ['objects' => OrdersOrders::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.orders.add');
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
        $object = new User;
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
        return view('providers.orders.add', ['object' => Orders::find($id)]);
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
        $ads = Orders::find($id);
        if ($ads != false) {
            $ads = Orders::find($ads->id);
            foreach (OrdersPhotos::where('ads_id', $id)->get() as $photo) {
                $old_file = 'uploads/' . $photo->photo;
                if (is_file($old_file)) unlink($old_file);
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
