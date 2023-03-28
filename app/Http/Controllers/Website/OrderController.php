<?php

namespace App\Http\Controllers\Website;

use App\Models\PaymentSettings;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Banks;
use App\Http\Requests;
use App\Models\Cobons;
use App\Models\Orders;
use App\Models\Balance;
use App\Models\CartItem;
use App\Models\Products;
use App\Models\Settings;
use App\Models\Addresses;
use Illuminate\Support\Str;
use App\Models\BankAccounts;
use App\Models\BankTransfer;
use Illuminate\Http\Request;
use App\Models\OrderShipments;
use App\Models\CobonsProviders;
use App\Models\ProductsRegions;
use App\Models\CobonsCategories;
use Illuminate\Http\UploadedFile;
use App\Services\SendNotification;
use App\Repositories\TapRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\TmaraRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Builder;

class OrderController extends Controller
{

    public function captureOrder($id){
        $order = Orders::find($id);
        $tmara = new TmaraRepository();
        $tmara->capturePayment($order, $order->tmara_order_id, $order->final_price);
    }

    public function checkout($id)
    {
        $user = auth()->user();
        $app_banks = BankAccounts::all();
        $banks = Banks::all();
        $order = Orders::where(['user_id' => \auth()->id(), 'id' => $id])->first();
        $current_balance = Balance::where('user_id', $user->id)->sum('price');

        return view('payment-bank', compact('order', 'banks', 'app_banks', 'current_balance'));
    }

    public function complete_order(Request $request)
    {
        $user = auth()->user();
        $current_balance = Balance::where('user_id', $user->id)->sum('price');
        $user_address = Addresses::where(['user_id' => $user->id, 'is_home' => 1, 'is_archived' => 0])->first();
        $address_id = @$user_address ? $user_address->region_id : 0;

        $current_items = CartItem::where('user_id', $user->id)->where('type', 1)
            ->with(['product' => function ($q) {
                $q->withoutGlobalScope('user');
            }])->where('order_id', 0)->where('type', 1)->get();
        $messages = [];
        foreach ($current_items as $item) {
            $product = $item->product;
            $edit_mount = $product->calculateMinWareHouseQty($item->quantity);
            // dd($edit_mount);
            if ($product->quantity == 0 || $product->stop == 1 || $edit_mount == -1) {
                $item->delete();
                $messages[] = $product->title . ' لم يعد متاح الان ';
            } elseif ($edit_mount == -2) {
                $item->quantity = $product->min_quantity;
                $item->save();
                $messages[] = ' تم تعديل الكمية المطلوبة للمنتج ' . $product->title;
            } elseif ($edit_mount > 0 && $edit_mount != $item->quantity) {
                $item->quantity = $edit_mount;
                $item->save();
                $messages[] = ' تم تعديل الكمية المطلوبة للمنتج ' . $product->title;
            } elseif ($item->quantity > @$product->quantity) {
                $item->quantity = $product->quantity;
                $item->save();
                $messages[] = ' تم تعديل الكمية المطلوبة للمنتج ' . $product->title;
            }
            // if ($product->quantity == 0 || $product->stop==1 ) {
            //     $item->delete();
            //     $messages[] = $product->title . ' لم يعد متاح الان ';
            // }
            if ($address_id != 0) {
                if ($product->has_regions1 == 1) {
                    $pro = ProductsRegions::where('product_id', $product->id)->where('region_id', $address_id)->first();
                    if (!$pro) {
                        $item->delete();
                        $messages[] = $product->title . ' لم يعد متاح الان فى منطقتك الحالية';
                    }
                    $pro = ProductsRegions::where('product_id', $product->id)->where('state_id', $user_address->state_id)->first();
                    if (!$pro) {
                        $item->delete();
                        $messages[] = $product->title . ' لم يعد متاح الان فى مدينتك الحالية';
                    }
                }
                /* $pro=Products::whereId($product->id)->whereHas('product_regions', function (Builder $query)use($address_id) {
                            $query->where('region_id', $address_id);
                        })->first();
                    if($pro){
                        $item->delete();
                        $messages[] = $product->title . ' لم يعد متاح الان فى منطقتك الحالية';
                    }*/
            } elseif ($item->quantity > $product->quantity) {
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
            if ($product->stop == 1) {
                $item->delete();
                $messages[] = ' تم حذف المنتج  ' . $product->title . ' لعدم توفره ';
            }
        }
        /**/


        $cart = \App\Models\CartItem::where('type', 1)
            ->where('user_id', auth()->id())
            ->where(['status' => 0, 'order_id' => 0])
            ->whereHas('product')
            ->with(['product' => function ($query) {
                $query->select('id', 'title', 'photo', 'min_quantity', 'quantity', DB::raw('round(price,2) as price'));
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->select('*', DB::raw('round(price,2) as price'))
            ->get();
        $address = Addresses::where(['user_id' => $user->id, 'is_home' => 1])->first();
        if (!$address) {
            return redirect('/addresses');
        }
        $shipment_price = Settings::find(22)->value;
        $taxs = Settings::find(38)->value;

        if ((int)PaymentSettings::find(1)->value === 1 && !empty($user->phone) && !empty($user->email)) {
            $total = 0;
            foreach ($cart as $item) {
                $total += ($item->price * $item->quantity);
            }
            $total += $shipment_price;

            $total = number_format(($total + ($total * ($taxs / 100))), 2, '.', '');

            $tmara = new TmaraRepository();
            $response = $tmara->getPaymentTypes([
                'total' =>  $total,
                'phone' => '966' . $user->phone
            ]);
        }
        $show_tmara = isset($response) && $response ? 1 : 0;

        return view('complete_order', compact(
            'cart',
            'shipment_price',
            'taxs',
            'address',
            'messages',
            'current_balance',
            'show_tmara'
        ));
    }

    public function addOrder(Request $request)
    {
        $user = auth()->user();
        $order = Orders::where('user_id', $user->id)->where(['payment_method' => 0, 'status' => 0])->where('added_by', null)->latest()->first();
        $order_id = 0;

        $get_balance = $this->withBalance($request, $user, $order);
        if ($get_balance['status'] == 400) {
            return \response()->json($get_balance);
        }
        if (!$order) {
            $order = new Orders();
            $order->user_id = $user->id;
            $order->platform = 'website';
            $order->save();
        }
        if ($request->address_id) {
            $address = Addresses::find($request->address_id);
            $order->longitude = $address->longitude;
            $order->address_name = $address->address;
            $order->address_desc = $address->details;
            $order->country_id = $address->country_id;
            $order->region_id = $address->region_id;
            $order->state_id = $address->state_id;
            if ($order->short_code == null) {
                $order->short_code = $order->id . str_random(4);
            }
        }

        //        $order->final_price = $request->final_price ?: 0;
        //        $order->order_price = $request->order_price ?: 0;
        //        $order->delivery_price = $request->delivery_price ?: 0;

        /**/

        $total = CartItem::where(['order_id' => $order_id, 'user_id' => \auth()->id()])->where('type', 1)->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;
        CartItem::where(['order_id' => $order_id, 'user_id' => $user->id])->update(['calculated' => 1]);
        if (!$total) {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة'
            ]);
        }

        $shipment_price = floatval(Settings::find(22)->value);
        $taxs = Settings::find(38)->value;
        $cobon = 0;
        if ($request->cobon != '') {
            $cobon = floatval($request->cobon_discount);
            $cobon_discount = $this->get_coupon_discount($request);
            // Log::alert(intval($cobon));
            // Log::alert(intval($cobon_discount['money']));
            if (intval($cobon) != intval($cobon_discount['money'])) {
                return \response()->json([
                    'status' => 400,
                    'cobon_discount' => $cobon_discount['money'],
                    'message' => 'قم بعمل تحديث للصفحة'
                ]);
            }
            $cobon = floatval($cobon_discount['money']);
        }
        $tax_price = (($total + $shipment_price - $cobon) * $taxs / 100);
        $order->order_price = $total;
        $order->delivery_price = $shipment_price;
        $order->taxes = $tax_price;

        $order->cobon = $request->cobon ?: '';
        $order->cobon_discount = $cobon > 0 ? $cobon : 0;
        $order->final_price = ($total + $shipment_price - $cobon) + (($total + $shipment_price - $cobon) * $taxs / 100);
        if ($request->is_schedul == 1) {
            $order->is_schedul = 1;
            $order->scheduling_date = $request->scheduling_date;
        } else {
            $order->is_schedul = 0;
            $order->scheduling_date = Null;
        }
        /**/

        $order->address_id = $request->address_id ?: 0;
        $order->status = 0;
        $order->created_at = Carbon::now();
        $order->save();


        $is_min_price = Settings::find(41)->value;
        if ($is_min_price == 1) {
            $min_price_for_order = Settings::find(42)->value;
            $order->refresh();
            if ($order->final_price < floatval($min_price_for_order)) {
                return \response()->json([
                    'status' => 400,
                    'message' => 'الحد الادني للطلب هو ' . $min_price_for_order . ' ريال '
                ]);
            }
        }
        //        return $order;
        $balance = Balance::where('user_id', $user->id)->sum('price');
        if ($request->payment_type == 3) { //balance order
            $options = [
                'order_id' => $order->id,
            ];
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add($options);
            $order_by_balance = $this->sendBalanceOrder($myRequest);
            if ($order_by_balance[0] == 200) {
                $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');
                $order->save();
                return \response()->json([
                    'status' => 200,
                    'order_id' => $order->id,
                    'balance' => $order_by_balance[2],
                    'message' => $order_by_balance[1],
                ]);
            } else {
                return \response()->json([
                    'status' => 400,
                    'message' => $order_by_balance[1],
                ]);
            }
        } elseif ($request->payment_type == 5) { //payment_later
            $options = [
                'order_id' => $order->id,
                'formdata' => true,
                'with_balance' => $request->with_balance,
                'type' => $request->payment_type == 5 ? 'payment_later' : '',
            ];
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add($options);
            $order_by_balance = $this->sendBankTransferOrder($myRequest);
            if ($order_by_balance[0] == 200) {
                $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');
                $order->save();
                return \response()->json([
                    'status' => 200,
                    'order_id' => $order->id,
                    'message' => $order_by_balance[1],
                ]);
            } else {
                return \response()->json([
                    'status' => 400,
                    'message' => $order_by_balance[1],
                ]);
            }
        } elseif ($request->payment_type == 7) { //scheduling payment
            $options = [
                'order_id' => $order->id,
                'formdata' => true,
                'with_balance' => $request->with_balance,
                'type' => $request->payment_type == 7 ? 'scheduling_payment' : '',
            ];
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add($options);
            $order_by_balance = $this->sendBankTransferOrder($myRequest);
            if ($order_by_balance[0] == 200) {
                $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');
                $order->save();
                return \response()->json([
                    'status' => 200,
                    'order_id' => $order->id,
                    'message' => $order_by_balance[1],
                ]);
            } else {
                return \response()->json([
                    'status' => 400,
                    'message' => $order_by_balance[1],
                ]);
            }
        } elseif ($request->payment_type == 2) { //pay online
            $url = '';
            if ($request->with_balance == 1) {
                $url = '?with_balance=1';
            }
            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'url' => url('/payment/' . $order->id . $url),
                'message' => 'تم انشاء الطلب بنجاح وسيتم تحويلك لصفحة الدفع'
            ]);
        } elseif ($request->payment_type == 8) { //pay tmara
            $tmara = new TmaraRepository();
            $response = $tmara->checkout($request , $order->id, $user, 'website');
            
            if ($response && $response !== false) {
                return \response()->json([
                    'status' => 200,
                    'order_id' => $order->id,
                    'url' => $response,
                    'message' => 'تم انشاء الطلب بنجاح وسيتم تحويلك لصفحة الدفع'
                ]);
            }else{
                return \response()->json([
                    'status' => 400,
                    'message' => 'حدث خطأ ما أثناء معالجة بيانات الطلب'
                ]);
            }
        } elseif ($request->payment_type == 9) { //pay tap
            $request = $request->merge(['order_id' => $order->id]);
            $responseTap = (new TapRepository())->checkout($request, 'web', false);
            $responseTap=$responseTap->original;
            if ($responseTap['url']) {
                return \response()->json([
                    'status' => 200,
                    'order_id' => $order->id,
                    'url' => $responseTap['url'],
                    'message' => 'تم انشاء الطلب بنجاح وسيتم تحويلك لصفحة الدفع'
                ]);
            } else {
                return \response()->json([
                    'status' => 400,
                    'message' => 'حدث خطأ ما أثناء معالجة بيانات الطلب'
                ]);
            }
        }
        $order->marketed_date = Carbon::now()->format('Y-m-d h:i:s');
        $order->save();
        return \response()->json([
            'status' => 200,
            'order_id' => $order->id,
            'balance' => $balance,
            'message' => 'تم انشاء الطلب بنجاح وسيتم تحويلك لصفحة الدفع'
        ]);
    }

    public function sendBankTransferOrder(Request $request)
    {
        //        return \response()->json([
        //            'status' => 400,
        //            'message' => 'لا يوجد شئ فى السلة'
        //        ],202);

        /*if ($request->hasFile('photo')){
            $file = $request->file('photo');
            if (!$file->isValid()){
                return \response()->json([
                    'status' => 400,
                    'message' => $file->getErrorMessage()
                ],202);
                return back()->with('error', $file->getErrorMessage());
            }
        }*/
        $user = auth()->user();
        if ($request->type != 'payment_later') {
            $transfer = BankTransfer::where('order_id', $request->order_id)->latest()->first();
            if (!$transfer) {
                $transfer = new BankTransfer();
                $transfer->user_id = $user->id;
                $transfer->order_id = $request->order_id;
            }
            $transfer->bank_id = $request->bank_id;
            $transfer->from_bank_id = $request->from_bank_id;
            $transfer->money_transfered = $request->money_transfered;
            $transfer->account_name = $request->account_name;
            $transfer->account_number = $request->account_number;
            $file = $request->file('photo');
            if ($request->hasFile('photo')) {
                $name = 'transfer-' . time() . '-' . uniqid();
                $destinationPath = 'uploads';
                $fileName = $this->uploadOne($file, $destinationPath, $name);
                $transfer->photo = $fileName;
            }
            $transfer->save();
        }


        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select(
            'cart_items.id',
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.type',
            'users.username as shop_name',
            'users.shipment_price',
            'users.taxes',
            'users.shipment_days'
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where('cart_items.order_id', 0)
            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')->get();
        if ($objects) {
            $order = Orders::find($request->order_id);

            //if pay with balance
            if ($request->with_balance == 1 || $request->with_balance == '1') {
                // Log::alert($request);
                $get_balance = $this->withBalance($request, $user, $order, true);
                if ($get_balance['status'] == 400) {
                    return [400, 'الرصيد غير كافي'];
                    return \response()->json($get_balance);
                } else {
                    // Log::alert($get_balance);
                    $balance = Balance::where(['order_id' => $order->id, 'user_id' => $user->id])->first();
                    if (!$balance) {
                        $balance = new Balance();
                    }
                    $balance->user_id = $user->id;
                    $balance->price = -$get_balance['payed_balance'];
                    $balance->balance_type_id = 12;
                    $balance->order_id = $order->id;
                    $balance->status = 1;
                    $balance->notes = 'استخدام جزئي للمحفظة فى شراء منتجات ' . $order->id;
                    $balance->method_name = 'website-order-sendBankTransferOrder' ;
                    $balance->save();
                    $order->with_balance = 1;
                    $order->save();
                }
            }
            foreach ($objects as $object) {
                $cart_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where('cart_items.order_id', 0)
                    ->where('cart_items.calculated', 1)
                    ->where('shop_id', $object->shop_id)
                    ->where('cart_items.user_id', $object->user_id)
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
            if ($request->type == 'payment_later') {
                $order->payment_method = 5;
            } else {
                $order->payment_method = 4;
            }


            $order->save();
            @$this->send_invoice($order->id);
            SendNotification::newOrder($order->id);
            if ($request->formdata) {
                return [200, 'تم ارسال الطلب بنجاح'];
            }
            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'message' => 'تم ارسال الطلب بنجاح'
            ]);
        } else {
            if ($request->formdata) {
                return [400, 'لا يوجد شئ فى السلة'];
            }
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة'
            ]);
        }
    }

    public function uploadOne(UploadedFile $file, $folder = null, $filename = null, $disk = 'public')
    {
        $name = !is_null($filename) ? $filename : Str::random(25);

        $file->storeAs(
            $folder,
            $name . "." . $file->getClientOriginalExtension(),
            $disk
        );
        return $name . "." . $file->getClientOriginalExtension();
    }

    public function sendBalanceOrder(Request $request)
    {
        $user = auth()->user();
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select(
            'cart_items.shop_id',
            'cart_items.user_id',
            'cart_items.type',
            'users.username as shop_name',
            'users.shipment_price',
            'users.taxes',
            'users.shipment_days'
        )
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where('cart_items.order_id', 0)
            ->where('cart_items.type', 1)
            //            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $user->id)
            ->groupBy('users.id')->get();
        if ($objects) {

            $balance = Balance::where('user_id', $user->id)->sum('price');
            $order = Orders::find($request->order_id);

            if (!$order) {
                return [400, 'لا يوجد طلب بهذا الرقم'];
                return \response()->json([
                    'status' => 400,
                    'message' => 'لا يوجد طلب بهذا الرقم'
                ]);
            }
            if ($balance < $order->final_price) {
                return [400, 'الرصيد غير كافى'];
                return \response()->json([
                    'status' => 400,
                    'message' => 'الرصيد غير كافى'
                ]);
            }
            foreach ($objects as $object) {

                $cart_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where('cart_items.order_id', 0)
                    ->where('cart_items.calculated', 1)
                    ->where('shop_id', $object->shop_id)
                    ->where('cart_items.user_id', $object->user_id)
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
                        //                        $product = Products::find($item->item_id);
                        //                        $product->quantity = $product->quantity - $item->quantity;
                        //                        $product->save();

                    }
                }

                //                $notification55 = new Notification();
                //                $notification55->sender_id = $user->id;
                //                $notification55->reciever_id = $object->shop_id;
                //                $notification55->ads_id = $shipment->id;
                //                $notification55->type = 13;
                //                $notification55->url = "/provider-panel/order-details/" . $shipment->id;
                //                $notification55->message = "قام " . $user->username . " بشراء منتجات من متجرك ";
                //                $notification55->message_en = @$user->username . " bought products from your shop.";
                //                $notification55->save();

            }
            $order->payment_method = 3;
            $order->save();
            $balance = new Balance();
            $balance->user_id = $user->id;
            $balance->price = $order->final_price * -1;
            $balance->status = 1;
            $balance->balance_type_id = 3;
            $balance->order_id = $order->id;
            $balance->notes = 'شراء من السله لطلب رقم ' . $order->id;
            $balance->save();
            $new_balance = Balance::where('user_id', $user->id)->sum('price');
            @$this->send_invoice($order->id);
            SendNotification::newOrder($order->id);
            return [200, 'تم ارسال الطلب بنجاح', $new_balance];

            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'balance' => $new_balance,
                'message' => 'تم ارسال الطلب بنجاح'
            ]);
        } else {
            return [400, 'لا يوجد شئ فى السلة'];
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة'
            ]);
        }
    }

    public function get_coupon_discount($request)
    {
        $user = \auth()->user();
        $code = Cobons::where('code', $request->cobon)->first();
        if ($code) {
            $date_of_end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($code->created_at)) . " +" . $code->days . " days"));
            if (date('Y-m-d') > $date_of_end) {
                return [
                    'status' => 400,
                    'message' => "عفوا انتهت صلاحية الكوبون",
                ];
            }

            $count_used = Orders::where('cobon', $request->cobon)
                ->where('payment_method', '<>', 0)->where('status', '<>', 5)->count();
            //            $used_cobons = Orders::where('cobon', $request->code)->whereIn('status', [1, 2])->count();
            if ($code->usage_quota <= $count_used) {
                return [
                    'status' => 400,
                    'message' => __('messages.coupon_used_before'),
                ];
            }

            $count_used = Orders::where('user_id', $user->id)->where('cobon', $request->cobon)->where('payment_method', '<>', 0)->where('status', '<>', 5)->count();
            if ($code->usage_quota <= $count_used) {
                return [
                    'status' => 400,
                    'message' => 'عفوا انتهت صلاحية الكوبون',
                ];
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

                //                  ->selectRaw('(SELECT sum(cart_items.quantity*cart_items.price)
                //                     FROM cart_items where cart_items.item_id=products.id) as total_item_price')

                ->where('cart_items.order_id', 0)
                ->where('type', 1)
                ->where('cart_items.user_id', $user->id)->first();
            $total = $discount_prices ? $discount_prices->total : 0;
            /*  if($discount_prices > $code->max_money && $code->max_money!=0){
                  $total=$code->max_money;
              }*/
            //                return response()->json($discount_prices);

            //                $total= CartItem::where(['order_id'=>0,'user_id'=>\auth()->id()])->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))
            //                    ->where('type',1)
            //                    ->first()->total;
            //            $shipment_price=Settings::find(22)->value;
            $shipment_price = 0;
            $total = $total + $shipment_price;
            $percent = $code->percent;

            $final_percent_price = ($total * $percent) / 100; // الخصم بالنسبه

            $final_money_price = $code->max_money; //اعلي مبلغ خصم

            if ($final_percent_price >= $final_money_price && $code->max_money != 0) {
                $final_cobon_money = $final_money_price;
            } else {
                $final_cobon_money = $final_percent_price;
            }
            //            if( $final_money_price==0){
            //                $final_cobon_money=$final_percent_price;
            //            }

            if ($final_cobon_money == 0) {
                return [
                    'status' => 200,
                    'message' => __('messages.coupon_not_fount_for_provider'),
                    'money' => $final_cobon_money

                ];
            }

            return [
                'status' => 200,
                'message' => __('messages.coupon_is_available'),
                'money' => $final_cobon_money
            ];
        } else {
            return [
                'status' => 400,
                'message' => __('messages.coupon_not_fount'),
            ];
        }
    }

    public function check_coupon_category(Request $request)
    {
        $user = \auth()->user();
        $code = Cobons::where('code', $request->code)->first();
        if ($code) {
            $date_of_end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($code->created_at)) . " +" . $code->days . " days"));
            if (date('Y-m-d') > $date_of_end) {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => "عفوا انتهت صلاحية الكوبون",
                    ]
                );
            }

            $count_used = Orders::where('cobon', $request->code)
                ->where('payment_method', '<>', 0)->where('status', '<>', 5)->count();
            //            $used_cobons = Orders::where('cobon', $request->code)->whereIn('status', [1, 2])->count();
            if ($code->usage_quota <= $count_used) {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => __('messages.coupon_used_before'),
                    ]
                );
            }

            $count_used = Orders::where('user_id', $user->id)->where('cobon', $request->code)->where('payment_method', '<>', 0)->where('status', '<>', 5)->count();
            if ($code->usage_quota <= $count_used) {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => 'عفوا انتهت صلاحية الكوبون',
                    ]
                );
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

                //                  ->selectRaw('(SELECT sum(cart_items.quantity*cart_items.price)
                //                     FROM cart_items where cart_items.item_id=products.id) as total_item_price')

                ->where('cart_items.order_id', 0)
                ->where('type', 1)
                ->where('cart_items.user_id', $user->id)->first();
            $total = $discount_prices ? $discount_prices->total : 0;
            /*  if($discount_prices > $code->max_money && $code->max_money!=0){
                  $total=$code->max_money;
              }*/
            //                return response()->json($discount_prices);

            //                $total= CartItem::where(['order_id'=>0,'user_id'=>\auth()->id()])->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))
            //                    ->where('type',1)
            //                    ->first()->total;
            //            $shipment_price=Settings::find(22)->value;
            $shipment_price = 0;
            $total = $total + $shipment_price;
            $percent = $code->percent;

            $final_percent_price = ($total * $percent) / 100; // الخصم بالنسبه

            $final_money_price = $code->max_money; //اعلي مبلغ خصم

            if ($final_percent_price >= $final_money_price && $code->max_money != 0) {
                $final_cobon_money = $final_money_price;
            } else {
                $final_cobon_money = $final_percent_price;
            }
            //            if( $final_money_price==0){
            //                $final_cobon_money=$final_percent_price;
            //            }

            if ($final_cobon_money == 0) {
                return response()->json(
                    [
                        'status' => 400,
                        'message' => __('messages.coupon_not_fount_for_provider'),
                    ]
                );
            }

            return response()->json(
                [
                    'status' => 200,
                    'message' => __('messages.coupon_is_available'),
                    'money' => $final_cobon_money
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => 400,
                    'message' => __('messages.coupon_not_fount'),
                ]
            );
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
فاتورة طلبك  لدى الطريق الذهبي
 : ' . url('/i/' . $order->short_code);


        $phone = $this->convertNum(ltrim($user->phone, '0'));
        $phone_number = '+' . $user->phonecode . $phone;
        $customer_id = Settings::find(25)->value;
        $api_key = Settings::find(26)->value;
        $message_type = "OTP";

        $resp = @$this->send4SMS($customer_id, $api_key, $smsMessage, $phone_number, 'GoldenRoad');
        //       @Log::alert($resp);
        //        return redirect()->back()->with('success', 'تمت إرسال الفاتورة للعميل .');


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


    public function withBalance($request, $user, $order, $ifByOrder = false)
    {
        if ($request->with_balance == 1 || $request->with_balance == '1' || intval($request->with_balance) == 1) {
            //            $total = CartItem::where(['order_id' => 0, 'user_id' => $user->id])
            //                ->orWhere(['order_id' => $order->id, 'user_id' => $user->id])
            //                ->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;
            $total = CartItem::where(function ($q) use ($ifByOrder, $order, $user) {
                if ($ifByOrder) {
                    $q->where(['order_id' => $order->id, 'user_id' => $user->id]);
                    $q->orWhere(['order_id' => 0, 'user_id' => $user->id]);
                } else {
                    $q->where(['order_id' => 0, 'user_id' => $user->id]);
                }
            })->where('type', 1)
                ->select(\Illuminate\Support\Facades\DB::raw('sum(price * quantity) as total'))->first()->total;

            if ($ifByOrder) {
                $total = $order->final_price;
            }
            $money_transfered = 0;
            if ($request->money_transfered) {
                $money_transfered = round(floatval($request->money_transfered), 2);
            }
            $get_balance = Balance::where('user_id', $user->id)->sum('price');
            $payed_balance = $total - $money_transfered; //100 -10
            if ($get_balance < $payed_balance) {
                $payed_balance = $get_balance;
            }
            if ($get_balance <= 0) {
                return [
                    'status' => 400,
                    'message' => 'الرصيد غير كافي',
                    'get_balance' => $get_balance,
                    'payed_balance' => $payed_balance,
                    'total' => $total,
                    'money_transfered' => $money_transfered,
                ];
            }

            if ($get_balance < $payed_balance) {
                return [
                    'status' => 400,
                    'message' => 'الرصيد غير كافي',
                    'get_balance' => $get_balance,
                    'payed_balance' => $payed_balance,
                    'total' => $total,
                    'money_transfered' => $money_transfered,
                ];
            } else {
                return [
                    'status' => 200,
                    'get_balance' => $get_balance,
                    'payed_balance' => $payed_balance,
                    'total' => $total,
                    'money_transfered' => $money_transfered,
                ];
            }
        }
        return [
            'status' => 200,
        ];
    }

    public function orderDetails($id)
    {
        $user = auth()->user();
        $order = Orders::where('user_id', $user->id)->where('is_edit', 1)->find($id);

        if (!$order) {
            return redirect('/')->with('error', 'لا يوجد طلبات متبقية');
        }
        ini_set('serialize_precision', -1);
        return view('order-details', ['shop' => json_encode($order->orderDetails()['data']), 'order_id' => $id] + $order->orderDetails());
    }

    public function addNewOrderOnOldOrder($id)
    {
        $user = auth()->user();
        $order = Orders::where('user_id', $user->id)->where('is_edit', 1)->find($id);

        if (!$order) {
            return redirect('/')->with('error', 'لا يوجد طلبات متبقية');
        }

        $order->newOrderOnOld();

        return redirect('/')->with('success', 'تم اعادة الطلب بنجاح');
    }

    public function returnBalance($id)
    {
        $user = auth()->user();
        $order = Orders::where('user_id', $user->id)->where('is_edit', 1)->find($id);

        if (!$order) {
            return redirect('/')->with('error', 'لا يوجد طلبات متبقية');
        }

        $order->returnBalance();

        return redirect('/')->with('success', 'تم اضافة رصيد الي حسابك');
    }
}
