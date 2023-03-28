<?php

namespace App\Http\Controllers\Providers;

use App\Models\BankTransfer;
use App\Models\Cards;
use App\Models\CardsCategories;
use App\Models\Notification;
use App\Models\Settings;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Orders;
use App\Models\OrdersOrders;
use App\Models\OrdersPhotos;


use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;


class CardsController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            $this->check_settings(217);
            return $next($request);
        });

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function current_prices(){
        return view('providers.cards.current_prices', ['objects' => Cards::all() ]);
    }

    public function current_balance()
    {
        $token = Settings::find(15)->value;


        $curl = curl_init();
        $data2 = [

        ];
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.rasseed.com/api/method/balance",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data2),
            CURLOPT_HTTPHEADER => array(
                "Authorization:$token"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $resp = json_decode($response);
            $your_money_is = $resp->message->cash;
        }

        return view('providers.cards.current_balance', ['your_money_is' => $your_money_is ]);
    }
    public function cards_categories()
    {
        $token = Settings::find(15)->value;


        $curl = curl_init();
        $data2 = [

        ];
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.rasseed.com/api/method/cards",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data2),
            CURLOPT_HTTPHEADER => array(
                "Authorization:$token"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $resp = json_decode($response);
            foreach ($resp->message as $key => $msg){
               if(!CardsCategories::where('name_en',$key)->first()){
                   $card_category = new CardsCategories();
                   $card_category -> name_en = $key;
                   $card_category -> save();
               }
            }
        }

        return view('providers.cards.all-categories', ['objects' => CardsCategories::all() ]);
    }
    public function cards($id=0)
    {
        $token = Settings::find(15)->value;

        $category = CardsCategories::find($id);
        if(!$category){
            return redirect()->back()->with('error','عفوا هذا العنوان غير صحيح');
        }

        $curl = curl_init();
        $data2 = [

        ];
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.rasseed.com/api/method/cards",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data2),
            CURLOPT_HTTPHEADER => array(
                "Authorization:$token"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $resp = json_decode($response);
            foreach ($resp->message->{$category->name_en} as $card){
                if(!Cards::where('card_id',$card->cardID)->first()){
                    $card_category = new Cards();
                    $card_category -> name_en = $card->name;
                    $card_category -> card_category_id = $id;
                    $card_category -> card_id = $card->cardID;
                    $card_category -> name = $card->category_ar;
                    $card_category -> details = $card->details;
                    $card_category -> save();
                }
            }
        }

        return view('providers.cards.cards-category', ['objects' => Cards::where('card_category_id',$category->id)->get() ,'category'=>$category ]);
    }


    public function filter_new_order(Request $request){
        $id = $request->order_id;
        $orders = Orders::where('id',$id)->where('status',0)->orderBy('id', 'DESC')->paginate(50);
        return view('providers.orders.new_orders', ['objects' => $orders ,'order_id'=>$id]);
    }

    public function save_price($card_id=0,$price=0)
    {
        $cat = Cards::find($card_id);
        $cat ->price = $price;
        $cat->save();
    }


    public function filter_approved_order(Request $request){
        $id = $request->order_id;
        $orders = Orders::where('id',$id)->where('status',2)->orderBy('id', 'DESC')->paginate(50);
        return view('providers.orders.approved_orders', ['objects' => $orders ,'order_id'=>$id]);
    }

    public function filter_cancelled_order(Request $request){
        $id = $request->order_id;
        $orders = Orders::where('id',$id)->where('status',3)->orderBy('id', 'DESC')->paginate(50);
        return view('providers.orders.cancelled_orders', ['objects' => $orders ,'order_id'=>$id]);
    }

    public function filter_on_progress_order(Request $request){
        $id = $request->order_id;
        $orders = Orders::where('id',$id)->where('status',1)->orderBy('id', 'DESC')->paginate(50);
        return view('providers.orders.on_progress_orders', ['objects' => $orders ,'order_id'=>$id]);
    }

    public function filter_all(Request $request){
        $id = $request->order_id;
        $orders = Orders::where('id',$id)->orderBy('id', 'DESC')->paginate(50);
        return view('providers.orders.all', ['objects' => $orders ,'order_id'=>$id]);
    }

    public function bank_transfer_order()
    {
        return view('providers.banks.new_transfer_order', ['objects' => BankTransfer::where('type', "order")->orderBy('id', 'DESC')->get()]);
    }

    public function bank_transfer_member()
    {
        return view('providers.banks.new_transfer_member', ['objects' => BankTransfer::where('type', "membership")->orderBy('id', 'DESC')->get()]);
    }

    public function new_orders()
    {
        return view('providers.orders.new_orders', ['objects' => Orders::where('status', 0)->orderBy('id', 'DESC')->paginate(50)]);
    }

    public function cancelled_orders()
    {
        return view('providers.orders.cancelled_orders', ['objects' => Orders::where('status', 3)->orderBy('id', 'DESC')->paginate(50)]);
    }

    public function approved_orders()
    {
        return view('providers.orders.approved_orders', ['objects' => Orders::where('status', 2)->orderBy('id', 'DESC')->paginate(50)]);
    }

    public function payed_orders()
    {
        return view('providers.orders.payed_orders', ['objects' => Orders::where('status', 2)->orderBy('id', 'DESC')->get()]);
    }

    public function on_progress_orders()
    {
        return view('providers.orders.on_progress_orders', ['objects' => Orders::where('status', 1)->orderBy('id', 'DESC')->paginate(50)]);
    }

    public function done_orders()
    {
        return view('providers.orders.done_orders', ['objects' => Orders::where('status', 4)->orderBy('id', 'DESC')->get()]);
    }


    public function normal_ads()
    {
        return view('providers.orders.normal', ['objects' => Orders::where('adv', 0)->get()]);
    }


    public function adv_ads($id = 0)
    {
        $ads = Orders::find($id);
        if (!$ads) {
            return redirect()->back()->with('error', 'لا يوجد اعلان بهذا العنوان');
        }
        if ($ads->adv == 0) {
            $ads->adv = 1;
            $ads->save();
            return redirect()->back()->with('success', 'تم تثبيت الاعلان في الرئيسية بنجاح .');
        } else {
            $ads->adv = 0;
            $ads->save();
            return redirect()->back()->with('success', 'تم ازالة التثبيت من الرئيسية بنجاح .');
        }

    }


    public function approve_order($id = 0, Request $request)
    {
        $order = Orders::find($id);
        $order->status = 1;

//        if ($order->getService->elegant_service == 1) {
//            $order->price = $request->price;
//            $order->currency_id = $request->currency_id;
//        }

        $order->save();

//        $order = Orders::where('id', $order->id)
//            ->with(['getService' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getYear' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getBrand' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getModel' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getStatus' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getCurrency' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->first();
//
//        $notification = new Notification();
//        $notification->sender_id = 1;
//        $notification->reciever_id = $order->user_id;
//        $notification->order_id = $order->id;
////        $notification->url = "/admin/orders/".$order->id;
//        $notification->message = "تم الموافقة على طلبك رقم " . $order->id;
//        $notification->message_en = "Your order " . $order->id . " was approved ";
//
//        $notification->type = 1;
//        $notification->save();
//
//
//        $order->{"created_time"} = Carbon::parse($order->created_at)->diffForHumans();
//        $order->{"message"} = "تم الموافقة على طلبك رقم " . $order->id . " ";
//
//        $optionBuilder = new OptionsBuilder();
//        $optionBuilder->setTimeToLive(60 * 20);
//
//        $notificationBuilder = new PayloadNotificationBuilder();
//        $notificationBuilder->setBody($order->message)
//            ->setSound('default');
//
//        $dataBuilder = new PayloadDataBuilder();
//        $dataBuilder->addData(['data' => $order, 'apps' => ['badge' => Notification::where('reciever_id', $order->user_id)->where('status', 0)->count()], 'type' => 1]);
//
//        $option = $optionBuilder->build();
//        $notification = $notificationBuilder->build();
//        $data = $dataBuilder->build();
//
//        $token = @$order->getUser->device_token;
//        if (@$order->getUser->device_type == "ios") {
//            $downstreamResponse = FCM::sendTo($token, null, $notification, $data);
//            @$downstreamResponse->numberSuccess();
//            @$downstreamResponse->numberFailure();
//            @$downstreamResponse->numberModification();
//        } elseif (@$order->getUser->device_type) {
//            $downstreamResponse = FCM::sendTo($token, null, null, $data);
//            @$downstreamResponse->numberSuccess();
//            @$downstreamResponse->numberFailure();
//            @$downstreamResponse->numberModification();
//
//        }
////        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
////        @$downstreamResponse = FCM::sendTo($token, null, null, $data);


        return redirect()->back()->with('success', 'تمت الموافقة على الطلب بنجاح .');
    }

    public function on_progress_order($id = 0)
    {
        $order = Orders::find($id);
        $order->status = 3;
        $order->save();

        $order = Orders::where('id', $order->id)
            ->with(['getService' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getYear' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getBrand' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getModel' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getStatus' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getCurrency' => function ($query) {
                $query->select('id', 'name');
            }])
            ->first();
        $order->{"created_time"} = Carbon::parse($order->created_at)->diffForHumans();
        $order->{"message"} = "يتم الان العمل على تقرير طلبك رقم " . $order->id . " ";

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setBody($order->message)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' => $order, 'apps' => ['badge' => Notification::where('reciever_id', $order->user_id)->where('status', 0)->count()], 'type' => 1]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = @$order->getUser->device_token;
        if (@$order->getUser->device_type == "ios") {
            $downstreamResponse = FCM::sendTo($token, null, $notification, $data);
            @$downstreamResponse->numberSuccess();
            @$downstreamResponse->numberFailure();
            @$downstreamResponse->numberModification();

        } elseif (@$order->getUser->device_type) {
            $downstreamResponse = FCM::sendTo($token, null, null, $data);
            @$downstreamResponse->numberSuccess();
            @$downstreamResponse->numberFailure();
            @$downstreamResponse->numberModification();

        }

//        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
//        $downstreamResponse = FCM::sendTo($token, null, null, $data);

        $notification = new Notification();
        $notification->sender_id = 1;
        $notification->reciever_id = $order->user_id;
        $notification->order_id = $order->id;
//        $notification->url = "/admin/orders/".$order->id;
        $notification->message = "جاري العمل طلبك رقم " . $order->id;
        $notification->message_en = "Your order " . $order->id . " is in progress ";

        $notification->type = 3;
        $notification->save();


        return redirect()->back()->with('success', 'تمت تحويل الطلب الى جاري العمل عليه لاصدار التقرير بنجاح .');
    }

    public function finish_order($id = 0)
    {
        $order = Orders::find($id);
        $order->status = 4;
        $order->save();

        $order = Orders::where('id', $order->id)
            ->with(['getService' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getYear' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getBrand' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getModel' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getStatus' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getCurrency' => function ($query) {
                $query->select('id', 'name');
            }])
            ->first();
        $order->{"created_time"} = Carbon::parse($order->created_at)->diffForHumans();
        $order->{"message"} = "تم انهاء طلبك رقم " . $order->id . " ";

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setBody($order->message)
            ->setSound('default');
        $notification = $notificationBuilder->build();

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' => $order, 'apps' => ['badge' => Notification::where('reciever_id', $order->user_id)->where('status', 0)->count()], 'type' => 1]);

        $option = $optionBuilder->build();
        $data = $dataBuilder->build();

        $token = @$order->getUser->device_token;
        if (@$order->getUser->device_type == "ios") {
            $downstreamResponse = FCM::sendTo($token, null, $notification, $data);
            @$downstreamResponse->numberSuccess();
            @$downstreamResponse->numberFailure();
            @$downstreamResponse->numberModification();

        } elseif (@$order->getUser->device_type) {
            $downstreamResponse = FCM::sendTo($token, null, null, $data);
            @$downstreamResponse->numberSuccess();
            @$downstreamResponse->numberFailure();
            @$downstreamResponse->numberModification();

        }

//        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);


        $notification = new Notification();
        $notification->sender_id = 1;
        $notification->reciever_id = $order->user_id;
        $notification->order_id = $order->id;
//        $notification->url = "/admin/orders/".$order->id;
        $notification->message = "تم الانتهاء من  طلبك رقم " . $order->id;
        $notification->message_en = "Your order " . $order->id . " was finished ";

        $notification->type = 4;
        $notification->save();


        return redirect()->back()->with('success', 'تم انهاء الطلب بنجاح .');
    }


    public function approve_payment($id = 0)
    {
        $order = Orders::find($id);
        $order->status = 2;
        $order->save();

        $order = Orders::where('id', $order->id)
            ->with(['getService' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getYear' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getBrand' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getModel' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getStatus' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['getCurrency' => function ($query) {
                $query->select('id', 'name');
            }])
            ->first();
        $order->{"created_time"} = Carbon::parse($order->created_at)->diffForHumans();
        $order->{"message"} = "تم التأكيد الدفع على طلبك رقم " . $order->id . " ";
        $order->{"message_en"} = "Your order " . $order->id . " was payed successfully . ";;

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setBody($order->message)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' => $order, 'apps' => ['badge' => Notification::where('reciever_id', $order->user_id)->where('status', 0)->count()], 'type' => 1]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = @$order->getUser->device_token;

        if (@$order->getUser->device_type == "ios") {
            $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
            @$downstreamResponse->numberSuccess();
            @$downstreamResponse->numberFailure();
            @$downstreamResponse->numberModification();

        } else {
            $downstreamResponse = FCM::sendTo($token, null, null, $data);
            @$downstreamResponse->numberSuccess();
            @$downstreamResponse->numberFailure();
            @$downstreamResponse->numberModification();

        }

        $notification = new Notification();
        $notification->sender_id = 1;
        $notification->reciever_id = $order->user_id;
        $notification->order_id = $order->id;
//        $notification->url = "/admin/orders/".$order->id;
        $notification->message = "تم تأكيد الدفع على  طلبك رقم " . $order->id;
        $notification->message_en = "Your order " . $order->id . " was payed successfully . ";

        $notification->type = 2;
        $notification->save();


        return redirect()->back()->with('success', 'تمت تحويل الطلب الى مدفوع .');


    }

    public function cancel_order($id = 0, Request $request)
    {
        $order = Orders::find($id);
        $order->status = 2;
        $order->reason_of_cancel = $request->reason_of_cancel;
        $order->save();

//        $order = Orders::where('id', $order->id)
//            ->with(['getService' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getYear' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getBrand' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getModel' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getStatus' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->with(['getCurrency' => function ($query) {
//                $query->select('id', 'name');
//            }])
//            ->first();
//        $order->{"created_time"} = Carbon::parse($order->created_at)->diffForHumans();
//        $order->{"message"} = "تم الغاء طلبك رقم " . $order->id . " ";
//
//        $optionBuilder = new OptionsBuilder();
//        $optionBuilder->setTimeToLive(60 * 20);
//
//        $notificationBuilder = new PayloadNotificationBuilder();
//        $notificationBuilder->setBody($order->message)
//            ->setSound('default');
//
//        $dataBuilder = new PayloadDataBuilder();
//        $dataBuilder->addData(['data' => $order, 'apps' => ['badge' => Notification::where('reciever_id', $order->user_id)->where('status', 0)->count()], 'type' => 1]);
//
//        $option = $optionBuilder->build();
//        $notification = $notificationBuilder->build();
//        $data = $dataBuilder->build();
//
//        $token = @$order->getUser->device_token;
//
////        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
//        if (@$order->getUser->device_type == "ios") {
//            $downstreamResponse = FCM::sendTo($token, null, $notification, $data);
//            @$downstreamResponse->numberSuccess();
//            @$downstreamResponse->numberFailure();
//            @$downstreamResponse->numberModification();
//
//        } elseif (@$order->getUser->device_type) {
//            $downstreamResponse = FCM::sendTo($token, null, null, $data);
//            @$downstreamResponse->numberSuccess();
//            @$downstreamResponse->numberFailure();
//            @$downstreamResponse->numberModification();
//
//        }


        return redirect()->back()->with('success', 'تمت الغاء الطلب بنجاح .');


    }

    public function adv_slider($id = 0)
    {
        $ads = Orders::find($id);
        if (!$ads) {
            return redirect()->back()->with('error', 'لا يوجد اعلان بهذا العنوان');
        }
        if ($ads->adv_slider == 0) {
            $ads->adv_slider = 1;
            $ads->save();
            return redirect()->back()->with('success', 'تم تثبيت الاعلان في القسم بنجاح .');
        } else {
            $ads->adv_slider = 0;
            $ads->save();
            return redirect()->back()->with('success', 'تم ازالة التثبيت من القسم  بنجاح .');
        }

    }


    public function orders_adv()
    {
        return view('providers.orders.ask_orders', ['objects' => OrdersOrders::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.orders.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories|max:100|min:3',
        ]);
        $object = new User;
        $object->name = $request->name;
        $object->save();
        return redirect()->back()->with('success', 'تم اضافة العضو بنجاح .');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit_cards_categories($id)
    {
        return view('providers.cards.edit-cards-categories', ['object' => CardsCategories::find($id)]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
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

    public function edit_cards_categories_post($id=0,Request $request){
        $object = CardsCategories::find($id);
            $object->hidden = $request->hidden ?:0 ;


        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
        	$old_file = 'uploads/'.$object->photo;
        	if(is_file($old_file))	unlink($old_file);
        	$fileName = 'cards-categories-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
        	$destinationPath = 'uploads';
        	$request->file('photo')->move($destinationPath, $fileName);
        	$object->photo=$fileName;
        }


        $object->save();
        return redirect()->back()->with('success', 'تم تعديل الكارت بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
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
