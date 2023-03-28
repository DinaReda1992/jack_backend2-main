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
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Resources\PurchaseItemResource;
use App\Http\Resources\PurchaseOrderResource;
use App\Http\Resources\SupplierAddressResource;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class DriverController extends Controller
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
        $orders = Purchase_order::where('driver_id', $user->id)
            ->withCount('purchase_item as products_count')->with('provider', 'orderStatus')
            ->whereIn('status', [3, 4, 6, 8])
            ->where(function ($query) use ($status) {
                if (request('date')) {
                    $query->where('provider_delivery_date', request('date'));
                }
                if (request('order_id')) {
                    $query->where('id', request('order_id'));
                }
                if ($status == 3) {
                    $query->where('status', 3);
                }
                if ($status == 4) {
                    $query->where('status', 4);
                }
                if ($status == 6) {
                    $query->where('status', 6);
                }
                if ($status == 8) {
                    $query->where('status', 8);
                }
            })->latest()->paginate(10);

        $orders->{'orders'} = PurchaseOrderResource::collection($orders);

        if ($user->user_type_id == 6) {
            $customersOrders = Orders::where('status', 3)->where('driver_id', $user->id)->where('delivery_date', now())->count();
            $suppliersOrders = Purchase_order::where('status', 3)->where('driver_id', $user->id)->where('delivery_date', now())->count();
            $array2 = ['customers' => $customersOrders, 'suppliers' => $suppliersOrders];
        } else {
            $array2 = [];
        }
        return response()->json(['orders' => $orders, 'notification_count' => Notification::where('reciever_id', $user->id)->where('status', 0)->orderBy('id', 'DESC')->count(),] + $array2);
    }

    public function changeOrderStatus($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $order = Purchase_order::where('driver_id', $user->id)->where('id', $id)
            ->withCount('purchase_item as products_count')->with('provider', 'orderStatus')
            ->whereIn('status', [3, 6])
            ->first();

        if (!$order) {
            return response()->json(['messages' => 'لا يوجد طلب بهذا الرقم'], 400);
        }

        if ($order->status == 3) {
            $order->update(['status' => 4]);
            Purchase_item::where('order_id', $order->id)->update(['status' => 4]);
            $order->refresh();
            $order->DriverOnWayNotification();
            return response()->json(['message' => 'الطلب قيد الاستلام', 'order' => PurchaseOrderResource::make($order)]);
        }

        if ($order->status == 6) {
            $order->update(['status' => 8, 'warehouse_date' => now()]);
            Purchase_item::where('order_id', $order->id)->update(['status' => 8]);
            $order->refresh();
            $order->OrderArrivedWayNotification();
            return response()->json(['message' => 'الطلب جاهز للتخزين', 'order' => PurchaseOrderResource::make($order)]);
        }

        return response()->json(['message' => 'لا تستطيع تغير حالة الطلب']);
    }

    public function orderDetails($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $order = Purchase_order::where('driver_id', $user->id)->where('id', $id)->whereIn('status', [3, 4, 6, 8])
            ->withCount('purchase_item as products_count')->with('provider', 'orderStatus')->first();

        if (!$order) {
            return response()->json(['messages' => 'لا يوجد طلب بهذا الرقم'], 400);
        }

        $items = Purchase_item::with('provider', 'product', 'product.measurement')->where('order_id', $order->id)->get();

        $user = User::where('id', $order->provider_id)->with('state', 'region')->first();

        return [
            'order' => PurchaseOrderResource::make($order),
            'address' => SupplierAddressResource::make($user),
            'cart_items' => PurchaseItemResource::collection($items),
        ];
    }

    public function receiveOrder($id, Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $order = Purchase_order::where('driver_id', $user->id)->where('id', $id)
            ->withCount('purchase_item as products_count')->with('provider', 'orderStatus')
            ->where('status', 4)
            ->first();

        if (!$order) {
            return response()->json(['messages' => 'لا يوجد طلب بهذا الرقم'], 400);
        }

        foreach (json_decode(request('items')) as $key => $item) {
            $purchase_item = Purchase_item::find($item->id);
            if ($purchase_item) {
                $purchase_item->update(['driver_quantity' => $item->quantity]);
            }
        }
        $order->update(['status' => 6]);
        Purchase_item::where('order_id', $order->id)->update(['status' => 6]);
        $order->refresh();
        $order->OrderOnWayNotification();
        return response()->json(['message' => 'الطلب قيد التوصيل', 'order' => PurchaseOrderResource::make($order)]);
    }

    public function refuseReceiveOrder($id)
    {
        $validator = Validator::make(request()->all(), [
            'reason_of_refuse' => 'required|string',
        ], [
            'reason_of_refuse.required' => 'يجب تحديد السبب للرفض',
        ]);

        if ($validator->fails()) {
            return response()->json(['messages' => $validator->errors()->first()], 400);
        }

        $user = JWTAuth::parseToken()->authenticate();
        $order = Purchase_order::where('driver_id', $user->id)->where('id', $id)
            ->withCount('purchase_item as products_count')->with('provider', 'orderStatus')
            ->where('status', 4)
            ->first();
        if (!$order) {
            return response()->json(['messages' => 'لا يوجد طلب بهذا الرقم'], 400);
        }
        $order->update(['status' => 5, 'reason_of_refuse' => request('reason_of_refuse'), 'refuse_date' => now(), 'refused' => 1]);
        Purchase_item::where('order_id', $order->id)->update(['status' => 5]);
        $order->refresh();
        $order->OrderRefusedNotification();
        return response()->json(['message' => 'الطلب تم رفضه', 'order' => PurchaseOrderResource::make($order)]);
    }
}
