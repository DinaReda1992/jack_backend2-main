<?php

namespace App\Models;

use App\Helpers\SendFcmNotification;
use App\Http\Resources\PurchaseOrderResource;
use Illuminate\Database\Eloquent\Model;

class Purchase_order extends Model
{
    protected $table = 'purchase_orders';
    protected $guarded = ['id'];
    public function provider()
    {
        return $this->belongsTo('App\Models\User', 'provider_id', 'id');
    }

    public function purchase_item()
    {
        return $this->hasMany(Purchase_item::class, 'order_id', 'id');
    }

    public function orderStatus()
    {
        return $this->belongsTo('App\Models\PurchaseOrderStatus', 'status', 'id');
    }
    public function orderStatusSupplier()
    {
        return $this->belongsTo(SupplierPurcheseStatus::class, 'status', 'id');
    }
    public function paymentMethod()
    {
        return $this->belongsTo('App\Models\PaymentMethods', 'payment_method', 'id');
    }
    public function paymentTerm()
    {
        return $this->belongsTo(Purchase_payment_method::class, 'payment_terms', 'id');
    }
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(User::class, 'warehouse_id', 'id');
    }

    public function DriverOnWayNotification()
    {
        $notification_for_client = new Notification();
        $notification_for_client->order_id = $this->id;
        $notification_for_client->sender_id = 1;
        $notification_for_client->reciever_id = $this->provider_id;
        $notification_for_client->type = 56;
        $notification_title = '';
        $notification_message = '';
        $notification_for_client->message = ' السائق في الطريق اليك لاستلام الطلب  رقم ' . $this->id;
        $notification_for_client->message_en = ' The driver is on the way to you to receive the order No #' . $this->id;
        if ($this->provider->lang == "en") {
            $notification_title = "The driver is on the way to you";
            $notification_message = $notification_for_client->message_en;
        } else {
            $notification_title = "السائق في الطريق اليك";
            $notification_message = $notification_for_client->message;
        }
        $notification_for_client->save();
        SendFcmNotification::send_fcm_notification(
            $notification_title,
            $notification_message,
            $notification_for_client,
            new PurchaseOrderResource($this)
        );
    }


    public function NewOrderNotification()
    {
        $notification_for_client = new Notification();
        $notification_for_client->order_id = $this->id;
        $notification_for_client->sender_id = 1;
        $notification_for_client->reciever_id = $this->provider_id;
        $notification_for_client->type = 56;
        $notification_title = '';
        $notification_message = '';
        $notification_for_client->message = ' لديك طلب شراء جديد رقم الطلب ' . $this->id;
        $notification_for_client->message_en = ' You Have New Purchase Order No #' . $this->id;
        if ($this->provider->lang == "en") {
            $notification_title = "New Purchase Order";
            $notification_message = $notification_for_client->message_en;
        } else {
            $notification_title = "طلب شراء جديد";
            $notification_message = $notification_for_client->message;
        }
        $notification_for_client->save();
        SendFcmNotification::send_fcm_notification(
            $notification_title,
            $notification_message,
            $notification_for_client,
            new PurchaseOrderResource($this)
        );
    }

    public function OrderArrivedWayNotification()
    {
        $users = User::where('user_type_id', 2)->where('privilege_id', 15)->where('block', 0)->get();
        foreach ($users as $key => $user) {
            $notification_for_client = new Notification();
            $notification_for_client->order_id = $this->id;
            $notification_for_client->sender_id = $this->driver_id;
            $notification_for_client->reciever_id = $user->id;
            $notification_for_client->message = ' طلب مورد جديد رقم ' . $this->id . ' وصل ويحتاج إلى التخزين ';
            $notification_for_client->message_en = 'New Purchase Order Num #' . $this->id . ' Arrived and Ready to Storage ';
            $notification_for_client->type = 77;
            $notification_title = '';
            $notification_message = '';

            if ($user->lang == "en") {
                $notification_title = "New Purchase Order Arrived";
                $notification_message = $notification_for_client->message_en;
            } else {
                $notification_title = "طلب مورد وصل ويحتاج إلى التخزين";
                $notification_message = $notification_for_client->message;
            }
            $notification_for_client->save();
            SendFcmNotification::send_fcm_notification(
                $notification_title,
                $notification_message,
                $notification_for_client,
                new PurchaseOrderResource($this)
            );
        }
    }

    public function OrderRefusedNotification()
    {
        $users = User::where('user_type_id', 2)->where('privilege_id', 15)->where('block', 0)->get();
        foreach ($users as $key => $user) {
            $notification_for_client = new Notification();
            $notification_for_client->order_id = $this->id;
            $notification_for_client->sender_id = $this->driver_id;
            $notification_for_client->reciever_id = $user->id;
            $notification_for_client->message = ' الطلب رقم ' . $this->id . ' تم رفضه  من قبل السائق ';
            $notification_for_client->message_en = ' Order Num #' . $this->id . ' was refused by the driver ';
            $notification_for_client->type = 77;
            $notification_title = '';
            $notification_message = '';

            if ($user->lang == "en") {
                $notification_title = "Order Refused";
                $notification_message = $notification_for_client->message_en;
            } else {
                $notification_title = "الطلب رفض";
                $notification_message = $notification_for_client->message;
            }
            $notification_for_client->save();
            SendFcmNotification::send_fcm_notification(
                $notification_title,
                $notification_message,
                $notification_for_client,
                new PurchaseOrderResource($this)
            );
        }
    }

    public function OrderOnWayNotification()
    {
        $users = User::where('user_type_id', 2)->where('privilege_id', 15)->where('block', 0)->get();
        foreach ($users as $key => $user) {

            $notification_for_client = new Notification();
            $notification_for_client->order_id = $this->id;
            $notification_for_client->sender_id = $this->driver_id;
            $notification_for_client->reciever_id = $user->id;
            $notification_for_client->type = 77;
            $notification_title = '';
            $notification_message = '';
            $notification_for_client->message = ' الطلب رقم ' . $this->id . '  في الطريق ';
            $notification_for_client->message_en = ' Order Num #' . $this->id . ' on the way ';
            if ($user->lang == "en") {
                $notification_title = "The Order is on the way to you";
                $notification_message = $notification_for_client->message_en;
            } else {
                $notification_title = "الطلب في الطريق اليك";
                $notification_message = $notification_for_client->message;
            }
            $notification_for_client->save();
            SendFcmNotification::send_fcm_notification(
                $notification_title,
                $notification_message,
                $notification_for_client,
                new PurchaseOrderResource($this)
            );
        }
    }

    public function newOrderDriverNotification()
    {
        $notification55 = new Notification();
        $notification55->sender_id = auth()->user()->id;
        $notification55->reciever_id = $this->driver_id;
        $notification55->order_id = $this->id;
        $notification55->type = 60;
        $notification55->save();
        $notification_title = '';
        $notification_message = '';
        $notification55->message = 'لديك طلب جديد لاستلامه طلب رقم ' . $this->id;
        $notification55->message_en = 'You have a new order to receive order #' . $this->id;
        if ($this->provider->lang == "en") {
            $notification_title = "New Order to receive";
            $notification_message = $notification55->message_en;
        } else {
            $notification_title = "طلب جديد لاستلامه";
            $notification_message = $notification55->message;
        }
        $notification55->save();
        SendFcmNotification::send_fcm_notification(
            $notification_title,
            $notification_message,
            $notification55,
            ['id' => $this->id, 'type' => 2],
        );
    }
}
