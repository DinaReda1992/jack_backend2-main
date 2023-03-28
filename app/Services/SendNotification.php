<?php

namespace App\Services;

use App\Models\User;
use App\Models\Orders;
use LaravelFCM\Facades\FCM;
use App\Models\DeviceTokens;
use App\Models\Notification;
use LaravelFCM\Message\OptionsBuilder;
use App\Models\PrivilegesGroupsDetails;
use App\Models\Products;
use Illuminate\Support\Facades\Log;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class SendNotification
{

    public static function newOrder($order_id)
    {
        $order = Orders::find($order_id);
        $privileges =   PrivilegesGroupsDetails::where('privilege_id', 509)->pluck('privilege_group_id')->toArray();
        $users = User::where('block', 0)->WhereIn('privilege_id', $privileges)
            ->when($order->region_id, function ($query) use ($order) {
                $query->whereHas('admin_regions', function ($query) use ($order) {
                    $query->where('region_id', $order->region_id);
                });
            })->get();

        $users = $users->merge(User::where('id', 1)->get());
        foreach ($users as $user) {
            $notification55 = new Notification();
            $notification55->sender_id = $order->user_id;
            $notification55->reciever_id = $user->id;
            $notification55->type = 3;
            $notification55->url = '/admin-panel/orders/' . $order->id;
            $notification55->order_id = $order->id;
            $notification55->message = 'طلب جديد من ' . $order->user->username . ' برقم ' . $order->id;
            $notification55->message_en = 'New Order From ' . $order->user->username . ' Order Number ' . $order->id;
            $notification55->save();
            if (@$notification55->getReciever->lang == "en") {
                $notification_title = 'New Order ';
                $notification_message = 'New Order From ' . $order->user->username . ' Order Number ' . $order->id;
            } else {
                $notification_title = 'طلب جديد ';
                $notification_message = 'طلب جديد من ' . $order->user->username . ' برقم ' . $order->id;
            }

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);


            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification_message)
                ->setSound("general_notification.mp3");

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'data' => [
                    'notification_type' => 3,
                    'notification_title' => $notification_title,
                    'notification_message' => $notification_message,
                    'notification_data' => [],
                    'item_id' => $order->id
                ]
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $tokens = DeviceTokens::where('user_id', $user->id)->pluck('device_token')->toArray();

            if (count($tokens) > 0) {
                $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }
        }
    }

    public static function cancelOrder($order_id)
    {
        $order = Orders::find($order_id);
        $privileges =   PrivilegesGroupsDetails::where('privilege_id', 509)->pluck('privilege_group_id')->toArray();
        $users = User::where('block', 0)->WhereIn('privilege_id', $privileges)
            ->when($order->region_id, function ($query) use ($order) {
                $query->whereHas('admin_regions', function ($query) use ($order) {
                    $query->where('region_id', $order->region_id);
                });
            })->get();
        $users = $users->merge(User::where('id', 1)->get());
        foreach ($users as $user) {
            $notification55 = new Notification();
            $notification55->sender_id = $order->user_id;
            $notification55->reciever_id = $user->id;
            $notification55->type = 3;
            $notification55->url = '/admin-panel/orders/' . $order->id;
            $notification55->order_id = $order->id;
            $notification55->message = 'طلب تم إلغاءه من ' . $order->user->username . ' برقم ' . $order->id;
            $notification55->message_en = 'Order Canceled From ' . $order->user->username . ' Order Number ' . $order->id;
            $notification55->save();
            if (@$notification55->getReciever->lang == "en") {
                $notification_title = 'Order Canceled ';
                $notification_message = 'Order Canceled From ' . $order->user->username . ' Order Number ' . $order->id;
            } else {
                $notification_title = 'طلب تم إلغاءه ';
                $notification_message = 'طلب تم إلغاءه من ' . $order->user->username . ' برقم ' . $order->id;
            }

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);


            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification_message)
                ->setSound("general_notification.mp3");

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'data' => [
                    'notification_type' => 3,
                    'notification_title' => $notification_title,
                    'notification_message' => $notification_message,
                    'notification_data' => [],
                    'item_id' => $order->id
                ]
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $tokens = DeviceTokens::where('user_id', $user->id)->pluck('device_token')->toArray();

            if (count($tokens) > 0) {
                $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }
        }
    }


    public static function editProduct($product)
    {
        self::product(
            $product,
            'قام ' . auth()->user()->username .  ' بالتعديل على المنتج ' . $product->title,
            auth()->user()->username . 'Edited Product  ' . $product->title_en,
            'تعديل منتج',
            'Edit Product '
        );
    }


    public static function createProduct($product)
    {
        self::product(
            $product,
            'قام ' . auth()->user()->username .  ' بانشاء المنتج ' . $product->title,
            auth()->user()->username . 'Created Product  ' . $product->title_en,
            'انشاء منتج',
            'Created Product '
        );
    }

    public static function deleteProduct($product)
    {
//        $product = Products::find($product_id);
        self::product(
            $product,
            'قام ' . auth()->user()->username .  ' بحذف المنتج ' . $product->title,
            auth()->user()->username . 'Deleted Product  ' . $product->title_en,
            'حذف منتج',
            'Deleted Product '
        );
    }

    public static function restoreProduct($product)
    {
//        $product = Products::find($product_id);
        self::product(
            $product,
            'قام ' . auth()->user()->username .  '  باستعادة المنتج  ' . $product->title,
            auth()->user()->username . 'Restored Product  ' . $product->title_en,
            'استعادة منتج',
            'Restored Product '
        );
    }

    public static function stopProduct($product)
    {
//        $product = Products::find($product_id);
        if ($product->stop == 1) {
            self::product(
                $product,
                'قام ' . auth()->user()->username .  '  بتعطيل المنتج  ' . $product->title,
                auth()->user()->username . 'Stopped Product  ' . $product->title_en,
                'تعطيل منتج',
                'Stopped Product '
            );
        } else {
            self::product(
                $product,
                'قام ' . auth()->user()->username .  '  بتفعيل المنتج  ' . $product->title,
                auth()->user()->username . 'Activation Product  ' . $product->title_en,
                'تفعيل منتج',
                'Activation Product '
            );
        }
    }

    public static function product($product, $message, $message_en, $title, $title_en)
    {
//        $product = Products::find($product_id);
        $users = User::whereIn('id', [1, 13])->get();
        foreach ($users as $user) {
            $notification55 = new Notification();
            $notification55->sender_id = auth()->user()->id;
            $notification55->reciever_id = $user->id;
            $notification55->type = 3;
            $notification55->url = '/admin-panel/products/' . $product->id;
            $notification55->order_id = '';
            $notification55->message = $message;
            $notification55->message_en = $message_en;
            $notification55->save();
            if (@$notification55->getReciever->lang == "en") {
                $notification_title = $title_en;
                $notification_message = $message_en;
            } else {
                $notification_title = $title;
                $notification_message = $message;
            }

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);


            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification_message)
                ->setSound("general_notification.mp3");

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'data' => [
                    'notification_type' => 3,
                    'notification_title' => $notification_title,
                    'notification_message' => $notification_message,
                    'notification_data' => [],
                    'item_id' => $product->id
                ]
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $tokens = DeviceTokens::where('user_id', $user->id)->pluck('device_token')->toArray();

            if (count($tokens) > 0) {
                $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }
        }
    }

    public static function order($order_id, $type = 1)
    {
        // type 1=>status 2=>stop order 3=> return to wallet
        $order = Orders::find($order_id);
        $user = auth()->user()->username;
        if ($type == 1) {
            switch ($order->status) {
                case 1:
                    $message = "    قام <$user>  بتاكد الطلب وارساله الي ادارة المالية طلب رقم " . $order->id;
                    $message_en = "$user Accept Order Number #" . $order->id . " And Send to Financial Management ";
                    $title = 'تاكيد طلب جديد';
                    $title_en = 'Accept New Order';
                    break;
                case 2:
                    $message = "    قام <$user> بتاكد الطلب وارساله الي المخزن   " . $order->id;
                    $message_en = "$user Accept Order Number #" . $order->id . " And Send to WareHouse Management ";
                    $title = 'تاكيد طلب من المالية';
                    $title_en = 'Accept New Order From Financial Management ';
                    break;
                case 3:
                    $message = "    قام <$user> بتجهز الطلب وارساله الي جاهز للشحن  " . $order->id;
                    $message_en = "$user Prepare Order Number #" . $order->id . " And Send to Ready To Ship ";
                    $title = 'تجهز الطلب في المخزن';
                    $title_en = 'Prepare New Order In Warehouse Management ';
                    break;
                case 4:
                    $message = "    قام  <$user> باستلام الطلب وبداية الرحلة  " . $order->id;
                    $message_en = "$user Receive Order Number #" . $order->id . " And Start Journey  ";
                    $title = 'استلام الطلب من المخزن';
                    $title_en = 'Receive New Order From Warehouse Management ';
                    break;
                case 5:
                    $message = "    قام  <$user> بالغاء الطلب   " . $order->id;
                    $message_en = "$user  Cancel Order Number #" . $order->id;
                    $title = 'الغاء الطلب';
                    $title_en = 'Cancel Order ';
                    break;
                case 6:
                    $message = "    قام  <$user> بتحول حالة الطلب الي قيد التوصيل  " . $order->id;
                    $message_en = "$user Change Status Order Number #" . $order->id . " To Delivering  ";
                    $title = 'تحول حالة الطلب الي قيد التوصيل';
                    $title_en = 'Change Order To  Delivering';
                    break;
                case 7:
                    $message = "    قام <$user> بتسليم الطلب الي العميل  " . $order->id;
                    $message_en = "$user Delivering the Order Number #" . $order->id . " To the Client ";
                    $title = 'تسليم الطلب الي العميل';
                    $title_en = 'Delivering the Order To the Client';
                    break;
                default:
                    # code...
                    break;
            }
        } elseif ($type == 2) {
            $message = "  قام <$user> بتعليق الطلب في المخزن  " . $order->id;
            $message_en = "$user Stop the Order Number #" . $order->id . " In Warehouse ";
            $title = 'تعليق الطلب في المخزن';
            $title_en = 'Stop the Order In Warehouse';
        } elseif ($type == 3) {
            $message =  "  قام   <$user> باعادة مبلغ الطلب الي المحفظة  " . $order->id;
            $message_en = "$user  Return the Price Order Number # " . $order->id . " To Wallet ";
            $title = 'اعادة مبلغ الطلب الي المحفظة';
            $title_en = 'Return the Price Order To Wallet';
        } elseif ($type == 4) {
            $message =  "  قام <$user>  بتعديل علي الطلب في المخزن وتجهيزه للشحن  " . $order->id;
            $message_en = "$user Edit the Order Number #" . $order->id . " In Warehouse And Send to Ready To Ship ";
            $title = 'تعديل علي الطلب في المخزن';
            $title_en = 'Edit the Order In Warehouse';
        }
        $users = User::whereIn('id', [1, 13])->get();
        foreach ($users as $user) {
            $notification55 = new Notification();
            $notification55->sender_id = auth()->user()->id;
            $notification55->reciever_id = $user->id;
            $notification55->type = 99;
            $notification55->order_id = $order->id;
            $notification55->message = $message;
            $notification55->message_en = $message_en;
            $notification55->save();
            if (@$notification55->getReciever->lang == "en") {
                $notification_title = $title_en;
                $notification_message = $message_en;
            } else {
                $notification_title = $title;
                $notification_message = $message;
            }

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);


            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification_message)
                ->setSound("general_notification.mp3");

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'data' => [
                    'notification_type' => 3,
                    'notification_title' => $notification_title,
                    'notification_message' => $notification_message,
                    'notification_data' => [],
                    'order_id' => $order->id
                ]
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $tokens = DeviceTokens::where('user_id', $user->id)->pluck('device_token')->toArray();

            if (count($tokens) > 0) {
                $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }
        }
    }
}
