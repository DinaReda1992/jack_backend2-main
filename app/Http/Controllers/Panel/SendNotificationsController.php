<?php

namespace App\Http\Controllers\Panel;

use FCM;
use OneSignal;
use App\Models\User;
use App\Http\Requests;
use App\Models\UsersOrders;
use App\Models\DeviceTokens;
use App\Models\Notification;
use Illuminate\Http\Request;
use LaravelFCM\Message\Topics;

use App\Models\SendNotifications;


use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class SendNotificationsController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
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
        return view('admin.send-notifications.all',['objects'=>SendNotifications::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.send-notifications.add');
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
            'title'=> 'required|max:100|min:3',
            'message' => 'required|max:100|min:3',
        ]);
        $object = new SendNotifications;
        $object->title = $request->title;

        $object->message = $request->message;
        $object->type = $request->type;
        $object->save();
        $type=$request->type;
        $users=User::where(function ($query)use ($type){
            if($type){
                $query->where('user_type_id',$type);
            }
        })
//            ->where('id',754)
            ->where('is_archived',0)->where('notification',1)->get();
            foreach ($users as $user) {
                $notification = new Notification();
                $notification->sender_id = 1;
                $notification->reciever_id = $user->id;
//                $notification->order_id = 0;
                $notification->message = $object->message;

//                $notification->message_en = $object->message;
                $notification->type = 1;
                $notification->save();
            }



        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $optionBuilder->setContentAvailable(true);

        $notification_title=$request->title;

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
                $type=$request->type;
                $query->select('id')
                    ->from(with(new User())->getTable())
                    ->where(function ($query)use ($type){
                        if($type){
                            $query->where('user_type_id',$type);
                        }
                        $query->where('notification',1)->where('is_archived',0);
                    });

            })->pluck('device_token')->toArray();
            // Log::info($tokens);
//            $tokens=['eUs3mN4fSwGUCHYbUDdQ9l:APA91bHC69uSeO2Pjqipq7jF8XThashJwvZpFI-kctnlAPineJJ5WlMlysaA-F2B1q8kk1lClBg45FtSKNqzjSCdONRUI_xTU2ws0HLIHZ_o3YMXIV4M1ReHnn6S5nSRC16FFKJG7Wmx'];
            // $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
//                        return dd($downstreamResponse);
            // Log::alert($downstreamResponse);
            // $downstreamResponse->numberSuccess();
            // $downstreamResponse->numberFailure();
            // Log::alert('numberFailure='.$downstreamResponse->numberFailure());
            // Log::alert('numberSuccess='.$downstreamResponse->numberSuccess());
            // $downstreamResponse->numberModification();

            // $tokens_to_delete = $downstreamResponse->tokensWithError();
            // if(count($tokens_to_delete)>0) {
            //     foreach ($tokens_to_delete as $key =>$token) {
            //         $u = DeviceTokens::where('device_token', $token)->delete();
            //         $u = DeviceTokens::where('device_token', $key)->delete();
            //     }
            // }    
    
            $topic = new Topics();
            $topic->topic('golden_general');
            $topicResponse =FCM::sendToTopic($topic, $option, $notification, $data);
            $topicResponse->isSuccess();
    //        Log::alert($topicResponse->isSuccess());
            $topicResponse->shouldRetry();
            $topicResponse->error();
    
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
    	return view('admin.send-notifications.add',['object'=> SendNotifications::find($id)]);
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
            'title'=> 'required|max:100|min:3',

            'message' => 'required|max:100|min:3|unique:send_notifications,message,'.$object->id.',id',
         ]);
		 $object->message = $request->message;
        $object->type = $request->type;
        $object->title = $request->title;

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
