<?php

namespace App\Http\Controllers\Panel;

use App\Models\User;
use App\Models\Orders;
use App\Models\Settings;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\OrderShipments;
use App\Models\Purchase_order;
use App\Services\SendNotification;
use App\Helpers\SendFcmNotification;
use App\Http\Controllers\Controller;
use App\Http\Resources\MyOrdersResources;

class ShipmentController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }

    public function clientOrder()
    {
        $objects = Orders::whereIn('status', [3, 4, 6, 7])->whereHas('cart_items')
            ->when(request('order_id'), function ($query) {
                $query->where('id', request('order_id'));
            })
            ->when(in_array(request('status'), [3, 4, 6, 7]), function ($query) {
                $query->where('status', request('status'));
            })
            ->where('financial_date', '!=', null)->orderBy('financial_date', 'desc')->paginate(10);
        return view('admin.shipment.clients-orders', ['objects' => $objects]);
    }

    public function clientOrderDetails($id)
    {
        OrderShipments::where('order_id', $id)->doesntHave('cart_items')->delete();
        $taxes = Settings::find(38)->value;
        $object = Orders::select(
            'orders.*',
            \Illuminate\Support\Facades\DB::raw('sum(cart_items.price * cart_items.quantity) as subtotal'),
            \Illuminate\Support\Facades\DB::raw('((sum(cart_items.price * cart_items.quantity)+orders.delivery_price)*' . $taxes . '/100) as order_vat')
        )
            ->join('cart_items', 'cart_items.order_id', 'orders.id')
            ->where('orders.id', $id)->with('shipments.cart_items', 'address', 'user')->where('orders.status', 3)->first();

        if (!$object) {
            return redirect('/admin-panel/client-orders')->with('error', 'لا يوجد طلب بهذا الرقم');
        }
        return view('admin.shipment.client-order-details', compact('object'));
    }

    public function supplierOrder()
    {
        $purchases_orders = Purchase_order::selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=3	) as shipping_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=4	) as in_shipment')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=5	) as canceled_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=6	) as progress_shipment')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=7 OR  purchase_orders.status=8	) as completed_orders')
            ->first();
        $status = request()->status;

        $objects = Purchase_order::whereHas('purchase_item')->whereHas('provider')->whereIn('status', [3, 4, 6, 7, 8])
            ->when(request('order_id'), function ($query) {
                $query->where('id', request('order_id'));
            })->where(function ($query) use ($status) {
                if ($status == 'shipping') {
                    $query->where('status', 3);
                }
                if ($status == 'in_shipment') {
                    $query->where('status', 4);
                }
                if ($status == 'progress_shipment') {
                    $query->where('status', 6);
                }
                if ($status == 'completed') {
                    $query->whereIn('status', [7, 8]);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate();
        return view('admin.shipment.supplier-orders', compact('objects', 'purchases_orders'));
    }

    public function supplierOrderDetails($id)
    {
        $object = Purchase_order::with('purchase_item')->whereId($id)->first();
        return view('admin.shipment.supplier-order-details', compact('object'));
    }

    public function selectDriverSupplier($id, Request $request)
    {
        $order = Purchase_order::where('id', $id)->first();
        $driver = User::where('id', $request->driver_id)->where('user_type_id', 6)->where('is_archived', 0)->first();
        if ($driver) {
            $order->driver_id = $driver->id;
            $order->save();
            $order->refresh();
            $order->newOrderDriverNotification();
            return redirect()->back()->with('success', 'تم تعيين السائق بنجاح');
        } else {
            return redirect()->back()->with('error', 'لا يوجد سائق');
        }
    }

    public function selectDriverClient($id, Request $request)
    {
        $order = Orders::where('id', $id)->first();
        $driver = User::where('id', $request->driver_id)->where('user_type_id', 6)->where('is_archived', 0)->first();
        if ($driver) {
            $order->driver_id = $driver->id;
            $order->delivery_date = $request->delivery_date ?: now();
            $order->save();
            $order->refresh();
            $order->newOrderDriverNotification();
            return redirect()->back()->with('success', 'تم تعيين السائق بنجاح');
        } else {
            return redirect()->back()->with('error', 'لا يوجد سائق');
        }
    }

    public function changeOrderStatus($id)
    {
        $order = Orders::where('id', $id)->first();
        if ($order->status == 3) {
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
            $notification_for_client->save();
        }

        if ($order->status == 3) {
            $select_status = app()->getLocale() == "ar" ? 'order_status.name as status_name' : 'order_status.name_en as status_name';
            $order->update(['status' => 4]);
            OrderShipments::where('order_id', $order->id)->update(['status' => 4]);
            $notification_for_client->save();
            $order = Orders::where('orders.id', $id)
                ->whereIn('orders.status', [3, 4])
                ->select('orders.id', 'orders.driver_id', 'orders.final_price', 'orders.status', $select_status, 'order_status.color', 'orders.created_at', 'users.id as user_id', 'users.username', 'users.photo', 'orders.marketed_date', 'orders.is_edit as has_second_order', 'orders.payment_method', 'payment_methods.name as payment_method_name')
                ->selectRaw('(CASE WHEN photo = ""  THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->selectRaw('(CONCAT ("' . url('/') . '/i/", orders.short_code)) as download_url')
                ->join('users', 'users.id', 'orders.user_id')
                ->join('payment_methods', 'orders.payment_method', 'payment_methods.id')
                ->leftJoin('order_status', 'order_status.id', 'orders.status')
                ->withCount('cart_items as products_count')->first();
            SendFcmNotification::send_fcm_notification($notification_title, $notification_message, $notification_for_client, new MyOrdersResources($order));
            SendNotification::order($order->id);
            return redirect()->back()->with('success', 'تم تغيير حالة الطلب بنجاح');
        }
    }
}
