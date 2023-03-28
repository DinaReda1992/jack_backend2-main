<?php

namespace App\Http\Controllers\Api\Client\Order;

use App\Models\Orders;
use App\Models\Balance;
use App\Models\CartItem;
use App\Models\Addresses;
use Illuminate\Http\Request;
use App\Models\OrderShipments;
use App\Services\SendNotification;
use App\Http\Resources\OrderResource;
use App\Http\Resources\AddressResource;
use App\Http\Resources\CartItemResource;
use App\Repositories\CartOrderRepository;
use App\Http\Controllers\Api\Client\Controller;

class OrderController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth:api');
    }

    public function addOrder(Request $request)
    {
        $user = auth('api')->user();
        return CartOrderRepository::addOrder($request, $user);
    }

    public function sendOrder(Request $request)
    {
        $user = auth('api')->user();
        return CartOrderRepository::sendOrder($request, $user);
    }

    public function myOrders(Request $request)
    {
        $user = auth('api')->user();
        $orders = Orders::where('user_id', $user->id)
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(request('date'), function ($query) {
                $query->whereDate('delivery_date', request('date'));
            })
            ->when(request('order_id'), function ($query) {
                $query->where('id', request('order_id'));
            })
            ->withCount('cart_items as products_count')
            ->latest()
            ->paginate(10);

        $orders->{'orders'} = OrderResource::collection($orders);
        return response()->json(['orders' => $orders]);
    }

    public function orderDetails(Request $request)
    {
        $user = auth('api')->user();

        $order = Orders::where('user_id', $user->id)->where('id', request()->order_id)
            ->withCount('cart_items as products_count')->first();

        $cartItems = CartItem::with('product')->where('order_id', $order->id)->where('user_id', $user->id)->get();

        $address = Addresses::where('addresses.id', $order->address_id)->first();

        return response()->json(['order' => OrderResource::make($order), 'address' => AddressResource::make($address), 'items' => CartItemResource::collection($cartItems)]);
    }

    public function cancelOrder(Request $request)
    {
        $user = auth('api')->user();

        $order = Orders::where('id', $request->order_id)->where('user_id', $user->id)->where('payment_method', '!=', 0)->where('status', 0)->first();

        if (!$order) {
            return response()->json(['status' => 400, 'message' => __('messages.not_found_order_with_this_number')], 400);
        };

        $order->status = 5;
        $order->save();
        OrderShipments::where('order_id', $order->id)->update(['status' => 5]);
        if ($order->balance != null) {
            $balance = new Balance();
            $balance->user_id = $user->id;
            $balance->price = -1 * $order->balance->price;
            $balance->balance_type_id = 3;
            $balance->status = 1;
            $balance->order_id = $order->id;
            $balance->notes = '  الغاء الطلب رقم ' . $order->id;
            $balance->save();
        }

        SendNotification::cancelOrder($order->id);

        return response()->json(['status' => 200, 'message' => __('messages.The order has been canceled successfully')], 200);
    }
}
