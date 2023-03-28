<?php

namespace App\Http\Controllers\Panel;

use App\Models\BankTransfer;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Posts;
use App\Models\PostsPosts;
use App\Models\PostsPhotos;


class PostsController extends Controller
{
    public function __construct()
    {
    
        $this->middleware(function ($request, $next) {
            $this->check_settings(27);
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
        return view('admin.posts.all', ['objects' => Posts::all()]);
    }



    public function new_posts()
    {
        return view('admin.posts.new_posts', ['objects' => Posts::where('status', 0)->orderBy('id', 'DESC')->get()]);
    }

    public function cancelled_posts()
    {
        return view('admin.posts.cancelled_posts', ['objects' => Posts::where('status', 2)->orderBy('id', 'DESC')->get()]);
    }

    public function approved_posts()
    {
        return view('admin.posts.approved_posts', ['objects' => Posts::where('status', 1)->orderBy('id', 'DESC')->get()]);
    }








    public function approve_post($id = 0, Request $request)
    {
        $order = Posts::find($id);
        $order->status = 1;

//        if ($order->getService->elegant_service == 1) {
//            $order->price = $request->price;
//            $order->currency_id = $request->currency_id;
//        }

        $order->save();

//        $order = Posts::where('id', $order->id)
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


        return redirect()->back()->with('success', 'تمت الموافقة على الموضوع بنجاح .');
    }

    public function on_progress_order($id = 0)
    {
        $order = Posts::find($id);
        $order->status = 3;
        $order->save();

        $order = Posts::where('id', $order->id)
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


        return redirect()->back()->with('success', 'تمت تحويل الموضوع الى جاري العمل عليه لاصدار التقرير بنجاح .');
    }

    public function finish_order($id = 0)
    {
        $order = Posts::find($id);
        $order->status = 4;
        $order->save();

        $order = Posts::where('id', $order->id)
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


        return redirect()->back()->with('success', 'تم انهاء الموضوع بنجاح .');
    }


    public function approve_payment($id = 0)
    {
        $order = Posts::find($id);
        $order->status = 2;
        $order->save();

        $order = Posts::where('id', $order->id)
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


        return redirect()->back()->with('success', 'تمت تحويل الموضوع الى مدفوع .');


    }

    public function cancel_post($id = 0, Request $request)
    {
        $order = Posts::find($id);
        $order->status = 2;
//        $order->reason_of_cancel = $request->reason_of_cancel;
        $order->save();

//        $order = Posts::where('id', $order->id)
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


        return redirect()->back()->with('success', 'تمت الغاء الموضوع بنجاح .');


    }

    public function adv_slider($id = 0)
    {
        $ads = Posts::find($id);
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
        return view('admin.posts.ask_orders', ['objects' => PostsPosts::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.posts.add');
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
            'title' => 'required',
            'description' => 'required',
        ]);
        $object = new Posts();
        $object->title = $request->title;
        $object->description = $request->description;
        $object->status=1;
        $object->user_id=auth()->user()->id;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'post-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

        $object->save();
        return redirect()->back()->with('success', 'تم اضافة الموضوع بنجاح .');
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
        return view('admin.posts.add', ['object' => Posts::find($id)]);
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
        $object = Posts::find($id);
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
        ]);

        $object->title = $request->title;
        $object->description = $request->description;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'post-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }


        $object->save();
        return redirect()->back()->with('success', 'تم تعديل الموضوع بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ads = Posts::find($id);
        if ($ads != false) {
            $ads->delete();
        }
    }

    public function delete_order($id = 0)
    {
        $ads = Posts::find($id);
        $ads->delete();

        return redirect()->back('success', 'تم حذف الموضوع بنجاح');
    }
}
