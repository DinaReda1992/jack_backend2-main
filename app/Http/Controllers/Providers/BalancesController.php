<?php

namespace App\Http\Controllers\Providers;

use App\Models\Balance;
use App\Models\CartItem;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\RequestMoney;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
class BalancesController extends Controller
{
    public function __construct()
    {
        App::setLocale('ar');
        \Carbon\Carbon::setLocale(App::getLocale());

        $this->middleware(function ($request, $next) {
            $this->check_provider_settings(480);
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;

        $objects = Balance::where('user_id', $provider_id)->get();
        $all_sales = CartItem::join('order_shipments', 'order_shipments.id', 'cart_items.shipment_id')
            ->whereNotIn('order_shipments.status', [1, 5])
            ->where('cart_items.status', '<>', 5)
            ->where('cart_items.shop_id', $provider_id)
            ->sum('cart_items.price');
        $finished_sales = CartItem::join('order_shipments', 'order_shipments.id', 'cart_items.shipment_id')
            ->whereIn('order_shipments.status', [4])
            ->where('cart_items.status', '<>', 5)
            ->where('cart_items.shop_id', $provider_id)
            ->sum('cart_items.price');

        $current_balance = $objects->sum('price');
        $site_profits = $objects->sum('site_profits');
//        profit_rate
        return view('providers.balances.all', ['objects' => $objects, 'all_sales' => $all_sales, 'finished_sales' => $finished_sales,
            'current_balance' => $current_balance, 'site_profits' => $site_profits]);
    }

public function request_money(Request $request){
    $this->validate($request, [
        'value' => 'required|numeric',
    ]);
    $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;

    $objects = Balance::where('user_id', $provider_id)->get();



    $current_balance = $objects->sum('price');
    $site_profits = $objects->sum('site_profits');
    $right_balance=$current_balance-$site_profits;
if($request->value>$right_balance){
    return redirect()->back()->with('error','المبلغ المطلوب اكبر من المبلغ المستحق');
}
$object=new RequestMoney();
$object->price=$request->value;
$object->user_id=$provider_id;
$object->save();
return redirect()->back()->with('success','تم ارسال طلب سحب الرصيد بنجاح');
}

}