<?php

namespace App\Repositories;


use FCM;
use App\Models\User;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Jobs\SendSmsAndEmail;
use App\Models\Privileges_groups;
use App\Jobs\SendPushNotification;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use LaravelFCM\Message\OptionsBuilder;
use App\Models\PrivilegesGroupsDetails;
use LaravelFCM\Message\OptionsPriorities;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class NotificationRepository extends Controller
{


    public static function sendFcmNotification(
        $sender,
        $reciever_id,
        $channel_name,
        $channel_time,
        $notification_title,
        $notification_title_en,
        $notification_message,
        $notification_message_en,
        $token = null,
        $platform = 'website')
    {
        $reciever = User::find($reciever_id);
        if ($reciever->lang == "en") {
            $notification_title = $notification_title_en;
            $notification_message = $notification_message_en;
        }

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20)
            ->setPriority(OptionsPriorities::high);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' => [
            'notification_type' => 60,
            'notification_title' => $notification_title,
            'notification_message' => $notification_message,
            'notification_data' => [
                'sender' => $sender,
                'token' => $token
            ],
            'channel_name' => $channel_name,
            'channel_time' => $channel_time
        ]
        ]);

        $option = $optionBuilder->build();
        $notification = null;
//        if ($platform === 'api') {
//            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
//            $notificationBuilder->setBody($notification_message)
//                ->setSound("general_notification.mp3");
//
//            $notification = $notificationBuilder->build();
//        }
////
        $data = $dataBuilder->build();

        $tokens = DeviceTokens::where('user_id', $reciever_id)->get();
        if (count($tokens) > 0) {
            foreach ($tokens as $token) {
                if ($token->ios_token != null) {
                    self::sendIosViopNotification($token->ios_token, $notification_message, $sender);
                } else {
                    $downstreamResponse = FCM::sendTo($token->device_token, $option, $notification, $data);
                }
            }
        }
    }

    public static function sendIosViopNotification($token, $message, $sender)
    {
        $url = 'https://api.sandbox.push.apple.com/3/device/' . $token;
        $body = [
            'isVideo' => true,
            'handle' => $sender,
            'nameCaller' => $sender->userData->user_name,
            "id" => "44d915e1-5ff4-4bed-bf13-c423048ec97a"
        ];
        // Create the payload body
        $body['aps'] = array(
//            'content-available' => 1,
            'alert' => $message,
//            'sound' => 'default',
//            'badge' => 0,
        );

        $headers = [
            'apns-topic: com.entlq.treatab.voip',
            'apns-push-type: voip',
            'authorization:bearer ' . $token
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSLCERT, public_path('/pem/VOIP.pem'));
        curl_setopt($ch, CURLOPT_SSLKEYPASSWD, '1234');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP09_ALLOWED, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $output = curl_exec($ch);
        curl_close($ch);
    }

    public static function send_fcm_notification(
        $sender_id,
        $reciever_id,
        $notification_title,
        $notification_title_en,
        $notification_message,
        $notification_message_en,
        $type,
        $object_in_push,
        $url = '',
        $ifPush = true,
        $privilege_id = 0,
        $ads_id = 0,
        $tokens = null,
        $sound = "default")
    {


        $notification55 = new Notification();
        $notification55->sender_id = $sender_id;
        $notification55->reciever_id = $reciever_id;
        $notification55->type = $type;
        $notification55->message = $notification_message;
        $notification55->message_en = $notification_message_en;
        $notification55->ads_id = $ads_id;
        $notification55->save();
        if ($privilege_id != 0) {//to one user
            //to multiple users ex: pharmacy and their moderators
            $all_groups = Privileges_groups::where('provider_id', $reciever_id)->pluck('id')->toArray();
            $privilege_groups = PrivilegesGroupsDetails::whereIn('privilege_group_id', $all_groups)
                ->where('privilege_id', $privilege_id)
                ->pluck('privilege_group_id')->toArray();
            $users = User::whereIn('privilege_id', $privilege_groups)->pluck('id')->toArray();
//            Log::alert('users array ' . $users);
            foreach ($users as $user) {
                $notification55 = new Notification();
                $notification55->sender_id = $sender_id;
                $notification55->reciever_id = @$user;
                $notification55->type = $type;
                $notification55->type = $url != '' ? $url : '';
                $notification55->message = $notification_message;
                $notification55->message_en = $notification_message_en;
                $notification55->save();
            }
        }


        if ($ifPush) {

            if (@$notification55->getReciever->lang == "en") {
                $notification_title = $notification_title_en;
                $notification_message = $notification_message_en;
            }

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);


            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification_message)
                ->setSound("general_notification.mp3");

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['data' => [
                'notification_type' => $type,
                'notification_title' => $notification_title,
                'notification_message' => $notification_message,
                'notification_data' => $object_in_push,
                'item_id' => $ads_id
            ]
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $tokens = DeviceTokens::where('user_id', $reciever_id)->pluck('device_token')->toArray();
            if (count($tokens) > 0) {
                $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }
        }


    }

    public static function change_status_message($object, $user)
    {
        try {

            $message = 'عميلنا العزيز
(' . $object->user->username . ')
الطلب رقم ' . $object->id . ' في حالة ' . $object->orderStatus->name;

            $message_en = 'Dear customer ' . $object->user->username . ' your order number is ' . $object->id . ' and its status is ' . $object->orderStatus->name_en;
            if (in_array($object->status, [2, 3, 4, 6])) {
                if ($object->shipment_no != '') {
                    $message .= ' ورقم الشحنة ' . $object->shipment_no;
                    $message_en .= ' and the shipment number is ' . $object->shipment_no;
                }

            }
            if ($object->status == 7) {
                $message .= ' في انتظار التقييم ';
                $message_en .= ' and is waiting for rating ';
            }
            $title = 'تتبع طلبك رقم ' . $object->order_id;
            $title_en = 'Track your order ' . $object->order_id;
            $notificationJobs = new SendPushNotification(
                $object->shop->id,
                $object->user->id,
                $title,
                $title_en,
                $message,
                $message_en,
                2,
                $object,
                '',
                true,
                0,
                $object->order_id
            );


            (new self)->dispatch($notificationJobs);

            $smsEmailJobs = new SendSmsAndEmail(
                $user,
                $message,
                $message_en
            );


            (new self)->dispatch($smsEmailJobs);
        } catch (\Exception $exception) {
        }
    }


}
