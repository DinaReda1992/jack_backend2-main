<?php

namespace App\Http\Controllers\Providers;

use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use OneSignal;

use App\Models\SendNotifications;


use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class SendNotificationsController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(2);
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
        return view('providers.send-notifications.all',['objects'=>SendNotifications::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.send-notifications.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'message' => 'required|max:100|min:3',
        ]);
        $object = new SendNotifications;
        $object->message = $request->message;
//        $object->type = $request->type;
        $object->save();


            foreach (User::all() as $user) {
                $notification = new Notification();
                $notification->sender_id = 1;
                $notification->reciever_id = $user->id;
                $notification->order_id = 0;
                $notification->message = $object->message;
//                $notification->message_en = $object->message;
                $notification->type = 1;
                $notification->save();
            }



        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notification_title="رسالة من الادارة";

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($object->message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');


        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData(['data' =>[
            'notification_type'=> $notification->type,
            'notification_title'=> $notification_title ,
            'notification_message'=> $object->message ,
            'notification_data' => null
        ]
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

            $tokens  = DeviceTokens::whereIn('user_id', function ($query) use ($request) {
                $query->select('id')
                    ->from(with(new User())->getTable())
                    ->where('notification',1);
            })->pluck('device_token')->toArray();

            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();


		 return redirect()->back()->with('success','تم ارسال الاشعار بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    	return view('providers.send-notifications.add',['object'=> SendNotifications::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $object= SendNotifications::find($id);
        $this->validate($request, [
        'message' => 'required|max:100|min:3|unique:send_notifications,message,'.$object->id.',id',
         ]);
		 $object->message = $request->message;
        $object->type = $request->type;

		 $object->save();
		 return redirect()->back()->with('success','تم تعديل الاشعار بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = SendNotifications::find($id);
        $object->delete();
    }
}
