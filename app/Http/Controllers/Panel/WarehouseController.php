<?php

namespace App\Http\Controllers\Panel;

use Psy\Util\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Cobons;
use App\Models\Orders;
use App\Models\Balance;
use App\Models\CartItem;
use App\Models\Products;
use App\Models\Settings;
use App\Models\BankTransfer;
use App\Models\Notification;
use App\Models\PageCategory;
use Illuminate\Http\Request;
use App\Models\Purchase_item;
use App\Models\OrderShipments;
use App\Models\Purchase_order;
use App\Models\CobonsProviders;
use App\Models\CobonsCategories;
use Illuminate\Http\UploadedFile;
use App\Services\SendNotification;
use Illuminate\Support\Facades\DB;
use App\Models\PageCategoryProduct;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use App\Helpers\SendFcmNotification;
use App\Http\Controllers\Controller;
use App\Models\Purchase_payment_method;
use App\Http\Resources\MyOrdersResources;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use MFrouh\Sms4jawaly\Facades\Sms4jawaly;

class WarehouseController extends Controller
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
        //        $total= CartItem::where('order_id',301)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
        $objects = Orders::whereIn('status', [2, 3, 4, 6, 7])
            ->when(request()->order_id, function ($query) {
                $query->where('id', request()->order_id);
            })
            ->when(request('from') && request('to'), function ($query) {
                $query->whereBetween('warehouse_date', [request('from') . ' ' . '00:00:00' ?: now()->subYears(4) . ' ' . '23:59:59', request('to') . ' ' . '23:59:59' ?: now() . ' ' . '23:59:59']);
            })
            ->when(in_array(request()->status, [2, 3]), function ($query) {
                $query->where('status', request()->status);
            })
            ->when(request()->status == 4, function ($query) {
                $query->whereIn('status', [4, 6, 7]);
            })
            ->where('financial_date', '!=', null)
            ->orderBy('financial_date', 'desc')
            ->paginate(50);
        return view('admin.orders.warehouse-orders', ['objects' => $objects]);
    }

    public function  warehouseDetails($id)
    {
        OrderShipments::where('order_id', $id)->doesntHave('cart_items')->delete();
        $taxs = Settings::find(38)->value;
        $object = Orders::select(
            'orders.*',
            \Illuminate\Support\Facades\DB::raw('sum(cart_items.price * cart_items.quantity) as subtotal'),
            \Illuminate\Support\Facades\DB::raw('((sum(cart_items.price * cart_items.quantity)+orders.delivery_price)*' . $taxs . '/100) as order_vat')
        )
            ->join('cart_items', 'cart_items.order_id', 'orders.id')
            ->where('orders.id', $id)->with('shipments.cart_items', 'address', 'user')->where('orders.status', '>=', 3)->where('orders.status', '!=', 5)->first();

        return view('admin.orders.warehouse-details', ['object' => $object]);
    }

    public function orderHasWrongStatus()
    {
        $objects = Orders::where('status', '>', 7)
            ->when(request()->order_id, function ($query) {
                $query->where('id', request()->order_id);
            })
            ->where('financial_date', '!=', null)
            ->orderBy('financial_date', 'desc')
            ->paginate(10);

        return view('admin.orders.wrong-status-orders', ['objects' => $objects]);
    }

    public function completeOrder($id)
    {
        $order = Orders::where('status', '>', 7)->find($id);
        $order->status = 7;
        $order->save();
        SendNotification::order($order->id);
        return redirect()->back()->with('success', 'تم تأكيد الطلب بنجاح');
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
        if (!in_array($status, [0, 1, 2, 3, 4, 6])) {
            return redirect()->back()->with('error', 'الطلب غير موجود');
        }
        if ($status == 4) {
            $order->status = $status + 2;
            OrderShipments::where('order_id', $order->id)->update(['status' => $status + 2]);
        } else {
            $order->status = $status + 1;
            OrderShipments::where('order_id', $order->id)->update(['status' => $status + 1]);
        }
        $order->save();
        SendNotification::order($order->id);
        if ($order->status == 6 || $order->status == 4) {
            $notification_for_client = new Notification();
            $notification_for_client->sender_id = 1;
            $notification_for_client->reciever_id = $order->user_id;
            $notification_for_client->order_id = $order->id;
            $notification_for_client->type = 3;
            $notification_title = '';
            $notification_message = '';

            if ($order->status == 4) {
                $notification_for_client->message = ' جاري تجهيز طلبك رقم ' . $order->id;
                $notification_for_client->message_en = ' Your order num. #' . $order->id . ' is being shipped ';

                if ($order->user->lang == "en") {
                    $notification_title = "Your order is being shipped";
                    $notification_message = $notification_for_client->message_en;
                } else {
                    $notification_title = "طلبك قيد الشحن";
                    $notification_message = $notification_for_client->message;
                }
            }
            if ($order->status == 6) {
                $notification_for_client->message = ' طلبك رقم ' . $order->id . ' قيد التوصيل ';
                $notification_for_client->message_en = ' Your order num. #' . $order->id . ' On Delivering ';

                if ($order->user->lang == "en") {
                    $notification_title = "Your order On Delivering";
                    $notification_message = $notification_for_client->message_en;
                } else {
                    $notification_title = "طلبك قيد التوصيل";
                    $notification_message = $notification_for_client->message;
                }
            }

            $notification_for_client->save();
            SendFcmNotification::send_fcm_notification($notification_title, $notification_message, $notification_for_client, new MyOrdersResources($order));
        }

        return redirect()->back()->with('success', 'تمت الموافقة على الطلب بنجاح .');
    }
    public function approved_shipment($id = 0, Request $request)
    {
        $shipment = OrderShipments::whereId($id)->where('status', '>=', 1)->first();

        $status = $shipment->status;
        if (!in_array($status, [1, 2, 3, 4, 6])) {
            return redirect()->back()->with('error', 'الطلب غير موجود');
        }

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
            SendNotification::order($shipment->order_id);
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
        OrderShipments::where('order_id', $id)->doesntHave('cart_items')->delete();
        $taxs = Settings::find(38)->value;
        $object = Orders::select(
            'orders.*',
            \Illuminate\Support\Facades\DB::raw('sum(cart_items.price * cart_items.quantity) as subtotal'),
            \Illuminate\Support\Facades\DB::raw('((sum(cart_items.price * cart_items.quantity)+orders.delivery_price)*' . $taxs . '/100) as order_vat')
        )
            ->join('cart_items', 'cart_items.order_id', 'orders.id')
            ->where('orders.id', $id)->with('shipments.cart_items', 'address', 'user')->where('orders.status', '>=', 2)->first();
        return view('admin.orders.warehouse-order-show', compact('object'));
    }

    public function editOrder($id)
    {
        $object = Orders::where('status', 2)->where('id', $id)->with('shipments.cart_items', 'address', 'user')->first();
        if (!$object) {
            return redirect()->back()->with('error', 'لا يمكن تعديل الطلب');
        }
        return view('admin.orders.warehouse-edit-order', compact('object'));
    }

    public function updateOrder(Request $request, $id)
    {
        $order = Orders::where('status', 2)->find($id);
        if (!$order) {
            return redirect('/admin-panel/warehouse?status=2')->with('error', 'لا يمكن تعديل الطلب');
        }
        $order->update(['paid_price' => $order->final_price]);
        foreach ($request->all() as $key => $value) {
            if ($key != '_token') {
                $cart_item = CartItem::find($key);
                $cart_item->update([
                    'quantity' => $value,
                    'quantity_difference' => $cart_item->quantity - $value,
                ]);
            }
        }
        $order = Orders::find($id);
        $total = CartItem::where('order_id', $id)->where('type', 1)
            ->select(DB::raw('sum(price * quantity) as total'))
            ->first()->total;
        $cobon = 0;
        if ($order->cobon_discount > 0) {
            $code = Cobons::where('code', $order->cobon)->first();
            if (!$code) {
                return redirect()->back()->with('error', 'لا يمكن تعديل الطلب');
            }
            $discount_prices = CartItem::select(
                'cart_items.id',
                'cart_items.price',
                'cart_items.quantity',
                'cart_items.item_id',
                'products.category_id',
                \Illuminate\Support\Facades\DB::raw('sum(cart_items.price * cart_items.quantity) as total')
            )

                ->join('products', 'products.id', 'cart_items.item_id')
                ->where(function ($q) use ($code) {
                    if ($code->link_type == 'category') {
                        $q->whereIn('products.category_id', function ($query) use ($code) {
                            $query->select('category_id')
                                ->from(with(new CobonsCategories())->getTable())
                                ->where('cobon_id', $code->id);
                        });
                    } else {
                        $q->whereIn('products.provider_id', function ($query) use ($code) {
                            $query->select('user_id')
                                ->from(with(new CobonsProviders())->getTable())
                                ->where('cobon_id', $code->id);
                        });
                    }
                })
                ->where('cart_items.order_id', $order->id)
                ->where('type', 1)
                ->first();
            $total1 = $discount_prices ? $discount_prices->total : 0;
            $percent = $code->percent;
            $final_percent_price = ($total1 * $percent) / 100; // الخصم بالنسبه
            $final_money_price = $code->max_money; //اعلي مبلغ خصم
            if ($final_percent_price >= $final_money_price && $code->max_money != 0) {
                $final_cobon_money = $final_money_price;
            } else {
                $final_cobon_money = $final_percent_price;
            }

            if ($final_cobon_money == 0) {
                $order->cobon_discount = 0;
                $order->save();
            } else {
                $order->cobon_discount = $final_cobon_money;
                $order->save();
                $cobon = $final_cobon_money;
            }
        }
        $shipment_price = $order->parent_order ? 0 : Settings::find(22)->value;
        $taxs = Settings::find(38)->value;
        $final_price = ($total + $shipment_price - $cobon) + ((($total + $shipment_price - $cobon) * $taxs) / 100);
        $order_price = $total;
        $delivery_price = $shipment_price;
        $taxes = ((($total + $shipment_price - $cobon) * $taxs) / 100);
        $order->update([
            'order_price' => $order_price,
            'delivery_price' => $delivery_price,
            'taxes' => $taxes,
            'final_price' => $final_price,
            'status' => 3,
        ]);
        if ($order->paid_price > $order->final_price) {
            $order->update(['is_edit' => 1, 'edit_date' => now()]);
            $smsMessage = "عزيزي العميل يوجد تعديل على طلبك {$order->id}  : الرجاء أتخاذ الاجراء خلال 24 ساعة أو سوف يتم تحويل المبلغ على محفظتك" . url('/order-details/' . $order->id);
            $phone = $this->convertNum(ltrim($order->user->phone, '0'));
            $phone_number = '+' . $order->user->phonecode . $phone;
            $customer_id = Settings::find(25)->value;
            $api_key = Settings::find(26)->value;
            $message_type = "OTP";
            $resp = @$this->send4SMS($customer_id, $api_key, $smsMessage, $phone_number, 'GoldenRoad');
            $notification_for_client = new Notification();
            $notification_for_client->sender_id = 1;
            $notification_for_client->reciever_id = $order->user_id;
            $notification_for_client->order_id = $order->id;
            $notification_for_client->type = 3;
            $notification_title = '';
            $notification_message = '';
            $notification_for_client->message = 'تم التعديل علي طلبك رقم  '  . '  ' . $order->id . ' ' . 'و طلبك الان جاهز للشحن';
            $notification_for_client->message_en = "Your order $order->id  has been updated and is ready to be shipped";

            if ($order->user->lang == "en") {
                $notification_title = "Your order is ready shipped";
                $notification_message = $notification_for_client->message_en;
            } else {
                $notification_title = "طلبك جاهز للشحن";
                $notification_message = $notification_for_client->message;
            }

            $notification_for_client->save();
            SendFcmNotification::send_fcm_notification($notification_title, $notification_message, $notification_for_client, new MyOrdersResources($order));
            SendNotification::order($order->id, 4);
        }
        OrderShipments::where('order_id', $order->id)->update(['status' => 3]);
        return redirect('/admin-panel/warehouse/' . $order->id)->with('success', 'تم تعديل الطلب وشحنه بنجاح .');
    }

    public function getEditOrder()
    {
        $objects = Orders::where('is_edit', 1)
            ->when(request('order_id'), function ($query) {
                $query->where('id', request('order_id'));
            })
            ->where('financial_date', '!=', null)
            ->orderBy('edit_date', 'desc')->paginate(10);
        return view('admin.orders.edit-orders', ['objects' => $objects]);
    }

    public function getMissingOrder()
    {
        $objects = Orders::where('status', 1)->when(request('order_id'), function ($query) {
            $query->where('id', request('order_id'));
        })->where('parent_order', '!=', NULL)->paginate(10);
        return view('admin.orders.missing-orders', ['objects' => $objects]);
    }

    public function getReturnBalanceOrder()
    {
        $objects = Balance::where('status', 1)->where('balance_type_id', 15)->orderBy('id', 'desc')->get();
        return view('admin.orders.balance-orders', ['objects' => $objects]);
    }

    public function cancelOrder($id)
    {
        $order = Orders::where('id', $id)->where('stop', 0)->where('return_to_wallet', 0)->first();

        if (!$order) {
            return redirect()->back()->with('error', 'لا يمكن الغاء الطلب');
        }

        $order->update(['status' => 5]);

        $items = CartItem::where('order_id', $order->id)
            ->select('id', 'item_id', 'quantity', 'quantity_difference')
            ->get();
        foreach ($items as $item) {
            Products::where('id', $item->item_id)->increment('quantity', $item->quantity);
        }

        OrderShipments::where('order_id', $order->id)->update(['status' => 5]);

        CartItem::where('order_id', $order->id)->update(['status' => 5]);

        $balance = new Balance();
        $balance->user_id = $order->user_id;
        $balance->price = $order->final_price;
        $balance->balance_type_id = 16;
        $balance->status = 1;
        $balance->order_id = $order->id;
        $balance->notes = '  الغاء الطلب رقم ' . $order->id;
        $balance->save();

        $notification_for_client = new Notification();
        $notification_for_client->sender_id = 1;
        $notification_for_client->reciever_id = $order->user_id;
        $notification_for_client->order_id = $order->id;
        $notification_for_client->type = 3;
        $notification_for_client->message = ' تم الغاء طلبك رقم ' . $order->id;
        $notification_for_client->message_en = ' Your order num. #' . $order->id . ' was canceled ';

        if ($order->user->lang == "en") {
            $notification_title = "Your order was Canceled";
            $notification_message = $notification_for_client->message_en;
        } else {
            $notification_title = "تم الغاء طلبك ";
            $notification_message = $notification_for_client->message;
        }
        $notification_for_client->save();
        SendFcmNotification::send_fcm_notification($notification_title, $notification_message, $notification_for_client, new MyOrdersResources($order));

        SendNotification::order($order->id);

        return redirect()->back()->with('success', 'تمت الالغاء بنجاح .');
    }

    public function stopOrder($id)
    {
        $order = Orders::where('id', $id)->where('stop', 0)->where('return_to_wallet', 0)->first();

        if (!$order) {
            return redirect()->back()->with('error', 'لا يمكن تعليق الطلب');
        }
        $order->update(['stop' => 1]);

        SendNotification::order($order->id, 2);

        return redirect()->back()->with('success', 'تمت تعليق بنجاح .');
    }

    public function returnToWallet($id)
    {
        $order = Orders::where('id', $id)->where('stop', 0)->where('return_to_wallet', 0)->first();

        if (!$order) {
            return redirect()->back()->with('error', 'لا يمكن اعادة مبلغ الطلب للمحفظة ');
        }
        $order->update(['return_to_wallet' => 1]);

        $items = CartItem::where('order_id', $order->id)
            ->select('id', 'item_id', 'quantity', 'quantity_difference')
            ->get();
        foreach ($items as $item) {
            Products::where('id', $item->item_id)->increment('quantity', $item->quantity);
        }

        $balance = new Balance();
        $balance->user_id = $order->user_id;
        $balance->price = $order->final_price;
        $balance->balance_type_id = 16;
        $balance->status = 1;
        $balance->order_id = $order->id;
        $balance->notes = '  اعادة مبلغ الطلب للمحفظة للطلب رقم ' . $order->id;
        $balance->save();

        $notification_for_client = new Notification();
        $notification_for_client->sender_id = 1;
        $notification_for_client->reciever_id = $order->user_id;
        $notification_for_client->order_id = $order->id;
        $notification_for_client->type = 3;
        $notification_for_client->message = ' تم اعادة مبلغ الطلب للمحفظة للطلب رقم ' . $order->id;
        $notification_for_client->message_en = ' Your order num. #' . $order->id . ' return to your wallet ';

        if ($order->user->lang == "en") {
            $notification_title = "Your order return to wallet";
            $notification_message = $notification_for_client->message_en;
        } else {
            $notification_title = "تم اعادة مبلغ الطلب للمحفظة ";
            $notification_message = $notification_for_client->message;
        }
        $notification_for_client->save();
        SendFcmNotification::send_fcm_notification($notification_title, $notification_message, $notification_for_client, new MyOrdersResources($order));

        SendNotification::order($order->id, 3);
        return redirect()->back()->with('success', 'تمت اعادة مبلغ الطلب للمحفظة بنجاح .');
    }

    public function edit($id)
    {
        $order = Orders::where('id', $id)->with('shipments.cart_items', 'address', 'user')->first();

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

        return view('admin.orders.invoice', compact('object'));
    }
    public function upload_invoice_purchase($id, Request $request)
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
    public function upload_invoice($id, Request $request)
    {
        $order = Orders::where('id', $id)->first();
        $transfer = BankTransfer::where('order_id', $order->id)->first();
        if (!$transfer) {
            $transfer = new BankTransfer();
            $transfer->order_id = $order->id;
            $transfer->user_id = $order->user_id;
        }
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $name = 'transfer-' . time() . '-' . uniqid();
            $destinationPath = 'uploads';
            $fileName = $this->uploadOne($file, $destinationPath, $name);
            $transfer->photo = $fileName;
            $transfer->save();
        }
        return redirect()->back()->with('success', 'تم اضافة صورة التحويل بنجاح');
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
تم إنشاء طلب لك لدى جاك.
لعرض تفاصيل الطلب:
 : ' . url('/i/' . $order->short_code);

        $phone = $this->convertNum(ltrim($user->phone, '0'));
        // $phone_number = '+' . $user->phonecode . $phone;
        // $customer_id = Settings::find(25)->value;
        // $api_key = Settings::find(26)->value;
        // $message_type = "OTP";
        Sms4jawaly::sendSms($smsMessage,  $phone, $user->phonecode?:966);
        // $resp = $this->send4SMS($customer_id, $api_key, $smsMessage, $phone_number, 'GoldenRoad');
        return redirect()->back()->with('success', 'تمت إرسال الفاتورة للعميل .');
    }
    public function convertNum($number)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $english = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
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
    public function send4SMS($oursmsusername, $oursmspassword, $messageContent, $mobileNumber, $senderName)
    {

        $user = $oursmsusername;
        $password = $oursmspassword;
        $sendername = $senderName;
        $text = $messageContent;
        $to = $mobileNumber;

        $getdata = http_build_query(
            $fields = array(
                "username" => $user,
                "password" => $password,
                "message" => $text,
                "numbers" => $to,
                "sender" => $sendername,
                "unicode" => 'e',
                "return" => 'json',
            )
        );

        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'Content-Type: text/html; charset=utf-8',

            ),
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
            'name_ar' => 'required',
            'name_en' => 'required',
            'is_offer' => 'required',
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
        if (count(json_decode($request->products)) == 0 && !$request->is_offer) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'اختر منتج واحد علي الأقل',
                ],
                202
            );
        }

        $page_category = PageCategory::create(['is_offer' => $request->is_offer ? 1 : 0] + $request->all());
        if (!$request->is_offer) {
            foreach (json_decode($request->products) as $product) {
                PageCategoryProduct::create(['product_id' => $product->id, 'page_category_id' => $page_category->id]);
            }
        }
        return \response()->json([
            'status' => 200,
            'url' => url('/admin-panel/page-categories'),
            'message' => 'تم انشاء القسم بنجاح ',
        ]);
    }

    public function purchases_items_user($id)
    {
        $object = User::where('id', $id)
            ->where(['is_archived' => 0, 'approved' => 1])
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')

            ->with(['products' => function ($query) {
                $query->where(['stop' => 0, 'is_archived' => 0]);
                //                   $query ->whereRaw('quantity <= min_warehouse_quantity');//<=
                $query->select(
                    \DB::raw('(SELECT CAST(SUM(IFNULL(quantity,0)) AS UNSIGNED) FROM purchase_items WHERE purchase_items.product_id = products.id) as progress_quantity'),
                    'products.id',
                    'products.provider_id',
                    'products.original_price',
                    'products.title',
                    'products.min_warehouse_quantity',
                    'products.quantity',
                    DB::raw('CAST(0 AS UNSIGNED)  AS qty'),
                    DB::raw('CAST(0 AS UNSIGNED)  AS min_qty')
                );
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])

            ->first();
        $delivery_price = Settings::find(22)->value;
        $object->delivery_price = $delivery_price;
        $object->payment_methods = Purchase_payment_method::all();
        $new = true;
        return view('admin.orders.purchases-items', compact('object', 'new'));
    }

    public function create()
    {
        if (request()->user_id) {
            return redirect('/admin-panel/warehouse-purchases/create/' . request()->user_id);
        }
        return view('admin.orders.add');
    }

    public function search_products(Request $request)
    {
        $data = Products::where(function ($query) {
            $query->where('title', 'LIKE', '%' . request()->search . '%');
            $query->orWhere('description', 'LIKE', '%' . request()->search . '%');
        })->where('is_archived', 0)
            ->select(
                \DB::raw('(SELECT CAST(SUM(IFNULL(quantity,0)) AS UNSIGNED) FROM purchase_items WHERE purchase_items.product_id = products.id) as progress_quantity'),
                'products.id',
                'products.provider_id',
                'products.original_price',
                'products.title',
                'products.min_warehouse_quantity',
                'products.quantity',
                DB::raw('CAST((products.min_warehouse_quantity - products.quantity) AS UNSIGNED)  AS qty'),
                DB::raw('CAST((products.min_warehouse_quantity - products.quantity) AS UNSIGNED)  AS min_qty')
            )
            //            ->select('id','title','description','original_price','quantity','min_quantity')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->distinct()
            ->paginate(20);

        return response()->json(
            [
                'status' => 200,
                'data' => $data,
            ]
        );
    }

    public function search_users(Request $request)
    {
        $this->validate($request, [
            'query' => 'required',
        ]);
        $data = User::where(['user_type_id' => 3, 'approved' => 1, 'is_archived' => 0])
            ->where(function ($query) use ($request) {
                $query->where('phone', 'LIKE', '%' . $request->get('query') . '%');
                $query->orWhere('username', 'LIKE', '%' . $request->get('query') . '%');
            })
            ->select('id', 'username', 'phone')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->limit(5)
            ->get();

        $output = '<ul class="dropdown-menu" id="search_box_result" style="display:block; position:relative;float:none;overflow:hidden;overflow-y: scroll;max-height: 300px;">';
        foreach ($data as $row) {
            $output .= '
       <li class="px-2">
            <a class="text-dark get-user" href="#" data-name="' . $row->username . '" data-id="' . $row->id . '">
                        <img class="mx-1 rounded-circle" src="' . $row->photo . '" width="30" height="30"  />'
                . $row->username . '
           </a>
       </li>
       ';
        }
        if (count($data) == 0) {
            $output .= '<li class="px-2" class="alert alert-danger">
                        لا يوجد مقترحات
                </li>';
        }

        $output .= '</ul>';
        echo $output;
    }

    public function warehouseExportExcel(Request $request)
    {
        return  \Excel::download(new \App\Exports\WarehouseExportExcel(), now()->format('Y-m-d') . "warehouse-orders.xlsx");
    }

    public function warehouseProducts(Request $request)
    {
        $warehouse_products = Products::whereRaw('quantity - min_quantity < min_warehouse_quantity')
            ->select('id', 'quantity', 'min_warehouse_quantity', 'title')
            ->selectRaw('quantity - min_warehouse_quantity as new_quantity')
            ->where('is_archived', 0)
            ->where('stop', 0)
            ->orderBy('new_quantity', 'asc')
            ->take(4)->get();

        return view('admin.orders.warhouse_products', compact('warehouse_products'));
    }
}
