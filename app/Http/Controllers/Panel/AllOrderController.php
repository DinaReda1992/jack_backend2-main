<?php

namespace App\Http\Controllers\Panel;

use App\Models\Orders;
use App\Models\Settings;
use Illuminate\Http\Request;
use App\Models\OrderShipments;
use App\Http\Controllers\Controller;

class AllOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }

    public function index()
    {
        $objects = Orders::whereIn('status', [2, 3, 4, 6, 7])
            ->when(request()->order_id, function ($query) {
                $query->where('id', request()->order_id);
            })
            // ->when(request('from') && request('to'), function ($query) {
            //     $query->whereBetween('warehouse_date', [request('from') . ' ' . '00:00:00' ?: now()->subYears(4) . ' ' . '23:59:59', request('to') . ' ' . '23:59:59' ?: now() . ' ' . '23:59:59']);
            // })
            ->when(in_array(request()->status, [2, 3, 4, 6, 7]), function ($query) {
                $query->where('status', request()->status);
            })
            ->where('financial_date', '!=', null)
            ->orderBy('financial_date', 'desc')
            ->paginate(50);
        return view('admin.orders.all-orders', ['objects' => $objects]);
    }

    public function show($id)
    {
        OrderShipments::where('order_id', $id)->doesntHave('cart_items')->delete();
        $taxs = Settings::find(38)->value;
        $object = Orders::select(
            'orders.*',
            \Illuminate\Support\Facades\DB::raw('sum(cart_items.price * cart_items.quantity) as subtotal'),
            \Illuminate\Support\Facades\DB::raw('((sum(cart_items.price * cart_items.quantity)+orders.delivery_price)*' . $taxs . '/100) as order_vat')
        )
            ->join('cart_items', 'cart_items.order_id', 'orders.id')
            ->where('orders.id', $id)->with('shipments.cart_items', 'address', 'user')->where('orders.status', '>=', 2)->first();
        return view('admin.orders.all-order-show', compact('object'));
    }
}
