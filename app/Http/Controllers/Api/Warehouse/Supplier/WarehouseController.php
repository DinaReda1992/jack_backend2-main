<?php

namespace App\Http\Controllers\Api\Warehouse\Supplier;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Orders;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Purchase_item;
use App\Models\Purchase_order;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Resources\PurchaseItemResource;
use App\Http\Resources\PurchaseOrderResource;
use App\Http\Resources\SupplierAddressResource;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class WarehouseController extends Controller
{
    public function __construct(Request $request)
    {
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        app()->setLocale($language);
        Carbon::setLocale(app()->getLocale());
        $this->middleware('jwt.auth');
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                if ($user->block == 1) {
                    JWTAuth::invalidate(JWTAuth::getToken());
                    return response()->json(__('messages.you_are_blocked_from_admin'), 401);
                }
            }
        } catch (TokenExpiredException $e) {
            return "1" . $e;
        } catch (TokenInvalidException $e) {
            return "2" . $e;
        } catch (JWTException $e) {
            return "3" . $e;
        }
    }

    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $status = request('status');
        $orders = Purchase_order::withCount('purchase_item as products_count')->with('provider', 'orderStatus')
            ->whereIn('status', [7, 8])
            ->where(function ($query) use ($status) {
                if (request('date')) {
                    $query->whereDate('warehouse_date', request('date'));
                }
                if (request('order_id')) {
                    $query->where('id', request('order_id'));
                }
                if ($status == 7) {
                    $query->where('status', 7);
                }
                if ($status == 8) {
                    $query->where('status', 8);
                }
            })->latest('warehouse_date')
            ->paginate(10);

        $orders->{'orders'} = PurchaseOrderResource::collection($orders);
        if ($user->user_type_id == 2) {
            $customersOrders = Orders::where('status', 2)->whereDate('warehouse_date', now())->count();
            $suppliersOrders = Purchase_order::where('status', 8)->whereDate('warehouse_date', now())->count();
            $array2 = ['customers' => $customersOrders, 'suppliers' => $suppliersOrders];
        } else {
            $array2 = [];
        }
        return response()->json(['orders' => $orders, 'notification_count' => Notification::where('reciever_id', $user->id)->where('status', 0)->orderBy('id', 'DESC')->count(),] + $array2);
    }

    public function receiveOrder($id, Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $order = Purchase_order::where('id', $id)
            ->withCount('purchase_item as products_count')->with('provider', 'orderStatus')
            ->where('status', 8)
            ->first();

        if (!$order) {
            return response()->json(['messages' => 'لا يوجد طلب بهذا الرقم'], 400);
        }

        foreach (json_decode(request('items')) as $key => $item) {
            $purchase_item = Purchase_item::find($item->id);
            if ($purchase_item) {
                $purchase_item->update(['delivered_quantity' => $item->quantity]);
            }
        }

        $order->update(['warehouse_id' => $user->id, 'status' => 7]);
        Purchase_item::where('order_id', $order->id)->update(['status' => 7]);

        return response()->json(['message' => 'الطلب مكتمل', 'order' => PurchaseOrderResource::make($order)]);
    }

    public function orderDetails($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $order = Purchase_order::where('id', $id)->whereIn('status', [7, 8])
            ->withCount('purchase_item as products_count')->with('provider', 'orderStatus')->first();

        if (!$order) {
            return response()->json(['messages' => 'لا يوجد طلب بهذا الرقم'], 400);
        }

        $items = Purchase_item::with('provider', 'product', 'product.measurement')->where('order_id', $order->id)->get();

        $user = User::where('id', $order->provider_id)->with('state', 'region')->first();

        $driver = ['driver_name' => null, 'driver_photo' =>  url('/images/placeholder.png'),];
        if ($order->driver_id) {
            $driver = [
                'driver_name' => $order->driver->username,
                'driver_photo' => $order->driver->photo ? url('/uploads/' . $order->driver->photo) : url('/images/placeholder.png'),
            ];
        }
        return [
            'order_has_driver' => $order->driver ? 1 : 0,
            'order' => PurchaseOrderResource::make($order),
            'address' => SupplierAddressResource::make($user),
            'cart_items' => PurchaseItemResource::collection($items),
        ] + $driver;
    }
}
