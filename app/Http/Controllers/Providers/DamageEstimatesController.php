<?php

namespace App\Http\Controllers\Providers;

use App\Models\BankTransfer;
use App\Models\DamageEstimate;
use App\Models\DamageOffer;
use App\Models\DeviceTokens;
use App\Models\Hall;
use App\Models\Messages;
use App\Models\Notification;
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


class DamageEstimatesController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
if((\auth()->user()->user_type_id==3 && \auth()->user()->accept_estimate==0) || (\auth()->user()->user_type_id==4 &&\auth()->user()->provider &&\auth()->user()->provider->accept_estimate==0)){
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
        $this->check_provider_settings(468);
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;

        $status = $request->status;
        $objects = DamageEstimate::select('*')
            ->selectRaw('(SELECT count(*) FROM damage_offers WHERE damage_offers.order_id=damage_estimates.id and damage_offers.provider_id =' . $provider_id . ') as is_replied')
            ->where('payment_method','<>', 0)
            ->where('published',1)

            ->where(function ($query1) use($status,$provider_id){
               if ($status == 'replied') {
                   $query1->whereIn('id', function ($query) use ($status,$provider_id) {
                       $query->select('order_id')
                           ->from(with(new DamageOffer())->getTable())
                           ->where('provider_id', $provider_id);
                   });
               }
               if ($status == 'new') {
                   $query1->whereNotIn('id', function ($query) use ($status,$provider_id) {
                       $query->select('order_id')
                           ->from(with(new DamageOffer())->getTable())
                           ->where('provider_id', $provider_id);
                   });
               }
               if ($status == 'accepted') {
                   $query1->where('shop_id',$provider_id);
               }
               })->orderBy('created_at','DESC')

            ->get();
        return view('providers.damage_estimates.all', ['objects' => $objects]);
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->check_provider_settings(469);
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;

        $object = DamageEstimate::select('*')->where('id', $id)
            ->selectRaw('(SELECT count(*) FROM damage_offers WHERE damage_offers.order_id=damage_estimates.id and damage_offers.provider_id =' . $provider_id . ') as is_replied')
            ->where('published',1)
            ->first();
        if(!$object)return abort(404);
        $my_reply=DamageOffer::where('order_id',$object->id)->where('provider_id',$provider_id)->first();

        return view('providers.damage_estimates.order_details', ['object' => $object,'my_reply'=>$my_reply]);
    }

    public function addDamageOffer($id, Request $request)
    {
        $this->check_provider_settings(469);
        $this->validate($request, [
            'cost_from' => 'required',
            'cost_to' => 'required',
            'time' => 'required',
            'description' => 'required|max:1500',
        ]);
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;
        $provider=User::find($provider_id);

        $order = DamageEstimate::select('*')->where('id', $id)
            ->where('published',1)
            ->selectRaw('(SELECT count(*) FROM damage_offers WHERE damage_offers.order_id=damage_estimates.id and damage_offers.provider_id =' . $provider_id . ') as is_replied')
            ->first();
        if (!$order) return abort(404);
        if ($order->status != 0) {
            return redirect()->back()->with('error', 'هذا الطلب لم يعد متاح');
        }
        if ($order->is_replied > 0) {
            return redirect()->back()->with('error', 'تم الرد على هذا الطلب من قبل');

        }
        $object = new DamageOffer();
        $object->provider_id = $provider_id;
        $object->cost_from = $request->cost_from;
        $object->cost_to = $request->cost_to;

        $object->order_id = $order->id;
        $object->description = $request->description;
        $object->time = $request->time;
        $object->save();
        $user_id=$order->user_id;
        $notify = new Notification();
        $notify->sender_id = $provider;
        $notify->reciever_id = $user_id;
        $notify->type = 6;
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
            'key'=>$object->id,
            'notification_data' => '{ads_id:'.$object->id.'}'
        ]
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $tokens  = DeviceTokens::whereIn('user_id', function ($query) use($user_id){
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
        $ads = Orders::find($id);
        if ($ads != false) {
            $ads = Orders::find($ads->id);
            foreach (OrdersPhotos::where('ads_id', $id)->get() as $photo) {
                $old_file = 'uploads/' . $photo->photo;
                if (is_file($old_file)) unlink($old_file);
                $photo->delete();
            }
            $ads->delete();
        }
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
