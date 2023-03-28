<?php

namespace App\Http\Controllers\Providers;

use App\Models\BankTransfer;
use App\Models\DamageEstimate;
use App\Models\DamageOffer;
use App\Models\DeviceTokens;
use App\Models\Hall;
use App\Models\Messages;
use App\Models\Notification;
use App\Models\PricingOffer;
use App\Models\PricingOrder;
use App\Models\PricingOrderPart;
use App\Models\Reservations;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Orders;
use App\Models\OrdersOrders;
use App\Models\OrdersPhotos;


use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;


class PricingOrdersController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            if (\auth()->user()->provider && \auth()->user()->provider->accept_pricing == 0) {
                die('<h1 align="center">عفوا غير مسموح لك بالدخول لهذه الصلاحية  </h1>');
            }
            return $next($request);
        });

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->check_provider_settings(470);
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;

        $status = $request->status;
        $objects = PricingOrder::select('*')
            ->where(function ($query1) use ($status, $provider_id) {
                if ($status == 'new') {
                    $query1->where('status', 0);
                }elseif ($status == 'closed'){
                    $query1->where('status','!=', 0);

                }
            })->where('payment_method','<>',0)->orderBy('id','DESC')
            ->get();
        return view('providers.pricing_orders.all', ['objects' => $objects]);
    }

public function getPartOrder($id=0){
    $this->check_provider_settings(471);
    $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;
    $part=PricingOrderPart::find($id);
    if(!$part)abort(404);

    $object = PricingOrder::select('*')->where('id', $part->order_id)
        ->where('payment_method','<>',0)->first();

    if(!$object)return abort(404);

    return view('providers.pricing_orders.order_details', ['object' => $object]);

}
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->check_provider_settings(471);
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;
        $object = PricingOrder::select('*')->where('id', $id)
            ->where('payment_method','<>',0)->first();

        if(!$object)return abort(404);

        return view('providers.pricing_orders.order_details', ['object' => $object]);
    }

    public function addPricingOffer($id, Request $request)
    {
        $this->check_provider_settings(471);
        $order=PricingOrder::find($id);
        if(!$order) return abort(404);
        if($order->status!=0){
            return redirect()->back()->with('error','لم يعد الطلب متاح لاستقبال عروض');
        }
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;
        $provider=User::find($provider_id);

        if ($request->part_id) {
            foreach ($request->part_id as $key => $value) {
//                $is_offerd=PricingOffer::where('provider_id',$provider_id)->where('part_id',$request->part_id)->get();
//                if($is_offerd->count()){
//                    return redirect()->back()->with('error','تم اضافة العرض على هذه القطعة من قبل');
//                }

                if($request->manufacture_type[$key] &&$request->available_quantity[$key]&&$request->prepare_time[$key]&&$request->price[$key] ){
                    $object = new PricingOffer();
                    $object->part_id = $value;
                    $object->provider_id = $provider_id;
                    $object->manufacture_type=$request->manufacture_type[$key];
                    $object->available_quantity=$request->available_quantity[$key];
                    $object->prepare_time=$request->prepare_time[$key];
                    $object->price=$request->price[$key];
//                    $object->order_type=$request->order_type[$key];
                    $object->manufacture_country=$request->manufacture_country[$key]?:'';
//                    $object->brand=$request->brand[$key]?:'';
                    $object->save();
                }
            }
        }
$user_id=$order->user_id;
        $notify = new Notification();
        $notify->sender_id = $provider_id;
        $notify->reciever_id = $user_id;
        $notify->type = 5;
        $notify->message = 'قام المتجر ' . $provider->username . ' باضافة عرض جديد على طلبك';
        $notify->message_en = 'new offer on your order by ' . $provider->username;
        $notify->ads_id = $order->id;
        $notify->save();
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $optionBuilder->setContentAvailable(true);

        $notification_title="عرض جديد على طلبك";

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notify->message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');


        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData(['data' =>[
            'notification_type'=> $notify->type,
            'notification_title'=> $notification_title ,
            'notification_message'=> $notify->message ,
            'key'=>$order->id,
            'notification_data' => '{ads_id:'.$order->id.'}'
        ]
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens  = DeviceTokens::whereIn('user_id', function ($query)use($user_id) {
            $query->select('id')
                ->from(with(new User())->getTable())
                ->where('block',0)
                ->where('id',$user_id)
                ->where('notification',1);
        })->pluck('device_token')->toArray();
        if(count($tokens)) {

            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }

        return redirect()->back()->with('success', 'تم اضافة عرضك بنجاح');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('providers.orders.add', ['object' => Orders::find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $object = Orders::find($id);
        $this->validate($request, [
            'name' => 'required|max:100|min:3|unique:categories,name,' . $object->id . ',id',
        ]);

        $object->name = $request->name;
        $object->save();
        return redirect()->back()->with('success', 'تم تعديل العضو بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        $ads = Orders::find($id);
//        if ($ads != false) {
//            $ads = Orders::find($ads->id);
//            foreach (OrdersPhotos::where('ads_id', $id)->get() as $photo) {
//                $old_file = 'uploads/' . $photo->photo;
//                if (is_file($old_file)) unlink($old_file);
//                $photo->delete();
//            }
//            $ads->delete();
//        }
    }

    public function delete_order($id = 0)
    {
        $ads = OrdersOrders::find($id);
        if ($ads != false) {
            $ads = OrdersOrders::find($ads->id);
            $ads->delete();
        }
        return redirect()->back('success', 'تم حذف الطلب بنجاح');
    }
}
