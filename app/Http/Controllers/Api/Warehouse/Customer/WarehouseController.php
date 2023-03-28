<?php

namespace App\Http\Controllers\Api\Warehouse\Customer;

use Carbon\Carbon;
use App\Models\Orders;
use App\Models\CartItem;
use App\Models\Addresses;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\OrderShipments;
use App\Services\SendNotification;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\AddressResource;
use App\Http\Resources\CartItemResource;

class WarehouseController extends Controller
{
    public function __construct(Request $request)
    {
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        app()->setLocale($language);
        Carbon::setLocale(app()->getLocale());
        $this->middleware(['auth:api','role:warehouse']);
    }

    public function index(Request $request)
    {
        $user = auth('api')->user();

        $orders = Orders::whereIn('status', [2, 3, 4, 6, 7])->where('warehouse_date', '!=', null)
            ->when(request('date'), function ($query) {
                $query->whereDate('warehouse_date', request('date'));
            })
            ->when(request('order_id'), function ($query) {
                $query->where('id', request('order_id'));
            })
            ->when(in_array(request('status'), [2, 3]), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(request('status') == 4, function ($query) {
                $query->whereIn('status', [4, 6, 7]);
            })
            ->withCount('cart_items as products_count')
            ->latest('warehouse_date')
            ->paginate(10);

        $orders->{'orders'} = OrderResource::collection($orders);
        if ($user->user_type_id == 2) {
            $count_orders = Orders::where('status', 2)->whereDate('warehouse_date', now())->count();
            $array2 = ['count_orders' => $count_orders];
        } else {
            $array2 = [];
        }
        return response()->json(['orders' => $orders, 'notification_count' => Notification::where('reciever_id', $user->id)->where('status', 0)->orderBy('id', 'DESC')->count(),] + $array2);
    }

    public function orderDetails($id)
    {
        $order = Orders::where('warehouse_date', '!=', null)->where('id', $id)->whereIn('status', [2, 3, 4, 6, 7])->first();

        if (!$order) {
            return response()->json(['messages' => __('messages.There is no order for this number'), 'status' => 400], 400);
        }

        $address = Addresses::where('id', $order->address_id)->first();

        $cart_items = CartItem::where('order_id', $order->id)->get();

        $order = Orders::where('id', $id)->whereIn('status', [2, 3, 4, 6, 7])->withCount('cart_items as products_count')->first();

        $driver = ['driver_name' => null, 'driver_photo' =>  url('/images/placeholder.png'),];

        if ($order->driver_id) {
            $driver = [
                'driver_name' => $order->driver->username,
                'driver_photo' => $order->driver->photo ? url('/uploads/' . $order->driver->photo) : url('/images/placeholder.png'),
            ];
        }
        return [
            'order_has_driver' => $order->driver ? 1 : 0,
            'order' => OrderResource::make($order),
            'address' => AddressResource::make($address),
            'cart_items' => CartItemResource::collection($cart_items),
        ] + $driver;
    }

    public function changeOrderStatus($id, Request $request)
    {
        $order = Orders::where('warehouse_date', '!=', null)->where('id', $id)->where('status', 2)->first();

        if (!$order) {
            return response()->json(['messages' => __('messages.There is no order for this number')], 400);
        }

        OrderShipments::where('order_id', $order->id)->update(['status' => 3]);
        $order->update(['status' => 3]);
        $order = Orders::where('id', $id)->whereIn('status', [3, 4, 6, 7])->withCount('cart_items as products_count')->first();
        SendNotification::order($order->id);
        return response()->json(['message' => __('messages.The order is being processed for shipment'), 'order' => OrderResource::make($order)]);
    }
}
