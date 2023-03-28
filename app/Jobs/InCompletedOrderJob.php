<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\CartItem;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Helpers\SendFcmNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class InCompletedOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userIds = CartItem::doesntHave('order')
            ->where('created_at', '<', now()->subDay())
            ->whereHas('user')
            ->whereHas('cart')
            ->where('status', 0)
            ->groupBy('user_id')
            ->pluck('user_id');

        $users = User::whereIn('id', $userIds)->get();
        foreach ($users as $key => $user) {
            $notification_for_client = new Notification();
            $notification_for_client->sender_id = 1;
            $notification_for_client->reciever_id = $user->id;
            $notification_for_client->order_id = 0;
            $notification_for_client->type = 96;
            $notification_for_client->message = 'لديك سلة لم تكتمل بعد';
            $notification_for_client->message_en = ' You have a cart that is not complete yet';

            if ($user->lang == "en") {
                $notification_title = "You have a cart that is not complete yet";
                $notification_message = $notification_for_client->message_en;
            } else {
                $notification_title = "لديك سلة لم تكتمل بعد";
                $notification_message = $notification_for_client->message;
            }
            $notification_for_client->save();
            SendFcmNotification::send_fcm_notification($notification_title, $notification_message, $notification_for_client, []);
        }
    }
}
