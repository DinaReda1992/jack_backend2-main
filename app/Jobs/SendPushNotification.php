<?php

namespace App\Jobs;

use App\Repositories\General\FlamingoRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushNotification 
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $sender_id;
    private $reciever_id;
    private $notification_title;
    private $notification_title_en;
    private $notification_message;
    private $notification_message_en;
    private $type;
    private $object_in_push;
    private $url;
    private $ifPush;
    private $sound;
    private $privilege_id;
    private $ads_id;

    public function __construct(
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
        $privilege_id=0,
        $ads_id = 0,
        $sound = "default"
    )
    {
        $this->sender_id = $sender_id;
        $this->reciever_id = $reciever_id;
        $this->notification_title = $notification_title;
        $this->notification_title_en = $notification_title_en;
        $this->notification_message = $notification_message;
        $this->notification_message_en = $notification_message_en;
        $this->type = $type;
        $this->object_in_push = $object_in_push;
        $this->url = $url;
        $this->ifPush = $ifPush;
        $this->sound = $sound;
        $this->privilege_id = $privilege_id;
        $this->ads_id = $ads_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        NotificationRepository::send_fcm_notification(
            $this->sender_id,
            $this->reciever_id,
            $this->notification_title,
            $this->notification_title_en,
            $this->notification_message,
            $this->notification_message_en,
            $this->type,
            $this->object_in_push,
            $this->url,
            $this->ifPush,
            $this->privilege_id,
            $this->ads_id,
            $this->sound = "default"
        );
        /*for ($x = 0; $x <= 1000; $x++) {

        }*/

    }
}
