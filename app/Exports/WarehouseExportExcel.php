<?php

namespace App\Exports;

use App\Models\CartItem;
use App\Models\Orders;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class WarehouseExportExcel implements FromView
{
    public function view(): View
    {
        $carts = CartItem::with('product')->whereHas('order', function ($query) {
            $query->whereIn('status', [2, 3, 4, 6, 7])->where('return_to_wallet', 0)->where('stop', 0)
                ->when(request()->order_id, function ($query) {
                    $query->where('id', request()->order_id);
                })
                ->when(request('from') && request('to'), function ($query) {
                    $query->whereBetween('warehouse_date', [request('from') . ' ' . '00:00:00' ?: now()->subYears(4) . ' ' . '23:59:59', request('to') . ' ' . '23:59:59' ?: now() . ' ' . '23:59:59']);
                })
                ->when(in_array(request()->status, [2, 3]), function ($query) {
                    $query->where('status', request()->status);
                })
                ->when(request()->status == 4, function ($query) {
                    $query->whereIn('status', [4, 6, 7]);
                })
                ->where('financial_date', '!=', null);
        })->groupBy('item_id')
            ->join('products', 'products.id', 'cart_items.item_id')
            // ->join('supplier_data', 'supplier_data.user_id', 'cart_items.shop_id')
            ->select('item_id as id', 'products.title')->selectRaw('sum(cart_items.quantity) as new_quantity')->get();

        return view('admin.orders.export-excel', [
            'carts' => $carts,
        ]);
    }
}
