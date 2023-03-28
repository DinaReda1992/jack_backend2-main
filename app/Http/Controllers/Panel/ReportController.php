<?php

namespace App\Http\Controllers\Panel;

use Excel;
use App\Models\User;
use App\Models\Orders;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Regions;

class ReportController extends Controller
{
    public function __construct()
    {
        // \Carbon\Carbon::setLocale('ar');
        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }


    public function index(Request $request)
    {
        $order = new Orders();

        $data = $order->newOrders() + $order->preparingOrders() + $order->deliveringOrders() + $order->pendingOrders() +
            $order->completedOrders() + $order->canceledOrders() + $order->readyToShipOrders() + $order->shippedOrders() + $order->allOrders();
        $users = User::where('user_type_id', 5)->where(['is_archived' => 0, 'approved' => 1, 'block' => 0])->select('id', 'username')->get();
        $employees = User::where('user_type_id', 2)->where(['is_archived' => 0, 'block' => 0])->select('id', 'username')->get();
        $suppliers = User::where('user_type_id',3)->where(['is_archived' => 0, 'block' => 0])->select('id', 'username')->get();
        $regions = Regions::select('id', 'name')->get();
        ini_set('serialize_precision', -1);
        return view('admin.report.index', compact('data', 'users', 'employees', 'regions','suppliers'));
    }

    public function excel(Request $request)
    {
        return  \Excel::download(new \App\Exports\ReportOrder(), now()->format('Y-m-d') . "-orders.xlsx");
    }
}
