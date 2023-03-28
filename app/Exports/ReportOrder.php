<?php

namespace App\Exports;

use App\Models\Orders;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReportOrder implements FromView
{
   
    public function view(): View
    {
        $order = new Orders();
        $data = $order->newOrders() + $order->preparingOrders() + $order->deliveringOrders() + $order->pendingOrders() +
            $order->completedOrders() + $order->canceledOrders() + $order->readyToShipOrders() + $order->shippedOrders()+ $order->allOrders();

        return view('admin.report.excel', ['data' => $data]);
    }
}
