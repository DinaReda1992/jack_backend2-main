<?php

namespace App\Http\Controllers\Api\Warehouse\Customer;

use Carbon\Carbon;
use App\Models\Orders;
use App\Helpers\SendSms;
use App\Models\CartItem;
use App\Models\Addresses;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\OrderShipments;
use App\Services\SendNotification;
use App\Helpers\SendFcmNotification;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\AddressResource;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\MyOrdersResources;

class DriverController extends Controller
{
    public function __construct(Request $request)
    {
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        app()->setLocale($language);
        Carbon::setLocale(app()->getLocale());
        $this->middleware(['auth:api', 'role:driver']);
    }

    public function index(Request $request)
    {
        $user = auth('api')->user();

        $orders = Orders::where('driver_id', $user->id)
            ->whereIn('status', [3, 4, 6, 7])
            ->when(request('date'), function ($query) {
                $query->where('delivery_date', request('date'));
            })
            ->when(request('order_id'), function ($query) {
                $query->where('id', request('order_id'));
            })
            ->when(in_array(request('status'), [3, 4, 6, 7]), function ($query) {
                $query->where('status', request('status'));
            })
            ->withCount('cart_items as products_count')
            ->latest()
            ->paginate(10);

        $orders->{'orders'} = OrderResource::collection($orders);
        if ($user->user_type_id == 6) {
            $count_orders = Orders::where('status', 3)->where('driver_id', $user->id)->whereDate('delivery_date', now())->count();
            $array2 = ['count_orders' => $count_orders];
        } else {
            $array2 = [];
        }
        return response()->json(['orders' => $orders, 'notification_count' => Notification::where('reciever_id', $user->id)->where('status', 0)->orderBy('id', 'DESC')->count(),] + $array2);
    }

    public function changeOrderStatus($id)
    {
        $user = auth('api')->user();
        $order = Orders::where('driver_id', $user->id)
            ->where('id', $id)
            ->whereIn('status', [3, 4])
            ->withCount('cart_items as products_count')
            ->first();

        if (!$order) {
            return response()->json(['messages' => __('messages.There is no order for this number')], 400);
        }

        if ($order->status == 3 || $order->status == 4) {
            $notification_for_client = new Notification();
            $notification_for_client->sender_id = 1;
            $notification_for_client->reciever_id = $order->user_id;
            $notification_for_client->order_id = $order->id;
            $notification_for_client->type = 3;
            $notification_title = '';
            $notification_message = '';

            if ($order->status == 3) {
                $notification_for_client->message = ' جاري تجهيز طلبك رقم ' . $order->id;
                $notification_for_client->message_en = ' Your order num. #' . $order->id . ' is being shipped ';

                if ($order->user->lang == "en") {
                    $notification_title = "Your order is being shipped";
                    $notification_message = $notification_for_client->message_en;
                } else {
                    $notification_title = "طلبك قيد الشحن";
                    $notification_message = $notification_for_client->message;
                }
            }
            if ($order->status == 4) {
                $notification_for_client->message = ' طلبك رقم ' . $order->id . ' جاري التوصيل ';
                $notification_for_client->message_en = ' Your order num. #' . $order->id . ' On Delivering ';

                if ($order->user->lang == "en") {
                    $notification_title = "Your order On Delivering";
                    $notification_message = $notification_for_client->message_en;
                    $type_lang = ' And ';
                } else {
                    $notification_title = "طلبك جاري التوصيل";
                    $notification_message = $notification_for_client->message;
                    $type_lang = ' و ';
                }
            }
            $notification_for_client->save();
        }

        if ($order->status == 3) {
            $order->update(['status' => 4]);
            OrderShipments::where('order_id', $order->id)->update(['status' => 4]);
            $notification_for_client->save();
            $order = Orders::where('driver_id', $user->id)
                ->where('id', $id)
                ->whereIn('status', [3, 4])
                ->withCount('cart_items as products_count')
                ->first();
            SendFcmNotification::send_fcm_notification($notification_title, $notification_message, $notification_for_client, new MyOrdersResources($order));
            SendNotification::order($order->id);
            return response()->json(['message' => __('messages.The order has been shipped'), 'order' => new OrderResource($order)]);
        }

        if ($order->status == 4) {
            $order->update(['status' => 6, 'code' => mt_rand(1000, 9999)]);
            OrderShipments::where('order_id', $order->id)->update(['status' => 6]);
            $notification_for_client->save();
            $smsMessage = "كود استلام طلبك رقم {$order->id}: هو '{$order->refresh()->code}'";
            $phone = $order->user->phone;
            $phonecode = $order->user->phonecode;
            (new SendSms)->send($phonecode, $phone, $smsMessage);
            $notification_for_client->update([
                'message' => $notification_for_client->message . ' و ' . $smsMessage,
                'message_en' => $notification_for_client->message_en . ' and ' . ' Your receive code for your order ' . $order->id . ' is ' . $order->refresh()->code
            ]);
            $order = Orders::where('driver_id', $user->id)->where('id', $id)
                ->whereIn('status', [3, 4, 6])
                ->withCount('cart_items as products_count')
                ->first();
            SendFcmNotification::send_fcm_notification($notification_title, $notification_message, $notification_for_client, new MyOrdersResources($order));
            SendNotification::order($order->id);
            return response()->json(['message' => __('messages.The order is now being delivered'), 'order' =>  OrderResource::make($order)]);
        }

        return response()->json(['message' => __('messages.You cannot change the status of the order')]);
    }

    public function completeOrder($id, Request $request)
    {
        $user = auth('api')->user();
        $order1 = Orders::where('driver_id', $user->id)->where('id', $id)->first();

        if (!$order1) {
            return response()->json(['messages' => __('messages.There is no order for this number'), 'status' => 400], 400);
        }

        $order = Orders::where('driver_id', $user->id)->where('id', $id)->where('code', request('code'))->where('status', 6)
            ->first();

        if (!$order) {
            return response()->json(['messages' => __('messages.The receipt code is incorrect'), 'code' => $order1->code, 'status' => 400], 400);
        }
        $order->update(['status' => 7]);
        OrderShipments::where('order_id', $order->id)->update(['status' => 7]);

        $order = Orders::where('driver_id', $user->id)->where('id', $id)->whereIn('status', [7])->withCount('cart_items as products_count')->first();
        SendNotification::order($order->id);
        return response()->json(['message' => __('messages.The order has been completed'), 'order' => OrderResource::make($order), 'status' => 200]);
    }

    public function orderDetails($id)
    {
        $user = auth('api')->user();
        $order = Orders::where('driver_id', $user->id)->where('id', $id)
            ->whereIn('status', [3, 4, 6, 7])->first();

        if (!$order) {
            return response()->json(['messages' => __('messages.There is no order for this number')], 400);
        }

        $address = Addresses::where('addresses.id', $order->address_id)->first();

        $cart_items = CartItem::where('order_id', $order->id)->get();

        $order = Orders::where('driver_id', $user->id)->where('id', $id)->whereIn('status', [3, 4, 6, 7])->withCount('cart_items as products_count')->first();

        return [
            'order' => OrderResource::make($order),
            'address' => AddressResource::make($address),
            'cart_items' => CartItemResource::collection($cart_items),
        ];
    }
}
