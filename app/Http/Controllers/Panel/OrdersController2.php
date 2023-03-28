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
use App\Models\Settings;
use App\Models\Shipment;
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

class OrdersController extends Controller
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
//        $auth_user=UsersRegions::where('user_id',auth()->id())->pluck('region_id')->toArray();

//        $total= CartItem::where('order_id',301)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
        $objects=Orders::
//            where('added_by',null)
            where('payment_method' ,'<>', 0)
//            ->where('marketed_date','!=',null)
                /*
                 whereIn('region_id', function ($query)  {
                    $query->select('region_id')
                        ->from(with(new UsersRegions())->getTable())
                        ->where('user_id', auth()->id());
                })
                 *
                 * */


            ->when(auth()->user()->user_type_id!=1, function ($query) {
                $query ->whereHas('user', function (Builder $query) {
                    return $query->whereIn('region_id', function ($query)  {
                        $query->select('region_id')
                            ->from(with(new UsersRegions())->getTable())
                            ->where('user_id', auth()->id());
                    });
                });
            })

            ->when(request()->order_id, function ($query) {
                $query->where('id', request()->order_id);
            })
              ->when(!request()->status, function ($query) {
                  $query->where('status', 0);
              })
            ->when(request()->status, function ($query) {
                $query->where('status', request()->status);
            })
            ->latest()
            ->paginate(50);
        return view('admin.orders.all', ['objects' => $objects]);
    }
    public function suppliers_orders()
    {

        $objects=Orders::where('provider_id','!=',null)
            ->where('payment_method' ,'<>', 0)
            ->when(auth()->user()->user_type_id!=1, function ($query) {
                $query ->whereHas('user', function (Builder $query) {
                    return $query->whereIn('region_id', function ($query)  {
                        $query->select('region_id')
                            ->from(with(new UsersRegions())->getTable())
                            ->where('user_id', auth()->id());
                    });
                });
            })
            ->when(request()->order_id, function ($query) {
                $query->where('id', request()->order_id);
            })
              ->when(!request()->status, function ($query) {
                  $query->where('status', 0);
              })
            ->when(request()->status, function ($query) {
                $query->where('status', request()->status);
            })
            ->latest()
            ->paginate(50);
        return view('admin.orders.suppliers-orders', ['objects' => $objects]);
    }
    public function create($token=null)
    {
        $order=Orders::
            with(['user' => function ($query) {
                $query->select('id','username','phone');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');

            }])
            ->where(['status'=>0,'token'=> $token])
            ->where('marketed_date',null)
            ->with('transfer_photo')->first();
        if($token && $order){
            if($order->short_code==null){
                $order->short_code=$order->id.str_random(4);
                $order->save();
            }
        }else{
            $order=Orders::
            with(['user' => function ($query) {
                $query->select('id','username','phone');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');

            }])->create(['token'=> str_random(40),'added_by'=>auth()->id()]);
            $order->short_code=$order->id.str_random(4);
            $order->save();
            if(request()->user_id){

                $user=User::find(request()->user_id);
                if(!$user)abort(404);
                $order->user_id=$user->id;
                $order->save();

                $carts=CartItem::where('order_id',0)->where('type',1)->whereHas('user')->where('status',0)->where('user_id',request()->user_id)
                    ->get();
               foreach ($carts as $user_cart){
                   $new_item = $user_cart->replicate()->fill([
                       'order_id' => $order->id
                   ]);
                   $new_item->save();
               }


                $order=Orders::where('id',$order->id)
                    ->with(['user' => function ($query) {
                    $query->select('id','username','phone');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');

                }])
                    ->where(['status'=>0])
                    ->where('marketed_date',null)->with('transfer_photo')->first();
            }
        }
//        $users=User::where(['user_type_id'=>3,'activate'=>1])->limit(5)->select('id','username as name')->get();

        $cart=\App\Models\CartItem::where('type',1)->where(['order_id'=>$order->id])->whereHas('product')
            ->with(['product' => function ($query) {
//                $query->where('quantity','>',0);
                $query->select('id','title','price','photo','min_quantity','quantity');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');

            }])
            ->get();



        return view('admin.orders.create-order',compact('order','cart'));
    }

    public function select_user(Request $request)
    {
        $order = Orders::where('id', $request->order_id)->first();
//        $order = Orders::where('id', $request->order_id)->where('payment_method', 0)->first();
        $user = User::whereId($request->user_id)->select('id','username','phone','address')->first();
        $order->user_id=$user->id;
        $order->save();
        $addresses=Addresses::where('user_id',$user->id)->orderBy('is_home','desc')->get();
        return \response()->json([
            'status' => 200,
            'message' => 'تم اختيار العميل بنجاح',
            'data' => $addresses,
            'user' => $user
        ]);
    }
    public function select_address(Request $request)
    {
        $order = Orders::where('id', $request->order_id)->first();
//        $order = Orders::where('id', $request->order_id)->where('payment_method', 0)->first();
        if(!$order){
            return \response()->json([
                'status' => 200,
                'message' => 'طلب غير معرف'
            ]);
        }
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
        return \response()->json([
            'status' => 200,
            'message' => 'تم اختيار العنوان بنجاح'
        ]);
    }
    public function select_payment_method(Request $request)
    {
        $order = Orders::where('id', $request->order_id)->first();
        if($request->payment_id==2){
            $order->cart_payment_method = 4;//d بنكي
        }elseif($request->payment_id==3){
            $order->cart_payment_method = 5;//d لاحقا
        }
        else{
         $order->cart_payment_method= 6;//s مدفوع
        }
        $order->save();

        return \response()->json([
            'status' => 200,
            'message' => 'تم اختيار طريقة الدفع بنجاح',
            'order' => $order
        ]);
    }
    public function confirm_order(Request $request)
    {
        $order = Orders::where('id', $request->order_id)->first();
//        $order = Orders::where('id', $request->order_id)->where('payment_method', 0)->first();
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $name = 'transfer-' . time() . '-' . uniqid() ;
            $destinationPath = 'uploads';
            $fileName=$this->uploadOne($file,$destinationPath,$name);
            $transfer=new BankTransfer();
            $transfer->user_id = $order->user_id;
            $transfer->photo = $fileName;
            $transfer->order_id = $order->id;
            $transfer->save();

            if($request->done){
                if($request->payment_id==2){
                    $order->payment_method = 4;//d بنكي
                }elseif($request->payment_id==3){
                    $order->payment_method = 5;//d لاحقا
                }
                elseif($request->payment_id==1){
                    $order->payment_method = 6;//d مدفوع
                }
                $order->status= 1;

            }

        }else{
            if($request->done) {
                if($request->payment_id==2){
                    $order->payment_method = 4;//d بنكي
                }elseif($request->payment_id==3){
                    $order->payment_method = 5;//d لاحقا
                }
                elseif($request->payment_id==1){
                    $order->payment_method = 6;//d مدفوع
                }
                $order->status= 1;
            }
        }

        $order->short_code= $order->id.str_random(4);
        $order->platform='admin-panel';
        if($request->done){
            $order->marketed_date=Carbon::now()->format('Y-m-d h:i:s');
            $order->financial_date = Carbon::now();
            $order->accepted_by=auth()->id();
            $order->status=1;
        }
        $order->save();


        $total= CartItem::where('order_id',$order->id)->where('type',1)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
        $shipment_price=Settings::find(22)->value;
        $taxs=Settings::find(38)->value;
        $order->final_price = $total+$shipment_price+(($total+$shipment_price)*$taxs/100);
        $order->order_price = $total;
        $order->delivery_price = $shipment_price;
        $order->taxes = (($total+$shipment_price)*$taxs/100);
        $order->save();
        CartItem::where('order_id',$order->id)->where('type',1)->update(['user_id'=>$order->user_id]);


        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $objects = CartItem::select('cart_items.shop_id', 'cart_items.user_id',
            'cart_items.type', 'users.username as shop_name', 'users.shipment_price', 'users.taxes', 'users.shipment_days')
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->where('cart_items.order_id', $order->id)
            ->where('cart_items.type', 1)
            ->where('cart_items.user_id', $order->user_id)
            ->groupBy('users.id')->get();
        if ($objects) {
            $order = Orders::find($request->order_id);

            foreach ($objects as $object) {

                $cart_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                    ->where('cart_items.type', 1)
                    ->where('cart_items.order_id', $order->id)
                    ->where('shop_id', $object->shop_id)
                    ->where('cart_items.user_id', $object->user_id)
                    ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                    ->join('products', 'cart_items.item_id', 'products.id')->get();

                $shipment = new OrderShipments();
                $shipment->order_id = $order->id;
                $shipment->user_id = $order->user_id;
                $shipment->shop_id = $object->shop_id;
                $shipment->delivery_date = ' بعد ' . $object->shipment_days . ' يوم';
                $shipment->delivery_date_en = ' after ' . $object->shipment_days . ' days';

                $shipment->delivery_price = $object->shipment_price;
                $shipment->taxes = $object->taxes;
                if($request->done){
                    $shipment->status= 0;
                }
                $shipment->save();
                foreach ($cart_items as $item) {
                    $cart_item = CartItem::find($item->id);
                    if ($cart_item) {
                        $cart_item->order_id = $order->id;
                        $cart_item->shipment_id = $shipment->id;
                        if($request->done){
                            $cart_item->status= 1;
                        }
                        $cart_item->save();
//                        $product = Products::find($item->item_id);
//                        $product->quantity = $product->quantity - $item->quantity;
//                        $product->save();

                    }
                }
                $order->created_at = Carbon::now();
                $order->save();
            }
            return \response()->json([
                'status' => 200,
                'order_id' => $order->id,
                'code' => $order->code,
                'message' => 'تم انشاء الطلب بنجاح'
            ]);
        } else {
            return \response()->json([
                'status' => 400,
                'message' => 'لا يوجد شئ فى السلة'
            ]);

        }
    }


    public function drafts(){
        $objects=Orders::
        where('status',0)
            ->where(function ($query){
                if(auth()->user()->user_type_id==2){
                  $query->where('added_by',auth()->id());
                }
            })
            ->where('token','!=',null)
            ->where('marketed_date',null)
            ->orderBy('updated_at','desc')
            ->paginate(50);
        return view('admin.orders.drafts', ['objects' => $objects]);
    }
    public function incomplete_orders(){
        $objects=CartItem::doesntHave('order')
           ->whereHas('user')->whereHas('cart')->where('status',0)->groupBy('user_id')
            ->orderBy('updated_at','desc')
            ->with('cart','user')
            ->with(['cart.product' => function ($query) {
                $query->select('id', 'title')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');

            }])
            ->where('type',1)
            ->select('*',DB::raw('sum(price * quantity) as total'))
            ->paginate(50);

        return view('admin.orders.incomplete-orders', ['objects' => $objects]);
    }
    public function incomplete_orders_items($id){
        $objects=CartItem::doesntHave('order')->whereHas('user')->where('status',0)->where('user_id',$id)
            ->orderBy('updated_at','desc')
            ->get();

        $total= CartItem::doesntHave('order')->whereHas('user')->where('status',0)->where('user_id',$id)
                ->select(DB::raw('sum(price * quantity) as total'))->first()->total;


        return view('admin.orders.usercart', ['objects' => $objects,'total'=>$total]);
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
        $order->status = 1;
        $order->accepted_by = auth()->id();
        $order->financial_date = Carbon::now();

        $order->save();
        OrderShipments::where('order_id',$order->id)->update(['status'=>1]);
        return redirect()->back()->with('success', 'تمت الموافقة على الطلب بنجاح .');
    }
    public function cancle_order($id = 0, Request $request)
    {
        $order = Orders::whereId($id)->where('added_by',auth()->id())->first();
        $order->delete();
        return redirect()->back()->with('success', 'تمت الحذف بنجاح .');
    }

    public function cancle_client_order($id = 0, Request $request)
    {
        $order = Orders::where('id',$id)->where('status',0)->first();
        if(!$order) {
            return redirect()->back()->with('error', 'طلب غير موجود او تم الغاءه بالفعل .');
        };
        $order->status=5;
        $order->save();
        return redirect()->back()->with('success', 'تمت الالغاء بنجاح .');
    }
    public function approved_shipment($id = 0, Request $request)
    {
        $order = OrderShipments::find($id);
        $order->status = 1;
        $order->save();
        return redirect()->back()->with('success', 'تمت الموافقة على الشحنة بنجاح .');
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
        $order->status = 5;
        $order->save();
        OrderShipments::where('order_id',$order->id)->update(['status'=>5]);
        return redirect()->back()->with('success', 'تمت الغاء الطلب بنجاح .');


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



    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $object=Orders::where('id',$id)
            ->with('shipments.cart_items','address','user')
            ->with(['shipments' => function ($query) {
                $query->whereHas('cart_items');
            }])
            ->when(auth()->user()->user_type_id!=1, function ($query) {
                $query ->whereHas('user', function (Builder $query) {
                    return $query->whereIn('region_id', function ($query)  {
                        $query->select('region_id')
                            ->from(with(new UsersRegions())->getTable())
                            ->where('user_id', auth()->id());
                    });
                });
            })
            ->first();
        if(!$object)abort(404);
        return view ('admin.orders.show',compact('object'));
    }
    public function edit($id)
    {
        $order=Orders::where('id',$id)->with('shipments.cart_items','address','user')
            ->when(auth()->user()->user_type_id!=1, function ($query) {
                $query ->whereHas('user', function (Builder $query) {
                    return $query->whereIn('region_id', function ($query)  {
                        $query->select('region_id')
                            ->from(with(new UsersRegions())->getTable())
                            ->where('user_id', auth()->id());
                    });
                });
            })


//            ->when(auth()->user()->user_type_id!=1, function ($query) {
//                return $query->where('region_id',auth()->user()->region_id)
//                    ->orWhere('added_by',auth()->id());
//            })
            ->first();
        if(!$order)abort(404);

        $object=$order;

        return view ('admin.orders.invoice',compact('object'));
    }
    public function upload_invoice($id,Request $request){
        $order=Orders::where('id',$id)->first();
         $transfer=BankTransfer::where('order_id',$order->id)->first();
         if(!$transfer){
             $transfer=new BankTransfer();
             $transfer->order_id=$order->id;
             $transfer->user_id=$order->user_id;
         }
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $name = 'transfer-' . time() . '-' . uniqid() ;
            $destinationPath = 'uploads';
            $fileName=$this->uploadOne($file,$destinationPath,$name);
            $transfer->photo = $fileName;
            $transfer->save() ;
            $order->payment_method=4;
            $order->save();
            return redirect()->back()->with('success','تم اضافة صورة التحويل بنجاح');
        }else{
            return redirect()->back()->with('error','لا يوجد صورة!');
        }
    }
    public function send_invoice($id){
        $order=Orders::where('id',$id)->first();
        if($order->short_code==null){
            $order->short_code=$order->id.str_random(4);
            $order->save();
        }
        $order->sent_sms=1;
        $order->save();

        $user=User::find($order->user_id);
            $smsMessage = 'مرحباً
تم إنشاء طلب لك لدى الطريق الذهبي.
لعرض تفاصيل الطلب:
 : ' . url('/i/'.$order->short_code);


            $phone = $this->convertNum(ltrim($user->phone, '0'));
            $phone_number = '+' . $user->phonecode . $phone;
            $customer_id = Settings::find(25)->value;
            $api_key = Settings::find(26)->value;
            $message_type = "OTP";

            $resp =$this->send4SMS($customer_id,$api_key,$smsMessage,$phone_number,'GoldenRoad');
            return redirect()->back()->with('success', 'تمت إرسال الفاتورة للعميل .');


    }
    public function convertNum($number){
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $english = [ 0 ,  1 ,  2 ,  3 ,  4 ,  5 ,  6 ,  7 ,  8 ,  9 ];
        return str_replace($arabic,$english, $number);
    }
    public function get_data($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public  function send4SMS($oursmsusername,$oursmspassword,$messageContent,$mobileNumber,$senderName)
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
                "sender"=>$sendername,
                "unicode"=>'e',
                "return"=>'json'
            ));

        $opts = array('http' =>
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
    public function search_users()
    {
        $auth_user=UsersRegions::where('user_id',auth()->id())->pluck('region_id')->toArray();
        $data=User::where(['user_type_id'=>5,'approved'=>1])
             ->where(function($query) {
                 $query->where('phone', 'LIKE', '%'.request()->search.'%');
                 $query->orWhere('username', 'LIKE', '%'.request()->search.'%');
             })
            ->when(auth()->user()->user_type_id!=1, function ($query)use($auth_user) {
                return $query->whereIn('region_id',$auth_user);
            })
            ->select('id','username','phone')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')

            ->get();


        return response()->json(
            [
                'status' => 200,
                'data' => $data,
            ]);
    }
    public function search_products()
    {
        $data=Products::whereHas('user')->where('quantity','>',0)
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
            'order_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status'=>400,
                    'message' => $validator->errors()->first(),
                ], 202
            );
        }
        foreach (json_decode($request->ids) as $id){
            $product=Products::find($id);
            $ifExist=CartItem::where('status',0)->where('type',1)->where('item_id',$product->id)->where('order_id',$request->order_id)->first();
            if($ifExist){
                continue;
            }else{
                $order=Orders::find($request->order_id);

                CartItem::create([
                    'user_id'=>$order->user_id?$order->user_id:0,
                    'shop_id'=>$product->provider_id,
                    'price'=>$product->price,
                    'item_id'=>$product->id,
                    'order_id'=>$request->order_id,
                    'quantity'=>$product->min_quantity,
                    'type'=>1,
                ]);

            }

        }
        $items=CartItem::where('type',1)->where('order_id',$request->order_id)->whereHas('product')
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

        $input=$request->all();
        $items=CartItem::where('id',$request->id)->where('type',1)->first();
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

        $items=CartItem::where('type',1)->where('order_id',$items->order_id)->whereHas('product')
            ->with(['product' => function ($query) {
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
        $item=CartItem::where('id',$request->id)->where('type',1)->first();
        $order_id=$item->order_id;
        $item->delete();

        $items=CartItem::where('type',1)->where('order_id',$order_id)->whereHas('product')
            ->with(['product' => function ($query) {
                $query->select('id','title','price','photo');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');

            }])
            ->get();
        return response()->json([
            'message' => 'تم حذف المنتج من السلة بنجاح',
            'items' =>$items,
        ]);
    }
}
