<?php

namespace App\Http\Controllers\Panel;

use App\Models\Addresses;
use App\Models\Balance;
use App\Models\BankTransfer;
use App\Models\CartItem;
use App\Models\Messages;
use App\Models\Notification;
use App\Models\OrderShipments;
use App\Models\Products;
use App\Models\Purchase_item;
use App\Models\Purchase_order;
use App\Models\Purchase_payment_method;
use App\Models\Settings;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Orders;
use App\Models\OrdersOrders;
use App\Models\OrdersPhotos;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Psy\Util\Str;
use App\Http\Controllers\Website\Auth\LoginController;

class ReceivingPurchasesController extends Controller
{
    public function __construct()
    {
            $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
        /*$this->middleware(function ($request, $next) {
            $this->check_settings(44);
            return $next($request);
        });*/
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        $purchases_orders = Purchase_order::selectRaw('(SELECT count(*) FROM purchase_orders ) as all_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=1	) as new_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=2	) as preparing_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=3	) as shipping_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=4	) as in_shipment')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=5	) as canceled_orders')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=6	) as progress_shipment')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=8	) as ready_to_storage')
            ->selectRaw('(SELECT count(*) FROM purchase_orders WHERE  purchase_orders.status=7	) as completed_orders')
            ->first();
        $status = request()->status;
        $objects = Purchase_order::where(function ($query) use ($status) {
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
            if ($status == 'ready_to_storage') {
                $query->where('status', 8);
            }
            if ($status == 'completed_orders') {
                $query->where('status', 7);
            }
        })->orderBy('created_at','desc')->paginate();
        return view('admin.purchases.all', compact('objects', 'purchases_orders'));
    }



    public function uploadOne(UploadedFile $file, $folder = null, $filename = null, $disk = 'public')
    {
        $name = !is_null($filename) ? $filename : \Illuminate\Support\Str::random(25);

        $file->storeAs(
            $folder,
            $name . "." . $file->getClientOriginalExtension(),
            $disk
        );
        return $name . "." . $file->getClientOriginalExtension();
    }

    public function approve_order($id = 0, Request $request)
    {
        $order = Orders::find($id);
        $status = $order->status;
        if ($status == 4) {
            $order->status = $status + 2;
            OrderShipments::where('order_id', $order->id)->update(['status' => $status + 2]);
        } else {
            $order->status = $status + 1;
            OrderShipments::where('order_id', $order->id)->update(['status' => $status + 1]);
        }
        $order->save();
        return redirect()->back()->with('success', 'تمت الموافقة على الطلب بنجاح .');
    }
    public function approved_shipment($id = 0, Request $request)
    {
        $shipment = OrderShipments::whereId($id)->where('status', '>=', 1)->first();

        $status = $shipment->status;
        if ($status == 4) {
            $shipment->status = $status + 2;
        } else {
            $shipment->status = $status + 1;
        }
        $shipment->save();
        $if_shipments = OrderShipments::where('order_id', $shipment->order_id)
            ->where('status', $status)
            ->where('id', '!=', $shipment->id)->first();
        if (!$if_shipments) {
            if ($status == 4) {
                Orders::whereId($shipment->order_id)->update(['status' => $status + 2]);
            } else {
                Orders::whereId($shipment->order_id)->update(['status' => $status + 1]);
            }
        }
        return redirect()->back()->with('success', 'تمت الموافقة على الشحنة بنجاح .');
    }
    public function cancle_order($id = 0, Request $request)
    {
        $order = Orders::find($id);
        $order->status = 5;
        $order->save();
        OrderShipments::where('order_id', $order->id)->update(['status' => 5]);
        return redirect()->back()->with('success', 'تمت الغاء بنجاح .');
    }





    public function show($id)
    {

        $object = Purchase_order::with('purchase_item')->whereId($id)->first();
        return view('admin.purchases.show', compact('object'));
    }
    public function edit($id)
    {
        $order = Purchase_order::where('id', $id)->with('purchase_item.product', 'provider')->first();

        /*
        $total= CartItem::where('order_id',$order->id)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
        $shipment_price=Settings::find(22)->value;
        $taxs=Settings::find(38)->value;
        $order->final_price = $total+$shipment_price+$taxs;
        $order->order_price = $total;
        $order->delivery_price = $shipment_price;
        $order->taxes = $taxs;
        $order->save();*/


        $object = $order;

        return view('admin.purchases.invoice', compact('object'));
    }

    public function upload_invoice($id, Request $request)
    {
        $order = Purchase_order::where('id', $id)->first();

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $name = 'transfer-purchase-' . time() . '-' . uniqid();
            $destinationPath = 'uploads';
            $fileName = $this->uploadOne($file, $destinationPath, $name);
            $order->transfer_photo = $fileName;
            $order->save();
            return redirect()->back()->with('success', 'تم اضافة صورة التحويل بنجاح');
        } else {
            return redirect()->back()->with('error', 'قم بإضافة ملف الايصال');
        }
    }
    public function select_driver($id, Request $request)
    {
        $order = Purchase_order::where('id', $id)->first();
        $driver = User::where('id', $request->driver_id)->where('user_type_id', 6)->where('is_archived', 0)->first();
        if ($driver) {
            $order->driver_id = $driver->id;
            $order->save();
            return redirect()->back()->with('success', 'تم تعيين السائق بنجاح');
        } else {
            return redirect()->back()->with('error', 'لا يوجد سائق');
        }
    }
    public function send_invoice($id)
    {
        $order = Orders::where('id', $id)->first();
        if ($order->short_code == null) {
            $order->short_code = $order->id . str_random(4);
            $order->save();
        }
        $order->sent_sms = 1;
        $order->save();

        $user = User::find($order->user_id);
        $smsMessage = 'مرحباً
تم إنشاء طلب لك لدى الطريق الذهبي.
لعرض تفاصيل الطلب:
 : ' . url('/i/' . $order->short_code);


        $phone = $this->convertNum(ltrim($user->phone, '0'));
        $phone_number = '+' . $user->phonecode . $phone;
        $customer_id = Settings::find(25)->value;
        $api_key = Settings::find(26)->value;
        $message_type = "OTP";

        $resp = $this->send4SMS($customer_id, $api_key, $smsMessage, $phone_number, 'GoldenRoad');
        return redirect()->back()->with('success', 'تمت إرسال الفاتورة للعميل .');
    }
    public function convertNum($number)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $english = [0,  1,  2,  3,  4,  5,  6,  7,  8,  9];
        return str_replace($arabic, $english, $number);
    }
    public function get_data($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public  function send4SMS($oursmsusername, $oursmspassword, $messageContent, $mobileNumber, $senderName)
    {

        $user = $oursmsusername;
        $password = $oursmspassword;
        $sendername = $senderName;
        $text =  $messageContent;
        $to = $mobileNumber;

        $getdata = http_build_query(
            $fields = array(
                "username" => $user,
                "password" => $password,
                "message" => $text,
                "numbers" => $to,
                "sender" => $sendername,
                "unicode" => 'e',
                "return" => 'json'
            )
        );

        $opts = array(
            'http' =>
            array(
                'method' => 'GET',
                'header' => 'Content-Type: text/html; charset=utf-8',


            )
        );

        $context = stream_context_create($opts);

        $response = $this->get_data('https://www.4jawaly.net/api/sendsms.php?' . $getdata, false, $context);


        return $response;

        // auth call

        //        $url = "https://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E&return=full";

        //لارجاع القيمه json
        //$url = "https://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text
        //&sender=$sendername&unicode=E&return=json";
        // لارجاع القيمه xml
        //$url = "https://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E&return=xml";
        // لارجاع القيمه string
        //$url = "https://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E";
        // Call API and get return message
        //fopen($url,"r");

        //        $ret = file_get_contents($url);
        //        echo nl2br($ret);
    }



    public function purchases()
    {
        //        return Products::where('provider_id',251)->whereRaw('quantity <= min_warehouse_quantity')->get();
        $objects = User::where(['is_archived' => 0, 'approved' => 1])
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->whereHas('products', function (Builder $query) {
                $query->where(['stop' => 0, 'is_archived' => 0])
                    ->whereRaw('quantity <= min_warehouse_quantity'); //<=
                $query->select(
                    \DB::raw('(SELECT CAST(SUM(IFNULL(quantity,0)) AS UNSIGNED) FROM purchase_items WHERE purchase_items.product_id = products.id) as progress_quantity'),
                    'products.id',
                    'products.provider_id',
                    'products.original_price',
                    'products.title',
                    'products.min_warehouse_quantity',
                    'products.quantity',
                    DB::raw('CAST((products.min_warehouse_quantity - products.quantity) AS UNSIGNED)  AS qty'),
                    DB::raw('CAST((products.min_warehouse_quantity - products.quantity) AS UNSIGNED)  AS min_qty')
                );
                $query->havingRaw('IFNULL(progress_quantity, 0) - qty < ?', [0]);
            })
            ->with(['products' => function ($query) {
                $query->where(['stop' => 0, 'is_archived' => 0])
                    ->whereRaw('quantity <= min_warehouse_quantity'); //<=
                $query->select(
                    \DB::raw('(SELECT CAST(SUM(IFNULL(quantity,0)) AS UNSIGNED) FROM purchase_items WHERE purchase_items.product_id = products.id) as progress_quantity'),
                    'products.id',
                    'products.provider_id',
                    'products.original_price',
                    'products.title',
                    'products.min_warehouse_quantity',
                    'products.quantity',
                    DB::raw('CAST((products.min_warehouse_quantity - products.quantity) AS UNSIGNED)  AS qty'),
                    DB::raw('CAST((products.min_warehouse_quantity - products.quantity) AS UNSIGNED)  AS min_qty')
                );
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                $query->havingRaw('IFNULL(progress_quantity, 0) - qty < ?', [0]);
            }])

            ->paginate();
        return view('admin.orders.purchases', compact('objects'));
    }
    public function purchases_items($id)
    {
        $object = User::where('id', $id)
            ->where(['is_archived' => 0, 'approved' => 1])
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->whereHas('products', function (Builder $query) {
                $query->where(['stop' => 0, 'is_archived' => 0])
                    ->whereRaw('quantity <= min_warehouse_quantity'); //<=
                $query->select(
                    \DB::raw('(SELECT CAST(SUM(IFNULL(quantity,0)) AS UNSIGNED) FROM purchase_items WHERE purchase_items.product_id = products.id) as progress_quantity'),
                    'products.id',
                    'products.provider_id',
                    'products.original_price',
                    'products.title',
                    'products.min_warehouse_quantity',
                    'products.quantity',
                    DB::raw('CAST((products.min_warehouse_quantity - products.quantity) AS UNSIGNED)  AS qty'),
                    DB::raw('CAST((products.min_warehouse_quantity - products.quantity) AS UNSIGNED)  AS min_qty')
                );

                $query->havingRaw('IFNULL(progress_quantity, 0) - qty < ?', [0]);
            })
            ->with(['products' => function ($query) {
                $query->where(['stop' => 0, 'is_archived' => 0])
                    ->whereRaw('quantity <= min_warehouse_quantity'); //<=
                $query->select(
                    \DB::raw('(SELECT CAST(SUM(IFNULL(quantity,0)) AS UNSIGNED) FROM purchase_items WHERE purchase_items.product_id = products.id) as progress_quantity'),
                    'products.id',
                    'products.provider_id',
                    'products.original_price',
                    'products.title',
                    'products.min_warehouse_quantity',
                    'products.quantity',
                    DB::raw('CAST((products.min_warehouse_quantity - products.quantity) AS UNSIGNED)  AS qty'),
                    DB::raw('CAST((products.min_warehouse_quantity - products.quantity) AS UNSIGNED)  AS min_qty')
                );
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                $query->havingRaw('IFNULL(progress_quantity, 0) - qty < ?', [0]);
            }])

            ->first();
        $delivery_price = Settings::find(22)->value;
        $object->delivery_price = $delivery_price;
        $object->payment_methods = Purchase_payment_method::all();
        return view('admin.orders.purchases-items', compact('object'));
    }
    public function order_details($id)
    {

        $object = Purchase_order::with('purchase_item')->whereId($id)->first();
        return view('admin.orders.purchase-order-show', compact('object'));
    }

    public function add_order(Request $request)
    {
        $todayDate = Carbon::tomorrow();

        $validator = Validator::make($request->all(), [
            'products' => 'required',
            'payment_terms' => 'required',
            'delivery_date' => 'required|date|after_or_equal:' . $todayDate,
            'provider_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }

        $order = new Purchase_order();
        $order->code = str_random(15);
        $order->provider_id = $request->provider_id;
        $order->delivery_date = $request->delivery_date;
        $order->payment_terms = $request->payment_terms;
        $order->payment_method = 4;
        $order->status = 1;
        $order->save();

        foreach (json_decode($request->products) as $product) {
            $get_product = Products::find($product->id);
            $item = new Purchase_item();
            $item->provider_id = $request->provider_id;
            $item->product_id = $product->id;
            $item->status = 1;
            $item->quantity = $product->quantity;
            $item->price = $get_product->original_price;
            $item->order_id = $order->id;
            $item->save();
        }
        $total = Purchase_item::where(['order_id' => $order->id, 'provider_id' => $request->provider_id])
            ->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;

        $taxs = Settings::find(38)->value;
        $tax_price = (($total) * $taxs / 100);
        $order->final_price = $total + $tax_price;
        $order->order_price = $total;
        $order->taxes = $tax_price;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $name = 'transfer-purchase-' . time() . '-' . uniqid();
            $destinationPath = 'uploads';
            $fileName = $this->uploadOne($file, $destinationPath, $name);
            $order->transfer_photo = $fileName;
        }
        $order->save();

        return \response()->json([
            'status' => 200,
            'order_id' => $order->id,
            'url' => url('/admin-panel/warehouse-purchases/order') . '/' . $order->id,
            'message' => 'تم انشاء الطلب بنجاح '
        ]);
    }


    public function change_order_status($id, Request $request)
    {
        $this->validate($request, [
            //            'items.quantity' => 'required|min:0',
            'items.*' => 'required|min:0',
        ], [
            'items.*.min' => 'اقل كمية هي صفر',
            'items.*.integer' => 'الكمية يجب ان تكون عدد صحيحا'
        ]);
        $order = Purchase_order::where('id', $id)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'هذا الطلب لا يمكنك التعامل معه');
        }
        if ($order->status == 4) {
            $status = 2;
        } else {
            $status = 1;
        }
        if ($order->status == 8) {
            foreach ($request->items as $item) {
                $cart_item = Purchase_item::where('order_id', $id)->where('id', intval($item['id']))->first();
                $cart_item->delivered_quantity = intval($item['quantity']);
                $cart_item->save();
            }
            $status = -1;
        }
        $order->status = $order->status + $status;
        $order->save();


        $changeStatus = Purchase_order::select('purchase_orders.id', 'purchase_orders.status', 'purchase_orders.updated_at')
            ->where('purchase_orders.id', $order->id)
            ->update(['purchase_orders.status' => $order->status]);

        $items = Purchase_item::where('order_id', $order->id)->get();
        foreach ($items as $item) {
            $item->status = $order->status;
            $item->save();
            Products::where('id', $item->product_id)->increment('quantity', $item->quantity);
        }

        return redirect()->back()->with('success', 'تم تغيير حالة الطلب بنجاح');
    }

    public function refuseOrders()
    {
        $drivers = User::where('user_type_id', 6)->get();
        $objects = Purchase_order::when(request('order_id'), function ($query) {
            $query->where('id', request('order_id'));
        })
            ->when(request('driver_id'), function ($query) {
                $query->where('driver_id', request('driver_id'));
            })
            ->when(request('from') && request('to'), function ($query) {
                $query->whereBetween('created_at', [request('from') . ' ' . '00:00:00' ?: now()->subYears(4) . ' ' . '23:59:59', request('to') . ' ' . '23:59:59' ?: now() . ' ' . '23:59:59']);
            })
            ->where('refused', 1)->orderBy('refuse_date', 'desc')->paginate(10);

        return view('admin.purchases.refused_orders', compact('objects', 'drivers'));
    }

    public function missingOrdersFromDriver()
    {
        $drivers = User::where('user_type_id', 6)->get();
        $objects = Purchase_order::when(request('order_id'), function ($query) {
            $query->where('id', request('order_id'));
        })
            ->when(request('driver_id'), function ($query) {
                $query->where('driver_id', request('driver_id'));
            })
            ->when(request('from') && request('to'), function ($query) {
                $query->whereBetween('created_at', [request('from') . ' ' . '00:00:00' ?: now()->subYears(4) . ' ' . '23:59:59', request('to') . ' ' . '23:59:59' ?: now() . ' ' . '23:59:59']);
            })
            ->whereHas('purchase_item', function ($query) {
                $query->whereRaw('quantity > delivered_quantity');
            })
            ->where('status', 7)->orderBy('provider_delivery_date', 'desc')
            ->paginate(10);

        return view('admin.purchases.missing_orders', compact('objects', 'drivers'));
    }
}
