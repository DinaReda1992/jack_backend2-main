<?php

namespace App\Http\Controllers\Api\Provider;

use FCM;
use Mail;
use JWTAuth;
use \Carbon\Carbon;
use App\Models\Ads;
use App\Models\Cars;
use App\Models\Faqs;
use App\Models\Hall;
use App\Models\Make;
use App\Models\User;
use App\Models\Arbpg;
use App\Models\Banks;
use App\Models\Likes;
use App\Models\Steps;
use App\Models\WhyUs;
use App\Models\Works;
use App\Models\Years;
use App\Http\Requests;
use App\Models\Cities;
use App\Models\JoinUs;
use App\Models\Models;
use App\Models\Orders;
use App\Models\Rating;
use App\Models\Slider;
use App\Models\States;
use App\Models\Stores;
use App\Models\Styles;
use function foo\func;
use App\Models\Answers;
use App\Models\Balance;
use App\Models\Banners;
use App\Models\Content;
use App\Models\Follows;
use App\Models\Museums;
use App\Models\Regions;
use App\Models\Reports;
use App\Models\Sliders;
use App\Models\UserCar;
use App\Models\Articles;
use App\Models\Branches;
use App\Models\CartItem;
use App\Models\Comments;
use App\Models\Contacts;
use App\Models\Favorite;
use App\Models\MakeYear;
use App\Models\Marchant;
use App\Models\Messages;
use App\Models\Packages;
use App\Models\Products;
use App\Models\Projects;
use App\Models\Services;
use App\Models\Settings;
use App\Models\Addresses;
use App\Models\AdsNotify;
use App\Models\AdsOrders;
use App\Models\AdsPhotos;
use App\Models\Companies;
use App\Models\Countries;
use App\Models\FollowCar;
use App\Models\Questions;
use App\Models\CarsModels;
use App\Models\Categories;
use App\Models\Currencies;
use App\Models\DeviceMake;
use App\Models\PayAccount;
use App\Models\Privileges;
use App\Models\WorkPhotos;
use Damas\Paytabs\paytabs;
use App\Models\ClientTypes;
use App\Models\CreditCards;
use App\Models\Restaurants;
use App\Models\Suggestions;
use Illuminate\Support\Str;
use App\Models\BankAccounts;
use App\Models\ContactTypes;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\OrdersStatus;
use App\Models\PricingOrder;
use App\Models\SupplierData;
use Illuminate\Http\Request;
use App\Models\ArticlePhotos;
use App\Models\DeliveryTimes;
use App\Models\Illustrations;
use App\Models\ProductRating;
use App\Models\ProjectOffers;
use App\Models\ProjectPhotos;
use App\Models\Purchase_item;
use App\Models\Subcategories;
use App\Models\ArticleReports;
use App\Models\BlogCategories;
use App\Models\CommentsNotify;
use App\Models\DamageEstimate;
use App\Models\OrderShipments;
use App\Models\PaymentMethods;
use App\Models\Purchase_order;
use App\Models\ServicesPhotos;
use App\Models\SocialProvider;
use App\Models\ActivationCodes;
use App\Models\ArticleComments;
use App\Models\CardsCategories;
use App\Models\CommentsFollows;
use App\Models\MandoobPayments;
use App\Models\ProductMakeYear;
use App\Models\RequestProvider;
use App\Models\ApprovedProjects;
use App\Models\OffersCategories;
use App\Models\SupplierCategory;
use App\Models\BlogSubcategories;
use App\Models\NotificationTypes;
use Illuminate\Http\UploadedFile;
use App\Models\MembershipBenefits;
use App\Models\ServicesCategories;
use Illuminate\Support\Facades\DB;
use \Alhoqbani\SmsaWebService\Smsa;
use App\Models\PurchaseOrderStatus;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdsResources;
use App\Http\Resources\HallResource;
use App\Models\CategoriesSelections;
use App\Models\RestaurantCategories;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\HallsResource;
use App\Http\Resources\UsersResource;
use Illuminate\Support\Facades\Input;
use App\Http\Resources\PurchaseOrders;
use App\Models\SupplierPurcheseStatus;
use LaravelFCM\Message\OptionsBuilder;
use App\Http\Resources\StoresResources;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductResources;
use App\Http\Resources\ProductsResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CompaniesResources;
use App\Models\MeasurementUnitsCategories;
use LaravelFCM\Message\PayloadDataBuilder;
use \Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Resources\AdsDetailsResources;

use App\Http\Resources\CategoriesResources;
use App\Http\Resources\RestuarantResources;


use App\Http\Resources\ShopRatingsResource;
use telesign\sdk\messaging\MessagingClient;
use App\Http\Resources\RestuarantsResources;
use \Alhoqbani\SmsaWebService\Models\Shipper;
use App\Http\Resources\OrderDetailsResources;
use \Alhoqbani\SmsaWebService\Models\Customer;
use \Alhoqbani\SmsaWebService\Models\Shipment;
use App\Http\Resources\ProductRatingsResource;
use App\Http\Resources\OffersCategoriesResources;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Http\Resources\RestuarantCategoriesResources;
use App\Http\Resources\Notifications as NotificationsResource;


class OrdersProviderController extends Controller
{

    public function __construct(Request $request)
    {
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        App::setLocale($language);
        $this->middleware('jwt.auth');
        \Carbon\Carbon::setLocale(App::getLocale());
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                if ($user->block == 1) {
                    JWTAuth::invalidate(JWTAuth::getToken());
                    return response()->json(__('messages.you_are_blocked_from_admin'), 401);
                }
            }
        } catch (TokenExpiredException $e) {
            return "1" . $e;
        } catch (TokenInvalidException $e) {
            return "2" . $e;
        } catch (JWTException $e) {
            return "3" . $e;
        }
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
    public function sendSMS($userAccount, $passAccount, $msg, $numbers, $sender)
    {


        $getdata = http_build_query(
            $fields = array(
                //  "Username" => "s12-".$userAccount,
                "Username" => $userAccount,
                "Password" => $passAccount,
                "Message" => $msg,
                "RecepientNumber" => $numbers,
                "ReplacementList" => "",
                "SendDateTime" => "0",
                "EnableDR" => False,
                "Tagname" => $sender,
                "VariableList" => "0"
            )
        );

        $opts = array(
            'http' =>
            array(
                'method' => 'GET',
                'header' => 'Content-type: application/x-www-form-urlencoded',

            )
        );

        $context = stream_context_create($opts);

        $response = $this->get_data('http://api.yamamah.com/SendSMSV2?' . $getdata, false, $context);


        return $response;
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
    public function saveImageUrl($url)
    {
        if (!$url) {
            return '';
        }
        $contents = file_get_contents($url);
        $userImage = 'file-' . time() . '-' . uniqid() . '-profile.jpg';
        $save_path = "uploads/" . $userImage;
        file_put_contents($save_path, $contents);
        return $userImage;
    }


    public function orders_status()
    {
        $language = request()->headers->get('Accept-Language');
        if ($language == 'ar') {
            $all = ['id' => 0, 'name' => 'الكل', 'btn_text' => ''];
            $name = 'name';
        } else {
            $all = ['id' => 0, 'name' => 'All', 'btn_text' => ''];
            $name = 'name_en as name';
        }
        $data = SupplierPurcheseStatus::whereNotIn('id', [4, 6, 8])->orderBy('sort', 'asc')->select('id', $name, 'btn_text', 'color')
            ->get();
        $data->prepend($all);
        return $data;
    }
    public function users_cart()
    {
        $auth_user = JWTAuth::parseToken()->authenticate();
        $provider_id = $auth_user->user_type_id == 3 ? $auth_user->id : $auth_user->main_provider;
        $data = User::whereHas('cart', function (Builder $query) use ($provider_id) {
            $query->where('provider_id', $provider_id)
                ->where('type', 2)
                ->where('order_id', 0);
        })
            ->select('id', 'username', 'photo')
            ->selectRaw('(CASE WHEN photo = ""  THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->withCount(['cart' => function ($query) use ($provider_id) {
                $query->where('provider_id', $provider_id)
                    ->where('type', 2)
                    ->where('order_id', 0);
            }])
            ->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $data,
            ],
            200
        );
    }
    public function all_orders()
    {
        $auth_user = JWTAuth::parseToken()->authenticate();
        $provider_id = $auth_user->id;
        $users_in_cart = User::whereHas('cart', function (Builder $query) use ($auth_user) {
            $query->whereIn('provider_id', $auth_user->mainSupplierUsers())
                ->where('type', 2)
                ->where('order_id', 0);
        })
            ->count();

        $status = \request()->status;
        $data = Purchase_order::whereIn('provider_id', $auth_user->mainSupplierUsers())

            ->where(function ($query) use ($status) {
                if (\request()->date) {
                    $query->whereDate('created_at', request()->date);
                }
                if (\request()->order_id) {
                    $query->where('id', request()->order_id);
                }
                if ($status == 1) {
                    $query->where('status', 1);
                }
                if ($status == 2) {
                    $query->where('status', 2);
                }
                if ($status == 3) {
                    $query->whereIn('status', [3, 4]);
                }
                if ($status == 5) {
                    $query->where('status', 5);
                }
                if ($status == 7) {
                    $query->whereIn('status', [6, 7, 8]);
                }
            })
            ->select(
                'id',
                'provider_id',
                'final_price',
                'order_price',
                'taxes',
                'delivery_date',
                'delivery_time',
                'provider_delivery_date',
                'provider_delivery_time',
                'delivery_method',
                'transfer_photo',
                'details',
                'payment_terms',
                'status'
            )
            ->selectRaw('(CASE WHEN code = "" THEN "' . "" . '" ELSE (CONCAT ("' . URL::to('/') . '/p/", code)) END) AS invoice_url')
            ->with(['purchase_item.product' => function ($query) {
                $query->select('id', 'title');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->selectRaw('DATE_FORMAT(purchase_orders.delivery_date, "%Y-%m-%d") As delivery_date')

            ->with('orderStatusSupplier', 'purchase_item', 'paymentMethod', 'paymentTerm')
            ->withCount('purchase_item')->latest()->paginate();
        ini_set('serialize_precision', -1);

        $data->{'data'} = PurchaseOrders::collection($data);
        return response()->json(
            [
                'status' => 200,
                'data' => $data,
                'users_in_cart' => $users_in_cart,
                'notification_count' => Notification::whereIn('reciever_id', $auth_user->mainSupplierUsers())->where('status', 0)->orderBy('id', 'DESC')->count(),
            ],
            200
        );
    }

    public function change_order_status(Request $request, $id)
    {
        $order = Purchase_order::find($id);
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'provider_delivery_time' => $order->status == 2 ? 'required' : '',
            'provider_delivery_date' =>  $order->status == 2 ? 'required' : '',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ]
            );
        }
        if ($order->status != $request->status) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'يجب ان تكون حالة الطلب المرسلة تساوي حالة الطلب الحالية',
                ]
            );
        }


        $auth_user = JWTAuth::parseToken()->authenticate();
        $order = Purchase_order::where(function ($query) use ($auth_user) {
            $query->whereIn('provider_id', $auth_user->mainSupplierUsers());
        })->where('id', $id)->first();
        if (!$order) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'هذا الطلب لا يمكنك التعامل معه',
                ]
            );
        }
        if ($order->status > 4) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'هذا الطلب لا يمكنك التعامل معه',
                ]
            );
        } else {
            if ($order->status == 4) {
                $status = 2;
            } else {
                $status = 1;
            }
            if ($order->status == 2) {
                $order->provider_delivery_date = $request->provider_delivery_date;
                $order->provider_delivery_time = $request->provider_delivery_time;
            }
            $order->status = $order->status + $status;
            $order->save();

            $changeStatus = Purchase_order::select('purchase_orders.id', 'purchase_orders.status', 'purchase_orders.updated_at')
                ->where('purchase_orders.id', $order->id)
                ->where('purchase_orders.provider_id', $order->provider_id)
                ->where('purchase_orders.status', '<>', 5)
                ->update(['purchase_orders.status' => $order->status]);
            Purchase_item::where('order_id', $order->id)
                ->where('provider_id', $order->provider_id)
                ->update(['status' => $order->status]);

            $order->refresh();
            $btn_text = OrdersStatus::where('id', $order->status)->first();
            return response()->json(
                [
                    'status' => 200,
                    'new_status' => $order->status,
                    'btn_text' => $btn_text,
                    'status_object' => $btn_text,
                    'message' => __('messages.change_order_status_successfully'),
                ],
                200
            );
        }
    }


    public function store_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ]
            );
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(
                [
                    'status' => 200,
                    'message' => 'لا يوجد عميل بهذا الرقم',
                ],
                201
            );
        }
        $auth_user = JWTAuth::parseToken()->authenticate();

        $provider_id = $auth_user->user_type_id == 3 ? $auth_user->id : $auth_user->main_provider;
        $total = CartItem::where('provider_id', $provider_id)->where('type', 2)->where('user_id', $user->id)->where('order_id', 0)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
        if ($total == null) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'لا يوجد منتجات فى السلة لهذا العميل',
                ],
                200
            );
        }
        $order = new Orders();
        $order->user_id = $user->id;
        $order->provider_id = $provider_id;
        $order->address_id = $request->address_id ?: 0;
        $order->payment_method = 5;
        if ($request->address_id || $request->address_id != "") {
            $address = Addresses::find($request->address_id);
            if (!$address) {
                return \response()->json([
                    'status' => 400,
                    'message' => 'العنوان غير معرف'
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
        $order->status = 0;


        $shipment_price = Settings::find(22)->value;
        $taxs = Settings::find(38)->value;
        $order->final_price = $total + $shipment_price + (($total + $shipment_price) * $taxs / 100);
        $order->order_price = $total;
        $order->delivery_price = $shipment_price;
        $order->cobon_discount = 0;
        $order->taxes = (($total + $shipment_price) * $taxs / 100);
        $order->save();
        CartItem::where('provider_id', $provider_id)->where('type', 2)->where('user_id', $user->id)->where('order_id', 0)->update(['order_id' => $order->id]);



        /**/
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
                'message' => __('messages.order_added_successfully'),
            ],
            200
        );
    }

    public function create_user(Request $request)
    {

        $input = $request->all();
        $input['phone'] = ltrim($request->phone, 0);
        $validator = Validator::make($input, [
            'username' => 'required',
            'phone' => 'required|unique:users,phone|digits:9',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ]
            );
        }

        $auth_user = JWTAuth::parseToken()->authenticate();

        $provider_id = $auth_user->user_type_id == 3 ? $auth_user->id : $auth_user->main_provider;

        $object = new User;
        $object->username = $request->username;
        $object->email = '';
        $object->photo = '';
        $object->main_provider = $provider_id;
        $object->phone = ltrim($request->phone, '0');
        $object->country_id = $request->country_id ?: 188;
        $object->state_id = $request->state_id ?: '';
        $object->region_id = $request->region_id ?: '';

        $object->currency_id = $request->currency_id ?: 1;
        $object->phonecode = $request->phonecode ?: 966;
        $object->address = $request->address ?: '';
        $object->longitude = $request->longitude ?: '';
        $object->latitude = $request->latitude ?: '';
        $object->activate = 0;
        $object->approved = 0;
        $object->user_type_id = 5;
        $object->profit_rate = $request->profit_rate ? $request->profit_rate : '';
        $object->device_type = $request->device_type ? $request->device_type : '';
        $object->accept_pricing = $request->accept_pricing ? 1 : 0;
        $object->accept_estimate = $request->accept_estimate ? 1 : 0;
        $object->add_product = $request->add_product ? 1 : 0;
        $object->shop_type = $request->shop_type ?: 0;
        $object->client_type = $request->client_type ?: 0;
        $object->shipment_id = 1;
        $object->shipment_days = 3;
        $object->save();
        return response()->json([
            'status' => 200,
            'message' => __('messages.user_added_successfully'),
            'data' => $object,
        ]);
    }

    public function search_users(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|digits_between:9,10|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ]
            );
        }



        $auth_user = JWTAuth::parseToken()->authenticate();
        $provider_id = $auth_user->user_type_id == 3 ? $auth_user->id : $auth_user->main_provider;

        $data = User::where(function ($query) use ($provider_id) {
            $query->where(['user_type_id' => 5, 'approved' => 1]);
            $query->orWhere(['user_type_id' => 5, 'approved' => 0, 'main_provider' => $provider_id]);
        })
            ->where(function ($query) use ($request) {
                //                $query->where('phone', 'LIKE', '%0'.$request->get('query').'%');
                //                $query->orWhere('username', 'LIKE', '%'.$request->get('query').'%');
                $query->where('phone', ltrim($request->get('query'), '0'));
                // $query->orWhere('username',$request->get('query'));
            })
            ->select('id', 'username', 'phone')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->limit(10)
            ->get();

        return response()->json(
            [
                'status' => 200,
                'data' => $data,
            ]
        );
    }

    public function get_products()
    {

        $auth_user = JWTAuth::parseToken()->authenticate();
        $provider_id = $auth_user->user_type_id == 3 ? $auth_user->id : $auth_user->main_provider;


        $data = Products::where('products.provider_id', $provider_id)
            ->when(request()->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('title', 'LIKE', '%' . request()->search . '%');
                    $query->orWhere('description', 'LIKE', '%' . request()->search . '%');
                });
            })
            /*->join('cart_items', function ($join) use($provider_id){
            $join->on('products.id', '=', 'cart_items.item_id')
                ->where('cart_items.user_id',request()->user_id)
                ->where('cart_items.order_id',0)->where('cart_items.provider_id',$provider_id);
        })*/
            ->select('products.id', 'products.provider_id', 'products.title', 'products.description', 'products.price', 'min_quantity')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->selectRaw('CAST(IFNULL((SELECT SUM(quantity) FROM cart_items WHERE cart_items.provider_id =' . $provider_id . ' AND cart_items.user_id =' . \request()->user_id . ' AND cart_items.item_id=products.id AND cart_items.order_id=0),0) AS UNSIGNED) as quantity')
            ->selectRaw('CAST(IFNULL((SELECT SUM(quantity) FROM cart_items WHERE  cart_items.item_id=products.id AND cart_items.status > 0 ),0) AS UNSIGNED) as purchases_count')
            ->paginate();

        $cart_count = CartItem::where('provider_id', $provider_id)
            ->where('type', 2)
            ->where('order_id', 0)->where('user_id', \request()->user_id)->count();
        $total = CartItem::where('provider_id', $provider_id)->where('type', 2)->where('user_id', \request()->user_id)->where('order_id', 0)->select(DB::raw('sum(price * quantity) as total'))->first()->total;

        return response()->json(
            [
                'status' => 200,
                'data' => $data,
                'cart_count' => $cart_count,
                'total' => $total == null ? 0 : $total,
            ]
        );
    }


    public function store_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'user_id' => 'required',
            'quantity' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ]
            );
        }

        $auth_user = JWTAuth::parseToken()->authenticate();
        $provider_id = $auth_user->user_type_id == 3 ? $auth_user->id : $auth_user->main_provider;


        $product = Products::find($request->product_id);
        $ifExist = CartItem::where('status', 0)
            ->where('type', 2)
            ->where('user_id', $request->user_id)
            ->where('provider_id', $provider_id)
            ->where('order_id', 0)
            ->where('item_id', $product->id)->first();
        if (!$ifExist && $product->min_quantity <= $request->quantity) {
            CartItem::create([
                'user_id' => $request->user_id,
                'shop_id' => $product->provider_id,
                'price' => $product->price,
                'item_id' => $product->id,
                'order_id' => 0,
                'quantity' => $request->quantity,
                'type' => 2,
                'provider_id' => $provider_id,
            ]);
            //            $message= 'تم الأضافة إلي السلة بنجاح';
            $message = __('messages.cart_added_successfully');
        } else {
            if ($product->min_quantity > $request->quantity) {
                CartItem::where('status', 0)
                    ->where('type', 2)
                    ->where('user_id', $request->user_id)
                    ->where('provider_id', $provider_id)
                    ->where('order_id', 0)
                    ->where('item_id', $product->id)->delete();
                //                $message='تم الحذف من السلة بنجاح';
                $message = __('messages.cart_delete_successfully');
            } else {
                CartItem::where('status', 0)
                    ->where('type', 2)
                    ->where('user_id', $request->user_id)
                    ->where('provider_id', $provider_id)
                    ->where('order_id', 0)
                    ->where('item_id', $product->id)->update(['quantity' => $request->quantity]);
                //                $message= 'تم تعديل الكمية بنجاح';
                $message = __('messages.quantity_update_successfully');
            }
        }

        $items = CartItem::where('type', 2)->where('provider_id', $provider_id)->where('user_id', $request->user_id)->whereHas('product')
            ->where('order_id', 0)
            ->with(['product' => function ($query) {
                $query->select('id', 'title', 'price', 'photo', 'min_quantity');
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            ->get();

        $total = CartItem::where('provider_id', $provider_id)->where('type', 2)->where('user_id', $request->user_id)->where('order_id', 0)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
        $users_in_cart = User::whereHas('cart', function (Builder $query) use ($provider_id) {
            $query->where('provider_id', $provider_id)
                ->where('type', 2)
                ->where('order_id', 0);
        })
            ->count();
        return response()->json([
            'status' => 200,
            'message' => $message,
            'items' => $items,
            'total' => $total == null ? 0 : $total,
            'users_in_cart' => $users_in_cart,
        ]);
    }



    public function update_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'quantity' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ]
            );
        }

        $auth_user = JWTAuth::parseToken()->authenticate();
        $provider_id = $auth_user->user_type_id == 3 ? $auth_user->id : $auth_user->main_provider;


        $items = CartItem::where('item_id', $request->product_id)->where('order_id', 0)
            ->where('type', 2)->where('provider_id', $provider_id)->where('user_id', $request->user_id)->first();
        if (!$items) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'المنتج غير مضاف للسلة',
                ]
            );
        }
        $product = Products::find($request->product_id);
        if (intval($product->min_quantity) > intval($request->quantity)) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => ' اقل كمية متاحة للبيع هي ' . $product->min_quantity,
                    'min_quantity' => $product->min_quantity
                ],
                202
            );
        }

        $items->quantity = $request->quantity;
        $items->save();

        $total = CartItem::where('provider_id', $provider_id)->where('type', 2)->where('user_id', $request->user_id)->where('order_id', 0)->select(DB::raw('sum(price * quantity) as total'))->first()->total;

        return response()->json([
            'message' => __('messages.cart_update_successfully'),
            'items' => $items,
            'total' => $total == null ? 0 : $total,
            'status' => 200,
        ]);
    }


    public function delete_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ]
            );
        }

        $auth_user = JWTAuth::parseToken()->authenticate();
        $provider_id = $auth_user->user_type_id == 3 ? $auth_user->id : $auth_user->main_provider;

        $item = CartItem::where('id', $request->id)->where('user_id', $request->user_id)->where('provider_id', $provider_id)->first();
        $item->delete();


        $total = CartItem::where('provider_id', $provider_id)->where('type', 2)->where('user_id', $request->user_id)->where('order_id', 0)->select(DB::raw('sum(price * quantity) as total'))->first()->total;
        return response()->json([
            'message' => __('messages.cart_delete_successfully'),
            'total' => $total == null ? 0 : $total,
            'status' => 200,
        ]);
    }
    public function delete_cart(Request $request, $id)
    {

        $auth_user = JWTAuth::parseToken()->authenticate();
        $provider_id = $auth_user->user_type_id == 3 ? $auth_user->id : $auth_user->main_provider;

        $item = CartItem::where('user_id', $id)->where('provider_id', $provider_id)->where('status', 0)->delete();


        $users_in_cart = User::whereHas('cart', function (Builder $query) use ($provider_id) {
            $query->where('provider_id', $provider_id)
                ->where('type', 2)
                ->where('order_id', 0);
        })
            ->count();
        return response()->json([
            'message' => __('messages.cart_delete_successfully'),
            'status' => 200,
            'users_in_cart' => $users_in_cart,
        ]);
    }


    public function show_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ]
            );
        }
        $auth_user = JWTAuth::parseToken()->authenticate();
        $provider_id = $auth_user->user_type_id == 3 ? $auth_user->id : $auth_user->main_provider;
        $user = User::whereId($request->user_id)
            ->select('id', 'username', 'phone')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->with('addresses')->first();



        /**/
        $current_items = CartItem::where('user_id', $user->id)->where('type', 2)->where('order_id', 0)->get();
        $messages = [];
        foreach ($current_items as $item) {
            $product = $item->product;
            if ($product->quantity == 0) {
                $item->delete();
                $messages[] = $product->title . ' لم يعد متاح الان ';
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
        }
        /**/


        $cart = CartItem::where('provider_id', $provider_id)->where('user_id', $request->user_id)->where('order_id', 0)

            ->with(['product' => function ($query) {
                $query->select('id', 'title', 'price', 'min_quantity')
                    ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');
            }])
            //            ->select('cart_items.id','cart_items.item_id as product_id','cart_items.provider_id','cart_items.title','cart_items.description','cart_items.price')
            ->get();
        $taxs_price = Settings::find(38)->value;
        $shipment_price = Settings::find(22)->value;
        $total = CartItem::where('provider_id', $provider_id)->where('type', 2)->where('user_id', $request->user_id)->where('order_id', 0)->select(DB::raw('sum(price * quantity) as total'))->first()->total;

        return response()->json([
            'cart' => $cart,
            'user' => $user,
            'tax_percent' => intval($taxs_price),
            'shipment_price' => intval($shipment_price),
            'total' => $total == null ? 0 : $total,
            'status' => 200,
        ]);
    }
}
