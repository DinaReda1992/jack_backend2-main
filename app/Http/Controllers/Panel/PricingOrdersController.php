<?php

namespace App\Http\Controllers\Panel;

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
            $this->check_settings(489);

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
        $provider_id = 235;

        $status = $request->status;
        $objects = PricingOrder::select('*')
            ->where(function ($query1) use ($status, $provider_id) {
                if ($status == 'unpublished') {
                    $query1->where('published',0);
                }

                if ($status == 'new') {
                    $query1->where('status', 0);
                }elseif ($status == 'closed'){
                    $query1->where('status','!=', 0);

                }
            })->where('payment_method','<>',0)->orderBy('id','DESC')
            ->get();
        return view('admin.pricing_orders.all', ['objects' => $objects]);
    }

public function getPartOrder($id=0){
    $this->check_provider_settings(471);
    $provider_id = 235;
    $part=PricingOrderPart::find($id);
    if(!$part)abort(404);

    $object = PricingOrder::select('*')->where('id', $part->order_id)
        ->where('payment_method','<>',0)->first();

    if(!$object)return abort(404);

    return view('admin.pricing_orders.order_details', ['object' => $object]);

}
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $provider_id = 235;
        $object = PricingOrder::select('*')->where('id', $id)
            ->where('payment_method','<>',0)->first();

        if(!$object)return abort(404);
$adminUser=User::find($provider_id);
        return view('admin.pricing_orders.order_details', ['object' => $object,'adminUser'=>$adminUser]);
    }

    public function addPricingOffer($id, Request $request)
    {
        $order=PricingOrder::find($id);
        if(!$order) return abort(404);
        if($order->status!=0){
            return redirect()->back()->with('error','لم يعد الطلب متاح لاستقبال عروض');
        }
        $provider_id = 235;
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
    public function publishPricingOrder($id=0){
        $object=PricingOrder::find($id);
        if(!$object) return abort(404);
        $object->published=1;
        $object->save();
        $user=$object->user;
        $shops = User::where('accept_pricing', 1)->where('block', 0)->get();
        $notify_message = 'قام المستخدم ' . $user->username . ' باضافة طلب جديد لتسعير منتجات';
        foreach ($shops as $shop) {
            $notify = new Notification();
            $notify->sender_id = $user->id;
            $notify->reciever_id = $shop->id;
            $notify->type = 3;
            $notify->url = '/provider-panel/pricing-orders/' . $object->id;
            $notify->message = $notify_message;
            $notify->message_en = 'new parts pricing order by ' . $user->username;
            $notify->ads_id = $object->id;
            $notify->save();

        }
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $optionBuilder->setContentAvailable(true);

        $notification_title = "طلب تسعير جديد";

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notify_message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');


        $dataBuilder = new PayloadDataBuilder();

        $dataBuilder->addData(['data' => [
            'notification_type' => 3,
            'notification_title' => $notification_title,
            'notification_message' => $notify_message,
            'key' => $object->id,
            'notification_data' => '{ads_id:' . $object->id . '}'
        ]
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens = DeviceTokens::whereIn('user_id', function ($query) {
            $query->select('id')
                ->from(with(new User())->getTable())
                ->where('accept_pricing', 1)
                ->where('block', 0)
                ->where('notification', 1);
        })->pluck('device_token')->toArray();
        if (count($tokens)) {

            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }

        return redirect()->back()->with('success','تم نشر الطلب للتجار بنجاح');
    }
    public function unPublishPricingOrder($id=0){
        $object=PricingOrder::find($id);
        if(!$object) return abort(404);
        $object->published=0;
        $object->save();
        return redirect()->back()->with('success','تم الغاء نشر الطلب للتجار بنجاح');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.orders.add', ['object' => Orders::find($id)]);
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
