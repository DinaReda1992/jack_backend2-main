<?php

namespace App\Http\Controllers\Providers;

use App\Models\Balance;
use App\Models\BankTransfer;
use App\Models\CartItem;
use App\Models\Messages;
use App\Models\Notification;
use App\Models\OrderShipments;
use App\Models\Purchase_item;
use App\Models\Purchase_order;
use App\Models\Purchase_payment_method;
use App\Models\Settings;
use App\Models\Shipment as Shipmentt;
use App\Models\SmsaSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Orders;
use App\Models\OrdersOrders;
use App\Models\OrdersPhotos;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Octw\Aramex\Aramex;
use SmsaSDK\Smsa;
use \Alhoqbani\SmsaWebService\Models\Shipment;
use \Alhoqbani\SmsaWebService\Models\Customer;
use \Alhoqbani\SmsaWebService\Models\Shipper;


class SupplyController extends Controller
{
    public function __construct()
    {
//            App::setLocale('ar');
        \Carbon\Carbon::setLocale(App::getLocale());

        $this->middleware(function ($request, $next) {
            $this->check_provider_settings(475);

//            $this->check_settings(44);
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
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        $status=request()->status;
        $shop=User::select('users.*')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE purchase_orders.provider_id =' . auth()->id() . '	) as all_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE purchase_orders.provider_id =' . auth()->id() . ' AND purchase_orders.status=1	) as new_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE purchase_orders.provider_id =' . auth()->id() . ' AND purchase_orders.status=2	) as preparing_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE purchase_orders.provider_id =' . auth()->id() . ' AND purchase_orders.status=3	) as shipping_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE purchase_orders.provider_id =' . auth()->id() . ' AND purchase_orders.status=4	) as in_shipment')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE purchase_orders.provider_id =' . auth()->id() . ' AND purchase_orders.status=5	) as canceled_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE purchase_orders.provider_id =' . auth()->id() . ' AND purchase_orders.status=6	) as progress_shipment')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE purchase_orders.provider_id =' . auth()->id() . ' AND purchase_orders.status=7	) as completed_orders')
            ->where('users.id',$provider_id)
            ->first();
        $objects=Purchase_order::where('provider_id',$provider_id)
            ->where(function ($query) use ($status) {
                if (\request()->order_id) {
                    $query->where('id', request()->order_id);
                }
                if ($status == 'new') {
                    $query->where('status', 1);
                }
                if ($status == 'preparing') {
                    $query->where('status', 2);
                }
                if ($status == 'shipping') {
                    $query->where('status', 3);
                }
                if ($status == 'in_shipment') {
                    $query->where('status', 4);
                }
                if ($status == 'canceled') {
                    $query->where('status', 5);
                }
                if ($status == 'progress_shipment') {
                    $query->where('status', 6);
                }
                if ($status == 'completed_orders') {
                    $query->where('status', 7);
                }
            })->paginate();
        return view ('providers.supply.all',compact('objects','shop'));
    }
    public function purchases_items($id)
    {
        $object=User::where('id',$id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->where(['is_archived'=>0,'approved'=>1])
            ->whereHas('products', function (Builder $query) {
                $query->where(['stop'=>0,'is_archived'=>0])
                    ->whereRaw('quantity <= min_warehouse_quantity');
            })
            ->with(['products' => function ($query) {
                $query->select(
                    \DB::raw('(SELECT CAST(SUM(quantity) AS UNSIGNED) FROM purchase_items WHERE purchase_items.product_id = products.id) as progress_quantity'),
                    'products.id',
                    'products.provider_id',
                    'products.original_price',
                    'products.title','products.min_warehouse_quantity',
                    'products.quantity',
                    DB::raw('CAST((products.min_warehouse_quantity - products.quantity) AS UNSIGNED)  AS qty')
                );
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
//           ->with('products')
            ->withCount('products')
            ->first();
        $delivery_price=Settings::find(22)->value;
        $object->delivery_price=$delivery_price;
        $object->payment_methods=Purchase_payment_method::all();
        return view ('admin.orders.purchases-items',compact('object'));
    }


    public function change_order_status(Request $request,$id)
    {
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        $order = Purchase_order:: where(function ($query)use($provider_id) {
            $query->where('provider_id', $provider_id);
        })->where('id', $id)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'هذا الطلب لا يمكنك التعامل معه');
        }
        if ($order->status > 4) {
            return redirect()->back()->with('error', 'هذا الطلب لا يمكنك التعامل معه');
        }else{
            $todayDate=Carbon::today();
            $this->validate($request, [
                'provider_delivery_date' => $order->status==2?'required|date|after_or_equal:'.$todayDate:'',
                'provider_delivery_time' => $order->status==2?'required':'',
            ],[
                'provider_delivery_date.required'=>'يوم التسليم مطلوب',
                'provider_delivery_date.after_or_equal'=>' يوم التسليم يجب أن يكون تاريخاً لاحقاً لتاريخ اليوم',
                'provider_delivery_time.required'=>'موعد التسليم مطلوب',
            ]);




            if($order->status==4){
                $status= 2;
            }else{
                $status=1;
            }
            if($order->status==2){
                $order->provider_delivery_date=$request->provider_delivery_date;
                $order->provider_delivery_time=$request->provider_delivery_time;
            }
            $order->status = $order->status + $status;
            $order->save();

            $changeStatus = Purchase_order::select('purchase_orders.id','purchase_orders.status','purchase_orders.updated_at')
                ->where('purchase_orders.id', $order->id)
                ->where('purchase_orders.provider_id', $order->provider_id)
                ->where('purchase_orders.status', '<>', 5)
                ->update(['purchase_orders.status' => $order->status]);
            Purchase_item::where('order_id', $order->id)
                ->where('provider_id', $order->provider_id)
                ->update(['status' => $order->status]);

            return redirect()->back()->with('success', 'تم تغيير حالة الطلب بنجاح');
        }



    }

    public function send_order_shipment($id, Request $request)
    {
        $order = OrderShipments:: where(function ($query) {
            $query->where('shop_id', auth()->id())
                ->orWhere('shop_id', auth()->user()->main_provider);
        })->where('status', '1')->where('id', $id)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'هذا الطلب لا يمكنك التعامل معه');
        }
        $shipment_method = Shipmentt::where('id', auth()->user()->provider->shipment_id)->first();
        $shipment_address = $order->getOrder->address;

        return view('providers.orders.order_shipment', ['object' => $order, 'shipment' => $shipment_method, 'shipment_address' => $shipment_address]);

    }

    public function send_shipment($id, Request $request)
    {

        $order = OrderShipments:: where(function ($query) {
            $query->where('shop_id', auth()->id())
                ->orWhere('shop_id', auth()->user()->main_provider);
        })->where('status', '1')->where('id', $id)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'هذا الطلب لا يمكنك التعامل معه');
        }
        $shipment_method = Shipmentt::where('id', auth()->user()->provider->shipment_id)->first();
        $shipment_address = $order->getOrder->address;
        $user = $order->user;
        if ($shipment_method->id == 1) {
            return $this->sendSmsa($request, $order, $shipment_method);
        } elseif ($shipment_method->id == 2) {
            return $this->sendAramex($request, $order, $shipment_method);

        }
    }

    public function sendAramex(Request $request, $order, $shipment_method)
    {
        $data = Aramex::createPickup([
            'name' => 'MyName',
            'cell_phone' => '+123123123',
            'phone' => '+123123123',
            'email' => 'myEmail@gmail.com',
            'city' => 'Dammam',
            'country_code' => 'SA',
            'zip_code' => 10018,
            'line1' => 'The line1 Details',
            'line2' => 'The line2 Details',
            'line3' => 'The line2 Details',
            'pickup_date' => time() + 45000,
            'ready_time' => time() + 43000,
            'last_pickup_time' => time() + 45000,
            'closing_time' => time() + 45000,
            'status' => 'Ready',
            'pickup_location' => 'some location',
            'weight' => 123,
            'volume' => 1
        ]);
        // extracting GUID
        if (!$data->error)
            $guid = $data->pickupGUID;
        else {
            return redirect()->back()->with('error', 'حدث خطأ اثناء الارسال');
        }
        $anotherData = Aramex::createShipment([
            'shipper' => [
                'name' => 'Steve',
                'email' => 'email@users.companies',
                'phone' => '+123456789982',
                'cell_phone' => '+321654987789',
                'country_code' => 'SA',
                'city' => 'Dammam',
                'zip_code' => 10027,
                'line1' => 'Line1 Details',
                'line2' => 'Line2 Details',
                'line3' => 'Line3 Details',
            ],
            'consignee' => [
                'name' => 'Steve',
                'email' => 'email@users.companies',
                'phone' => '+123456789982',
                'cell_phone' => '+321654987789',
                'country_code' => 'SA',
                'city' => 'Dammam',
                'zip_code' => 10019,
                'line1' => 'Line1 Details',
                'line2' => 'Line2 Details',
                'line3' => 'Line3 Details',
            ],
            'shipping_date_time' => time() + 50000,
            'due_date' => time() + 60000,
            'comments' => 'No Comment',
            'pickup_location' => 'at reception',
            'pickup_guid' => $guid,
            'weight' => 1,
            'number_of_pieces' => 1,
            'description' => 'Goods Description, like Boxes of flowers',
        ]);

        $order->shipment_no = $guid;
        $order->status = 2;
        $order->shipment_company = $shipment_method->id;
        $order->shipment_attach = $anotherData->Shipments->ProcessedShipment->ShipmentLabel->LabelURL;
        $order->save();

        return redirect('/provider-panel/orders')->with('success', 'تم ارسال الشحنة بنجاح يمكنك تتبعها بهذا الرقم : ' . $guid);
    }

    public function sendSmsa(Request $request, $order, $shipment_method)
    {
        $shipment_address = $order->getOrder->address;
        $user = $order->user;

// send to smsa
        $passKey = SmsaSetting::first()->passkey;
        Smsa::key($passKey);   // Setting up the SMSA Key
//        Smsa::cancelShipment('290111909180',$passKey,'test api');
//return 1;
        Smsa::nullValues('');
        $cod = $order->getOrder->payment_method == 1 ? (@$order->cart_items->sum('price') + $order->delivery_price + $order->taxes) : 0;

        $shipmentData = [
            'refNo' => 'falcon_part' . time(), // shipment reference in your application
            'cName' => $user->username, // customer name
            'cntry' => 'SA', // shipment country
            'cCity' => @$shipment_address->state->smsa_name, // shipment city, try: Smsa::getRTLCities() to get the supported cities
            'cMobile' => $shipment_address->phone ?: $user->phone, // customer mobile
            'cAddr1' => $request->address, // customer address
            'cAddr2' => '', // customer address 2
            'shipType' => 'DLV', // shipment type
            'PCs' => $request->boxes ?: 1, // quantity of the shipped pieces
            'cEmail' => $user->email ?: '', // customer email
            'codAmt' => $cod, // payment amount if it's cash on delivery, 0 if not cash on delivery
            'weight' => $request->weight ?: 0, // pieces weight
            'itemDesc' => $request->item_description, // extra description will be printed
            'sName' => $request->shop_name,
            'sContact' => $request->user_name,
            'sAddr1' => $request->shipper_address,
            'sCity' => $request->shipper_state,
            'sPhone' => $request->phone

        ];

        $shipment = Smsa::addShip($shipmentData);

        $awbNumber = $shipment->getAddShipResult();
        $order->shipment_no = $awbNumber;
        $order->status = 2;
        $order->shipment_company = $shipment_method->id;
        $order->save();
        return redirect('/provider-panel/orders')->with('success', 'تم ارسال الشحنة بنجاح يمكنك تتبعها بهذا الرقم : ' . $awbNumber);

    }

    public function printShipmentAwb($shipment_no)
    {
        $order = OrderShipments:: where(function ($query) {
            $query->where('shop_id', auth()->id())
                ->orWhere('shop_id', auth()->user()->main_provider);
        })->where('status', '<>', '1')->where('shipment_no', $shipment_no)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'هذا الطلب لا يمكنك التعامل معه');
        }
        $passKey = SmsaSetting::first()->passkey;
        Smsa::key($passKey);   // Setting up the SMSA Key
        $pdf_file = Smsa::getPDF($shipment_no);
        $content = $pdf_file->getGetPDFResult();
        if (!$content) return redirect()->back()->with('error', 'no data');
        return view('providers.orders.shipment_awb', ['object' => $content]);
    }

    public function filter_new_order(Request $request)
    {
        $id = $request->order_id;
        $orders = OrderShipments:: where(function ($query) {
            $query->where('shop_id', auth()->id())
                ->orWhere('shop_id', auth()->user()->main_provider);
        })->where('id', $id)->paginate(50);
//        $orders = Orders::where('id',$id)->where('status',0)->orderBy('id', 'DESC')->paginate(50);
        return view('providers.orders.new_orders', ['objects' => $orders, 'order_id' => $id]);
    }

    public function filter_approved_order(Request $request)
    {
        $id = $request->order_id;
        $orders = Orders::where('id', $id)->where('status', 2)->orderBy('id', 'DESC')->paginate(50);
        return view('providers.orders.approved_orders', ['objects' => $orders, 'order_id' => $id]);
    }

    public function filter_cancelled_order(Request $request)
    {
        $id = $request->order_id;
        $orders = Orders::where('id', $id)->where('status', 3)->orderBy('id', 'DESC')->paginate(50);
        return view('providers.orders.cancelled_orders', ['objects' => $orders, 'order_id' => $id]);
    }

    public function filter_on_progress_order(Request $request)
    {
        $id = $request->order_id;
        $orders = Orders::where('id', $id)->where('status', 1)->orderBy('id', 'DESC')->paginate(50);
        return view('providers.orders.on_progress_orders', ['objects' => $orders, 'order_id' => $id]);
    }

    public function filter_all(Request $request)
    {
        $id = $request->order_id;
        $orders = OrderShipments:: where(function ($query) {
            $query->where('shop_id', auth()->id())
                ->orWhere('shop_id', auth()->user()->main_provider);
        })->where('id', $id)->paginate(50);
        $shipment = Shipmentt::where('id', auth()->user()->provider->shipment_id)->first();
        $shop=User::select('users.*')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . '	) as all_orders')

            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=1	) as new_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=2	) as preparing_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=3	) as shipping_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=4	) as completed_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=5	) as canceled_orders')

            ->where('users.id',auth()->user()->provider->id)
            ->first();
//        $orders = Orders::where('id',$id)->orderBy('id', 'DESC')->paginate(50);
        return view('providers.orders.all', ['objects' => $orders, 'order_id' => $id, 'shipment' => $shipment,'shop'=>$shop]);
    }

    public function bank_transfer_order()
    {
        return view('providers.banks.new_transfer_order', ['objects' => BankTransfer::where('type', "order")->orderBy('id', 'DESC')->get()]);
    }

    public function bank_transfer_member()
    {
        return view('providers.banks.new_transfer_member', ['objects' => BankTransfer::orderBy('id', 'DESC')->get()]);
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
        return view('providers.orders.on_progress_orders', ['objects' => Orders::whereIn('status', [1, 4])->orderBy('id', 'DESC')->paginate(50)]);
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

    public function order_messages($id = 0)
    {
        $order = Orders::find($id);
        if ($order) {

            return view('providers.orders.messages', ['messages' => Messages::where('order_id', $order->id)->get(), 'order' => $order]);

        } else {

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
     * @param \Illuminate\Http\Request $request
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        $order=Purchase_order::where('id',$id)->where('provider_id',$provider_id)->with('purchase_item.product','provider')->first();

        /*
                $total= CartItem::where('order_id',$order->id)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
                $shipment_price=Settings::find(22)->value;
                $taxs=Settings::find(38)->value;
                $order->final_price = $total+$shipment_price+$taxs;
                $order->order_price = $total;
                $order->delivery_price = $shipment_price;
                $order->taxes = $taxs;
                $order->save();*/


        $object=$order;

        return view ('providers.supply.invoice',compact('object'));
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

    public function getOrderDetails($id)
    {
        $object = OrderShipments::where('id', $id)->select('*')
            ->selectRaw('(SELECT sum(price)*quantity FROM cart_items WHERE cart_items.shipment_id =order_shipments.id and cart_items.status !=5) as items_price')
            ->first();
        $shipment = Shipmentt::where('id', auth()->user()->provider->shipment_id)
            ->first();

        if (!$object) return abort(404);
        return view('providers.orders.order_page', ['object' => $object, 'shipment' => $shipment]);

    }

    public function getInvoicePrint($id)
    {
        $object = OrderShipments::where('id', $id)->first();
        if (!$object) return abort(404);
        return view('providers.orders.invoice', ['object' => $object]);

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
