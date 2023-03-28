<?php

namespace App\Http\Controllers\Providers;

use App\Models\Addresses;
use App\Models\Balance;
use App\Models\BankTransfer;
use App\Models\CartItem;
use App\Models\Messages;
use App\Models\Notification;
use App\Models\OrderShipments;
use App\Models\Products;
use App\Models\Settings;
use App\Models\Shipment as Shipmentt;
use App\Models\SmsaSetting;
use App\Models\User;
use App\Models\UsersRegions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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


class OrdersController extends Controller
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
    public function index(Request $request)
    {
        $status = $request->status;
        $objects = OrderShipments::select('*')->where(function ($query) {
            $query->where('shop_id', auth()->id())
                ->orWhere('shop_id', auth()->user()->main_provider);
        })->where(function ($query) use ($status) {
            if ($status == 'new') {
                $query->where('status', 1);
            }
            if ($status == 'preparing') {
                $query->where('status', 2);

            }
            if ($status == 'shipping') {
                $query->where('status', 3);
            }
            if ($status == 'completed') {
                $query->where('status', 4);
            }
            if ($status == 'canceled') {
                $query->where('status', 5);
            }
        })

            ->selectRaw('(SELECT sum(price)*quantity FROM cart_items WHERE cart_items.shipment_id =order_shipments.id and cart_items.status !=5) as items_price')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $shipment = Shipmentt::where('id', auth()->user()->provider->shipment_id)
            ->first();
        $shop=User::select('users.*')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . '	) as all_orders')

            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=1	) as new_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=2	) as preparing_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=3	) as shipping_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=4	) as completed_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=5	) as canceled_orders')

            ->where('users.id',auth()->user()->provider->id)
            ->first();
//$objects=Orders::select('orders.*','order_shipments.id as shipment_id')
//    ->join('order_shipments','order_shipments.order_id','orders.id')
//    ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.shipment_id=order_shipments.id) as cart_count')
//
//    ->where(function ($query) {
//        $query->where('order_shipments.shop_id', auth()->id())
//            ->orWhere('order_shipments.shop_id', auth()->user()->main_provider);
//    })->where('payment_method','<>',0)->paginate(5);
        return view('providers.orders.all', ['objects' => $objects, 'shipment' => $shipment,'shop'=>$shop]);
    }

    public function change_order_status($id)
    {
        $order = OrderShipments:: where(function ($query) {
            $query->where('shop_id', auth()->id())
                ->orWhere('shop_id', auth()->user()->main_provider);
        })->where('id', $id)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'هذا الطلب لا يمكنك التعامل معه');
        }
        if ($order->status != 4) {
            $order->status = $order->status + 1;
            $order->save();
        }
        if ($order->status == 4) {
            $finished_sales = CartItem::join('order_shipments', 'order_shipments.id', 'cart_items.shipment_id')
                ->where('order_shipments.status', 4)
                ->where('order_shipments.id', $order->id)
                ->where('cart_items.status', '<>', 5)
                ->where('cart_items.shop_id', $order->shop_id)
                ->sum('cart_items.price');
            $new_balance = $finished_sales * $order->shop->profit_rate / 100;

            if (@$order->getOrder->payment_method == 1) {
                $balance = new Balance();
                $balance->price = -$new_balance;
                $balance->user_id = $order->shop_id;
                $balance->order_id = $order->id;
                $balance->balance_type_id = 8;
                $balance->notes = 'خصم عمولة التطبيق من توصيل منتجات دفع عند الاستلام';
                $balance->save();
            } else {
                $balance = new Balance();
                $balance->price = $finished_sales;
                $balance->site_profits = -$new_balance;
                $balance->user_id = $order->shop_id;
                $balance->order_id = $order->id;
                $balance->balance_type_id = 9;
                $balance->notes = 'بيع منتجات للطلب رقم' . $order->id;
                $balance->save();
            }
        }
        $changeStatus = CartItem::select('cart_items.id','cart_items.status','cart_items.updated_at')->join('order_shipments', 'order_shipments.id', 'cart_items.shipment_id')
            ->where('order_shipments.id', $order->id)
            ->where('cart_items.shop_id', $order->shop_id)
            ->where('cart_items.status', '<>', 5)
            ->update(['cart_items.status' => $order->status]);


        return redirect()->back()->with('success', 'تم تغيير حالة الطلب بنجاح');
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
            'user_id' => 'required',
        ],[
            'user_id.required'=>'اسم العميل مطلوب'
        ]);
        $user=User::find($request->user_id);
        if($user){
            return redirect('/provider-panel/orders/'.$user->id);
        }
    }
    public function store_order(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
        ]);
        $user=User::find($request->user_id);
        if(!$user){
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'لا يوجد عميل بهذا الرقم',
                ],201);
        }
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        $order=new Orders();
        $order->user_id=$user->id;
        $order->provider_id=$provider_id;
        $order->address_id=$request->address_id?:0;
        $order->payment_method=5;
        if($request->address_id){
            $address=Addresses::find($request->address_id);
            if(!$address){
                return \response()->json([
                    'status' => 200,
                    'message' => 'عنوان غير معرف'
                ]);
            }
            $order->longitude = $address->longitude;
            $order->latitude = $address->latitude;
            $order->address_name = @$address->address;
            $order->address_desc = $address->details;
            $order->country_id = $address->country_id;
            $order->region_id = $address->region_id;
            $order->state_id = $address->state_id;
            $order->address_id = $address->id;
            $order->save();

        }
        $order->status=1;

        $total= CartItem::where('provider_id',$provider_id)->where('type',2)->where('user_id',$user->id)->where('order_id', 0)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
        $shipment_price=Settings::find(22)->value;
        $taxs=Settings::find(38)->value;
        $order->final_price = $total+$shipment_price+(($total+$shipment_price)*$taxs/100);
        $order->order_price = $total;
        $order->delivery_price = $shipment_price;
        $order->cobon_discount = 0;
        $order->taxes = (($total+$shipment_price)*$taxs/100);
        $order->save();
        CartItem::where('provider_id',$provider_id)->where('type',2)->where('user_id',$user->id)->where('order_id', 0)->update(['order_id'=>$order->id]);



/**/
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select('cart_items.id','cart_items.shop_id', 'cart_items.user_id',
            'cart_items.type', 'users.username as shop_name', 'users.shipment_price', 'users.taxes', 'users.shipment_days')
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where('cart_items.order_id', $order->id)
            ->where('cart_items.provider_id', $provider_id)
            ->where('cart_items.type', 2)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')->get();
        if ($objects) {
            foreach ($objects as $object) {
                $cart_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 2)
                    ->where('cart_items.provider_id', $provider_id)
                    ->where('shop_id', $object->shop_id)
                    ->where('order_id', $order->id)
//                    ->where('cart_items.user_id', $object->user_id)
                    ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                    ->join('products', 'cart_items.item_id', 'products.id')->get();

                $shipment = new OrderShipments();
                $shipment->order_id = $order->id;
                $shipment->user_id = $user->id;
                $shipment->shop_id = $object->shop_id;
                $shipment->delivery_date = ' بعد ' . $object->shipment_days . ' يوم';
                $shipment->delivery_date_en = ' after ' . $object->shipment_days . ' days';

                $shipment->delivery_price = $object->shipment_price;
                $shipment->taxes = $object->taxes;

                $shipment->status = 1;
                $shipment->save();
                foreach ($cart_items as $item) {
                    $cart_item = CartItem::find($item->id);
                    if ($cart_item) {
                        $cart_item->order_id = $order->id;
                        $cart_item->shipment_id = $shipment->id;
                        $cart_item->status = 1;
                        $cart_item->save();
                    }
                }
            }
        }
/**/


        return response()->json(
            [
                'status' => 200,
                'id' => $order->id,
                'message' => 'تم اضافة الطلب بنجاح',
            ],200);

    }
    public function create_user(Request $request)
    {

        $input=$request->all();
        $input['phone']=ltrim($request->phone,0);
        $validator = Validator::make($input, [
            'username' => 'required|unique:users,username',
            'phone' => 'required|unique:users,phone|digits:9',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status'=>400,
                    'message' => $validator->errors()->first(),
                ], 202
            );
        }
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        $object = new User;
        $object->username= $request->username;
        $object->email = '';
        $object->main_provider = $provider_id;
        $object->phone = ltrim($request->phone, '0');
        $object->country_id = $request->country_id?:188;
        $object->state_id = $request->state_id?:'';
        $object->region_id = $request->region_id?:'';

        $object->currency_id = $request->currency_id?:1;
        $object->phonecode = $request->phonecode?:966;
        $object->address = $request->address?:'';
        $object->longitude = $request->longitude?:'';
        $object->latitude = $request->latitude?:'';
        $object->activate = 0;
        $object->approved = 0;
        $object->user_type_id = 5 ;
        $object->profit_rate =$request->profit_rate?$request->profit_rate:'';
        $object->device_type =$request->device_type?$request->device_type:'';
        $object->accept_pricing =$request->accept_pricing?1:0;
        $object->accept_estimate =$request->accept_estimate?1:0;
        $object->add_product =$request->add_product?1:0;
        $object->shop_type =$request->shop_type?:0;
        $object->client_type =$request->client_type?:0;
        $object->shipment_id =1;
        $object->shipment_days =3;
        $object->save();
        return response()->json([
            'message' => 'تم اضافة العميل بنجاح',
            'data' =>$object,
        ]);
    }
    public function search_users(Request $request)
    {
        $this->validate($request, [
            'query' => 'required',
        ]);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        $data=User::
            where(function($query) use($provider_id){
                $query->where(['user_type_id'=>5,'approved'=>1]);
                $query->orWhere(['user_type_id'=>5,'approved'=>0,'main_provider'=>$provider_id]);
            })
            ->where(function($query) use($request){
                $query->where('phone', 'LIKE', '%'.$request->get('query').'%');
                $query->orWhere('username', 'LIKE', '%'.$request->get('query').'%');
            })
            ->select('id','username','phone')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->limit(5)
            ->get();


        $output = '<ul class="dropdown-menu" id="search_box_result" style="display:block; position:relative;float:none;overflow:hidden;overflow-y: scroll;max-height: 300px;">';
        foreach($data as $row)
        {
            $output .= '
       <li class="px-2">
            <a class="text-dark get-user" href="#" data-name="'.$row->username.'" data-id="'.$row->id.'">
                        <img class="mx-1 rounded-circle" src="'.$row->photo.'" width="30" height="30"  />'
                .$row->username.'
           </a>
       </li>
       ';
        }
        if(count($data)==0){
            $output.='<li class="px-2" class="alert alert-danger">
                        لا يوجد عملاء مقترحة
                </li>';
        }
        $output.='
                <li class="px-2" class="alert alert-danger">
                      <a href="#" class="add-user">اضافة عميل جديد</a>
                </li>';
        $output .= '</ul>';
        echo $output;
    }
    public function search_products()
    {
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        $data=Products::whereHas('user')->where('provider_id',$provider_id)
            ->where(function($query) {
                $query->where('title', 'LIKE', '%'.request()->search.'%');
                $query->orWhere('description', 'LIKE', '%'.request()->search.'%');
            })
            ->select('id','title','description','price','quantity','min_quantity')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->paginate(3);


        return response()->json(
            [
                'status' => 200,
                'data' => $data,
            ]);
    }


    public function store_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status'=>400,
                    'message' => $validator->errors()->first(),
                ], 202
            );
        }
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        foreach (json_decode($request->ids) as $id){
            $product=Products::find($id);
            $ifExist=CartItem::where('status',0)
                ->where('type',2)
                ->where('user_id',$request->user_id)
                ->where('provider_id',$provider_id)
                ->where('order_id', 0)
                ->where('item_id',$product->id)->first();
            if($ifExist){
                continue;
            }else{

                CartItem::create([
                    'user_id'=>$request->user_id,
                    'shop_id'=>$product->provider_id,
                    'price'=>$product->price,
                    'item_id'=>$product->id,
                    'order_id'=>0,
                    'quantity'=>$product->min_quantity,
                    'type'=>2,
                    'provider_id'=>$provider_id,
                ]);

            }

        }
        $items=CartItem::where('type',2)->where('provider_id',$provider_id)->where('user_id',$request->user_id)->whereHas('product')
            ->where('order_id', 0)
            ->with(['product' => function ($query) {
                $query->select('id','title','price','photo');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->get();
        return response()->json([
            'message' => 'تم الأضافة إلي السلة بنجاح',
            'items' =>$items,
        ]);
    }
    public function update_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status'=>400,
                    'message' => $validator->errors()->first(),
                ], 202
            );
        }
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        $input=$request->all();
        $items=CartItem::where('id',$request->id)->where('order_id', 0)
            ->where('type',2)->where('provider_id',$provider_id)->first();
        $product=Products::find($items->item_id);
        if(intval($product->min_quantity) > intval($request->quantity)){
            return response()->json(
                [
                    'status'=>400,
                    'message' => ' اقل كمية متاحة للبيع هي '.$product->min_quantity,
                    'min_quantity'=>$product->min_quantity
                ], 202
            );
        }

        $items->update($input);

        $items=CartItem::where('type',2)->where('provider_id',$provider_id)->where('user_id',$request->user_id)->whereHas('product')
            ->where('order_id', 0)->with(['product' => function ($query) {
                $query->select('id','title','price','photo');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->get();
        return response()->json([
            'message' => 'تم تعديل السلة بنجاح',
            'items' =>$items,
        ]);
    }
    public function delete_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status'=>400,
                    'message' => $validator->errors()->first(),
                ], 400
            );
        }
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        $item=CartItem::where('id',$request->id)->where('provider_id',$provider_id)->first();
        $item->delete();

        $items=CartItem::where('type',2)->where('provider_id',$provider_id)->where('user_id',$request->user_id)->whereHas('product')
            ->where('order_id', 0)->with(['product' => function ($query) {
                $query->select('id','title','price','photo');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->get();
        return response()->json([
            'message' => 'تم حذف المنتج من السلة بنجاح',
            'items' =>$items,
        ]);
    }

    public function show($id)
    {
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        $user=User::whereId($id)->with('addresses')->first();



        /**/
        $current_items = CartItem::where('user_id', $user->id)->where('type',2)->where('order_id', 0)->get();
        $messages = [];
        foreach ($current_items as $item) {
            $product = $item->product;
            if ($product->quantity == 0) {
                $item->delete();
                $messages[] = $product->title . ' لم يعد متاح الان ';
            }
            elseif ($item->quantity > $product->quantity) {
                $item->quantity = $product->quantity;
                $item->save();
                $messages[] = ' تم تعديل الكمية المطلوبة للمنتج ' . $product->title;

            }
            $price = $product->price_after_discount ?: $product->price;
            if ($product->quantity && $item->price != $price) {
                $item->price = $price;
                $item->save();
                $messages[] = ' تم تعديل سعر المنتج ' . $product->title;
            }

        }
        /**/


        $cart=CartItem::where('provider_id',$provider_id)->where('user_id',$id)->where('order_id', 0)
            ->select('*')
            ->with(['product' => function ($query) {
                $query->select('id', 'title','price')
                    ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->get();
        $taxs_price=Settings::find(38)->value;
        $shipment_price=Settings::find(22)->value;

        return view('providers.orders.create-order',compact('user','cart','taxs_price','shipment_price'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order=Orders::where('id',$id)->with('shipments.cart_items','address','user')
//            ->when(auth()->user()->user_type_id!=1, function ($query) {
//                return $query->where('region_id',auth()->user()->region_id)
//                    ->orWhere('added_by',auth()->id());
//            })
            ->first();
        if(!$order)abort(404);

        $object=$order;

        return view ('providers.orders.invoice',compact('object'));
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
