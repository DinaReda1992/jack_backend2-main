<?php
namespace App\Helpers;
use App\Huawei\Huawei;
use Illuminate\Support\Facades\Log;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class SendFcmNotification
{
    public static function send_fcm_notification($notification_title, $notification_message, $notification55, $object_in_push, $sound = "notification_default.mp3"){
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);


        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification_message)
            ->setSound($sound);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' => [
            'notification_type' => $notification55->type,
            'notification_title' => $notification_title,
            'notification_message' => $notification_message,
            'notification_data' => $object_in_push
        ]
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = @$notification55->getReciever->device_token;

        if ($token) {
            $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
            // Log::alert($downstreamResponse->numberSuccess());
        }
    }
    public static function send_fcm_notification_to_users($notification_title, $notification_message, $notification55, $object_in_push,$tokens, $device_type='android',$sound = "default"){
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);


        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification_message)
            ->setSound($sound);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' => [
            'notification_type' => $notification55->type,
            'notification_title' => $notification_title,
            'notification_message' => $notification_message,
            'notification_data' => $object_in_push
        ]
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($tokens, $option, $device_type=='ios'?$notification:null, $data);

        $downstreamResponse->numberSuccess();
        // Log::alert('notification ='.$downstreamResponse->numberSuccess());
        // Log::alert('numberFailure ='.$downstreamResponse->numberFailure());
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

//        if ($notification55->getReciever->device_type == "huawei") {
////            $notification = new Huawei($notification55->type, $notification_title, $notification_message, json_encode($object_in_push), $tokens);
////            $notification->sendNotification();
//        } else {
////                $downstreamResponse->numberSuccess();
////                $downstreamResponse->numberFailure();
////                $downstreamResponse->numberModification();
//        }
    }
}