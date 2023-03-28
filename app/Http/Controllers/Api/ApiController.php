<?php

namespace App\Http\Controllers\Api;

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
use App\Models\PricingOrder;
use App\Models\SupplierData;
use Illuminate\Http\Request;
use App\Models\ArticlePhotos;
use App\Models\DeliveryTimes;
use App\Models\Illustrations;
use App\Models\ProductRating;
use App\Models\ProjectOffers;
use App\Models\ProjectPhotos;
use App\Models\Subcategories;
use App\Models\ArticleReports;
use App\Models\BlogCategories;
use App\Models\CommentsNotify;
use App\Models\DamageEstimate;
use App\Models\MainCategories;
use App\Models\OrderShipments;
use App\Models\PaymentMethods;
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
use App\Models\ProductCategories;
use Illuminate\Http\UploadedFile;
use App\Models\MembershipBenefits;
use App\Models\ServicesCategories;
use Illuminate\Support\Facades\DB;
use \Alhoqbani\SmsaWebService\Smsa;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
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
use LaravelFCM\Message\OptionsBuilder;
use App\Http\Resources\StoresResources;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductResources;
use App\Http\Resources\ProductsResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use MFrouh\Sms4jawaly\Facades\Sms4jawaly;
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


class ApiController extends Controller
{

    public function __construct(Request $request)
    {
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        App::setLocale($language);
        \Carbon\Carbon::setLocale(App::getLocale());
    }

    public function initiatePayment(Request $request)
    {
        $card_number = $request->card_number;

        $expiry_month = $request->expiry_month;
        $expiry_year = $request->expiry_year;
        $cvv = $request->cvv;
        $card_holder = $request->holder_name;
        $amount = $request->amount;
        $order_id = $request->order_id;
        $order_type = $request->order_type;

        //$this->paymentResultss($order_id,$order_type);
        $arbPg = new Arbpg();


        $url = $arbPg->getmerchanthostedPaymentid($card_number, $expiry_month, $expiry_year, $cvv, $card_holder, $order_id, $order_type, $amount);
        return response()->json($url);


        return response()->redirectTo($url, 302);
    }
    //    public function paymentResultss($order_id,$order_type){
    //
    ////        var_dump($trandata);
    //
    ////       return response()->json($result);-
    //            if($order_type==1){
    //                return $this->shopPayment($order_id);
    //            }
    //            elseif ($order_type==2){
    //                return $this->pricingSendPaymentOrder($order_id);
    //            }
    //            elseif ($order_type==3){
    //                return $this->damageSendPaymentOrder($order_id);
    //            }
    //
    //
    //        return redirect('/api/v1/payment-error');
    //
    //    }

    public function paymentResult(Request $request)
    {

        $trandata = $request->trandata;
        //        var_dump($trandata);
        $arbPg = new Arbpg();

        $result = $arbPg->getresult($trandata);
        //       return response()->json($result);-
        if ($result['status'] == 'success') {
            if ($result['orderType'] == 1) {
                return $this->shopPayment($result['orderId']);
            } elseif ($result['orderType'] == 2) {
                return $this->pricingSendPaymentOrder($result['orderId']);
            } elseif ($result['orderType'] == 3) {
                return $this->damageSendPaymentOrder($result['orderId']);
            }
        }

        return redirect('/api/v1/payment-error');
    }

    public function testSmsa()
    {
        $passKey = 'Tat@2036';
        $smsa = new Smsa($passKey);
        $smsa->shouldUseExceptions = false; // Disable throwing exceptions by the library

        $cities = $smsa->cities();
        //foreach ($cities->data as $city){
        //    $state=States::where('name_en',$city['name'])->first();
        //    if(!$state){
        //        $new_state=new States();
        //        $new_state->name_en=$city['name'];
        //        $new_state->country_id=188;
        //        $new_state->routeCode=$city['routeCode'];
        //        $new_state->save();
        //    }else{
        //        if($state->routeCode==""){
        //            $state->routeCode=$city['routeCode'];
        //            $state->save();
        //        }
        //    }
        //}
        if ($cities->success) {
            return response()->json($cities->data);
        } else {
            return response()->json(['status' => 400, errors => $cities->error]);
        }
    }

    public function getContactOptions()
    {
        $contacts = Settings::select('option_name', 'name', 'value')->where('input_type', 'contact_options')
            ->orWhere('input_type', 'fees')
            ->orWhereIn('id', [22, 38])
            ->orWhere('input_type', 'app_links')
            ->get();
        return response()->json($contacts);
    }

    public function login(Request $request)
    {
        $phone = $this->convertNum($request->phone);
        $phone1 = $this->convertNum(ltrim($phone, '0'));
        //        return $this->convertNum($phone1);
        $phone2 = "0" . $phone;
        $phonecode = '966';
        $is_sms_active = Settings::find(24)->value;

        if (User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1, $phone2])->where('block', 1)->first()) {
            return response()->json([
                'message' => trans('messages.you_are_blocked'),
                // 'message'=> 'تم حذف حسابك'
            ], 400);
        } elseif (User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1, $phone2])->where('block', 0)->first()) {
            $user = User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1, $phone2])->where('block', 0)->first();
            //            $user->activate = 0;
            $user->save();
            $activation = ActivationCodes::where('user_id', $user->id)->whereIn('phone', [$phone, $phone1, $phone2])->first();
            if ($activation) {
                if ($phone == "123456789") {
                    $activation->activation_code = 1234;
                } else {
                    $activation->activation_code = mt_rand(1000, 9999);
                }

                $activation->save();
            } else {
                $activation = new ActivationCodes();
                $activation->user_id = $user->id;
                $activation->phonecode = $request->phonecode ?: 966;
                $activation->phone = $phone;
                if ($phone == "123456789") {
                    $activation->activation_code = 1234;
                } else {
                    $activation->activation_code = mt_rand(1000, 9999);
                }
                $activation->save();
            }
            if ($is_sms_active == '0') {

                $smsMessage = 'كود تفعيل حسابك فى تطبيق جاك : ' . $activation->activation_code;
                $final_num = $this->convertNum(ltrim($user->phone, '0'));

                // $phone_number = '+' . $user->phonecode . $final_num;
                // $customer_id = Settings::find(25)->value;
                // $api_key = Settings::find(26)->value;
                Sms4jawaly::sendSms($smsMessage, $final_num, $phonecode);
                // $resp = $this->send4SMS($customer_id, $api_key, $smsMessage, $phone_number, 'GoldenRoad');
            }
            // if no errors are encountered we can return a JWT
            return response()->json([
                //                'login_status' => 1,
                'message' => trans('messages.please_activate_your_phone'),
                'sms_active' => (int)$is_sms_active,

                //                'code' => $send_sms_response,
                'activation_code' => $activation->activation_code,
                // 'activation_code' => '',
                //                'lang'=>App::getLocale()
            ], 200);
        } else {

            if ($activation = ActivationCodes::where('phonecode', $phonecode)->whereIn('phone', [$phone1, $phone, $phone2])->first()) {
                $activation->activation_code = mt_rand(1000, 9999);
                $activation->save();
            } else {
                $activation = new ActivationCodes();
                $activation->phonecode = '966';
                $activation->phone = $phone;
                $activation->activation_code = mt_rand(1000, 9999);
                $activation->save();
            }
            if ($is_sms_active == '0') {

                $smsMessage = 'كود تفعيل حسابك فى تطبيق جاك : ' . $activation->activation_code;
                // $customer_id = Settings::find(25)->value;
                // $api_key = Settings::find(26)->value;
                $final_num = $this->convertNum(ltrim($activation->phone, '0'));
                // $phone_number = '+' . $activation->phonecode . $final_num;
                Sms4jawaly::sendSms($smsMessage, $final_num, $phonecode);
                // $resp = $this->send4SMS($customer_id, $api_key, $smsMessage, $phone_number, 'GoldenRoad');
            }

            return response()->json([
                //                'login_status' => 0,
                'message' => trans('messages.please_activate_your_phone'),
                'sms_active' => (int)$is_sms_active,

                //                'code' => $send_sms_response,
                'activation_code' => $activation->activation_code
            ], 200);
        }
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

    public function activate(Request $request)
    {
        $activation_code = $request->activation_code;
        $phone = $this->convertNum($request->phone);
        $phone1 = $this->convertNum(ltrim($phone, '0'));
        $phone2 = "0" . +$phone1;
        $phonecode = 966;


        $activation = ActivationCodes::where('activation_code', $activation_code)->whereIn('phone', [$phone, $phone1, $phone2])->where('phonecode', $phonecode)->first();
        if (!$activation) {
            return response()->json(
                [
                    'message' => trans('messages.error_code'),
                ],
                400
            );
        } else {
            $this_user1 = User::
                //            where('phonecode', $phonecode)->
                whereIn('phone', [$phone, $phone1, $phone2])->first();
            //            return response()->json($phone2);
            if ($this_user1) {

                $user = $this_user1;
                $this_user = User::where('id', $user->id)
                    ->select('*')
                    ->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo')
                    ->first();
                $activation->user_id = $user->id;
                $activation->save();
                //              $this_user -> username = $this_user -> username == null ?  '' : $this_user -> username;
                $this_user->last_login = date('Y-m-d H:i:s');
                $this_user->device_token = $request->device_token;
                if ($request->device_type) {
                    $this_user->device_type = $request->device_type;
                }
                $this_user->save();

                if ($request->device_token) {
                    if ($device = DeviceTokens::where('device_token', $request->device_token)->first()) {
                        $device->user_id = $this_user->id;
                        $device->save();
                    } else {
                        $device = new DeviceTokens();
                        $device->device_token = $request->device_token;
                        $device->user_id = $this_user->id;
                        $device->save();
                    }
                }
                $token = JWTAuth::fromUser($this_user);
                $this_user->token = $token;
                $this_user->phonecode = 966;
                $this_user->activate = 1;
                $this_user->save();

                $items = json_decode($request->items);
                if ($items && count($items) > 0) {
                    foreach ($items as $item) {
                        if (!empty($item) && $item->product_id) {
                            $product = Products::find($item->product_id);

                            if ($product && $product->quantity > 0) {
                                $cart_item = CartItem::where('item_id', $product->id)->where('type', 1)
                                    ->where('order_id', 0)->where('user_id', $this_user->id)->first();
                                $mount = $item->quantity;
                                if ($cart_item) {
                                    $mount = $mount + $cart_item->quantity;
                                } else {
                                    $cart_item = new CartItem();
                                }
                                if ($mount > $product->quantity) {
                                    $mount = $product->quantity;
                                }

                                $cart_item->item_id = $item->product_id;
                                $cart_item->shop_id = $product->provider_id;

                                $cart_item->user_id = $this_user->id;
                                $cart_item->quantity = $mount;
                                $cart_item->type = 1;
                                $cart_item->price = $item->price ?: $product->price_after_discount;
                                $cart_item->save();
                            }
                        }
                    }
                }

                //                return response()->json(
                //                    [
                //                        'status' => 200,
                //                        'message' => 'تم اضافة المنتجات الى السلة بنجاح',
                //                    ]
                //);

                return response()->json(new UsersResource($this_user), 200);
            } else {
                $activation->activate = 1;
                $activation->save();
                $select_name = App::getLocale() == "ar" ? 'name' : 'name_en as name';
                $regions = Regions::select('id', $select_name)
                    ->with(['getStates' => function ($query) use ($select_name) {
                        $query->select('id', 'region_id', $select_name);
                    }])->where('country_id', 188)->where('is_archived', 0)->get();
                $client_types = ClientTypes::select('id', $select_name)->get();
                return response()->json(
                    [
                        'message' => trans('messages.go_to_register_page'),
                        'regions' => $regions,
                        'client_types' => $client_types
                    ],
                    202
                );
            }
        }
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

    public function addToCartFromLocal(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $items = json_decode($request->items);
        if ($items && count($items) > 0) {
            foreach ($items as $item) {
                if (!empty($item) && $item->product_id) {
                    $product = Products::find($item->product_id);

                    if ($product && $product->quantity > 0) {
                        $cart_item = CartItem::where('item_id', $product->id)->where('type', 1)
                            ->where('order_id', 0)->where('user_id', $user->id)->first();
                        $mount = $item->quantity;
                        if ($cart_item) {
                            $mount = $mount + $cart_item->quantity;
                        } else {
                            $cart_item = new CartItem();
                        }
                        if ($mount > $product->quantity) {
                            $mount = $product->quantity;
                        }

                        $cart_item->item_id = $item->product_id;

                        $cart_item->user_id = $user->id;
                        $cart_item->quantity = $mount;
                        $cart_item->type = 1;
                        $cart_item->price = $item->price ?: $product->price_after_discount;
                        $cart_item->save();
                    }
                }
            }
        }

        return response()->json(
            [
                'status' => 200,
                'message' => 'تم اضافة المنتجات الى السلة بنجاح',
            ]
        );
    }

    public function register(Request $request)
    {

        if ($request->email = '') {
            $input = $request->except('email');
        } else {
            $input = $request->all();
        }
        $phone = ltrim($request->phone, '0');
        $phone1 = $this->convertNum(ltrim($phone, '0'));
        $phone2 = "0" . $phone;
        $input['phone'] = $phone1;
        $input['phonecode'] = 966;

        $validator = Validator::make($input, [
            'username' => 'required',
            'phone' => 'required|unique:users,phone',
            'email' => 'required|unique:users,email',
            //            'country_id' => 'required',
            'region_id' => 'required',
            'state_id' => 'required',
            'client_type' => 'required',
            //            'commercial_no' => 'required',
            //            'commercial_end_date' => 'required',
            //            'longitude'=>'required',
            //            'latitude'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                200
            );
        }
        $phone = ltrim($request->phone, 0);
        /* return response()->json(
             [
                 'status'=>400,
                 'message' =>[$phone,$phone1,$phone2],
             ], 200
         );*/
        $ifUser = ActivationCodes::whereIn('phone', [$phone, $phone1, $phone2])->where('activate', 1)->first();
        if (!$ifUser) {
            return response()->json(
                [
                    'status' => 402,
                    'message' => __('messages.incorrect_login_data'),
                ],
                200
            );
        }

        $input['password'] = Hash::make('secret');
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $name = 'profile-' . time() . '-' . uniqid();
            $destinationPath = 'uploads';
            $fileName = $this->uploadOne($file, $destinationPath, $name);
            $input['photo'] = $fileName;
        }
        $file2 = $request->file('commercial_id');
        if ($request->hasFile('commercial_id')) {
            $name = 'commercial_id-' . time() . '-' . uniqid();
            $destinationPath = 'uploads';
            $fileName = $this->uploadOne($file2, $destinationPath, $name);
            $input['commercial_id'] = $fileName;
        }
        $input['user_type_id'] = 5;
        $input['country_id'] = 188;
        $input['activate'] = 1;
        $user = User::create($input);

        //        if ($request->device_type) {
        //            $user->device_type = $request->device_type;
        //                    $user->save();
        //
        //        }
        //$user->commercial_no=$request->commercial_no?:'';
        //        $user->commercial_end_date=$request->commercial_end_date?:'';
        //        $user->tax_number=$request->tax_number?:'';
        //        $user->longitude=$request->longitude?:'';
        //        $user->latitude=$request->latitude?:'';
        //        $user->client_type=$request->client_type;

        //        $user->save();
        $user = User::where('id', $user->id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo')
            ->first();

        $activation_code = ActivationCodes::whereIn('phone', [$user->phone, ltrim($user->phone, 0)])->first();
        if ($activation_code) {
            $activation_code->user_id = $user->id;
            $activation_code->save();
        }
        $token = JWTAuth::fromUser($user);
        $user->token = $token;
        $user->save();
        $items = json_decode($request->items);
        if ($items && count($items) > 0) {
            foreach ($items as $item) {
                if (!empty($item) && $item->product_id) {
                    $product = Products::find($item->product_id);

                    if ($product && $product->quantity > 0) {
                        $cart_item = CartItem::where('item_id', $product->id)->where('type', 1)
                            ->where('order_id', 0)->where('user_id', $user->id)->first();
                        $mount = $item->quantity;
                        if ($cart_item) {
                            $mount = $mount + $cart_item->quantity;
                        } else {
                            $cart_item = new CartItem();
                        }
                        if ($mount > $product->quantity) {
                            $mount = $product->quantity;
                        }

                        $cart_item->item_id = $item->product_id;
                        $cart_item->shop_id = $product->provider_id;

                        $cart_item->user_id = $user->id;
                        $cart_item->quantity = $mount;
                        $cart_item->type = 1;
                        $cart_item->price = $item->price ?: $product->price_after_discount;
                        $cart_item->save();
                    }
                }
            }
        }

        if ($request->device_token) {
            if ($device = DeviceTokens::where('device_token', $request->device_token)->first()) {
                $device->user_id = $user->id;
                $device->save();
            } else {
                $device = new DeviceTokens();
                $device->device_token = $request->device_token;
                $device->user_id = $user->id;
                $device->save();
            }
        }
        return response()->json(new UsersResource($user), 200);
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


    public function getResend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $email = $request->email;
        $user = User::where('email', $email)->first();
        if ($user) {
            $activation_code = rand(100000, 999999);
            $user->activation_code = $activation_code;
            $user->save();

            \Illuminate\Support\Facades\Mail::send('emails.reminder', ['activation_code' => $activation_code, 'username' => $user->username], function ($m) use ($user) {
                $m->from('info@Callories.com', 'Callories');
                $m->to($user->email, $user->username)->subject(__('messages.activate_account'));
            });

            return response()->json([
                'message' => __('messages.please_activate_your_email'),
                'activation_code' => $activation_code

            ], 200);
        } else {
            return response()->json(
                [
                    'message' => __('messages.we_cannot_find_you_your_email_in_our_records')
                ],
                400
            );
        }
    }

    public function setPassword(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'activation_code' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }

        $activation_code = $request->activation_code;
        $email = $request->email;
        $passsword = bcrypt($request->password);
        $user = User::where('activation_code', $activation_code)->where('email', $email)->first();
        if (!$user) {
            return response()->json(
                [
                    'message' => trans('messages.error_code'),
                ],
                400
            );
        } else {
            $user =
                User::where('id', $user->id)
                ->select('*')->first();
            $user->last_login = date('Y-m-d H:i:s');
            $user->password = $passsword;
            $user->save();

            $user = User::where('email', $email)
                ->select('*')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->with(['country' => function ($query) {
                    if (App::getLocale() == "ar") {
                        $query->select('id', 'name', 'phonecode');
                    } else {
                        $query->select('id', 'name_en as name', 'phonecode');
                    }
                }])
                ->with(['currency' => function ($query) {
                    if (App::getLocale() == "ar") {
                        $query->select('id', 'name', 'code');
                    } else {
                        $query->select('id', 'name_en as name', 'code');
                    }
                }])
                ->first();

            $token = JWTAuth::fromUser($user);
            $user->{"token"} = $token;
            return response()->json(new UsersResource($user), 200);
        }
    }


    public function socialLogin(Request $request)
    {
        $socialProvider = SocialProvider::where('provider_id', $request->userId)->first();
        if (!$socialProvider) {
            $userImage = '';
            if ($request->avatar) {
                $userImage = $this->saveImageUrl($request->avatar);
            }

            //create a new user and provider
            //            $user = User::firstOrCreate(
            //                ['email' => $request->email],
            //                ['username' => $request->username],
            //                ['photo' => $userImage],
            //                ['activate' => 1]
            //            );
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                $user = new User();
                $user->email = $request->email;
                $user->username = $request->username;
                $user->photo = $userImage;
                $user->activate = 1;
                $user->currency_id = $request->currency_id ?: 1;
                $user->country_id = $request->country_id ?: 188;
                $user->save();
            }
            $provider = new SocialProvider();
            $provider->user_id = $user->id;
            $provider->provider_id = $request->userId;
            $provider->provider = $request->provider;
            $provider->save();
            //            $user->socialProviders()->create(
            //                ['provider_id' => $request->userId, 'provider' => $request->provider]
            //            );

        } else {
            $user = $socialProvider->getUser;
        }
        if ($user->block == 1) {
            if ($token = JWTAuth::getToken()) {
                JWTAuth::invalidate($token);
            }
            return response()->json(['message' => __('messages.you_are_blocked_from_admin'), 'status' => 400], 400);
        } elseif ($user->activate == 0) {
            if ($token = JWTAuth::getToken()) {
                JWTAuth::invalidate($token);
            }
            return response()->json(['message' => __('messages.you_are_not_activated_yet'), 'status' => 403], 403);
        } elseif ($user->activate == 1 && $user->block == 0) {
            $token = JWTAuth::getToken();
        }

        $user = User::where('id', $user->id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->with(['country' => function ($query) {
                if (App::getLocale() == "ar") {
                    $query->select('id', 'name', 'phonecode');
                } else {
                    $query->select('id', 'name_en as name', 'phonecode');
                }
            }])
            ->with(['currency' => function ($query) {
                if (App::getLocale() == "ar") {
                    $query->select('id', 'name', 'code');
                } else {
                    $query->select('id', 'name_en as name', 'code');
                }
            }])
            ->first();


        $user->last_login = date('Y-m-d H:i:s');

        $user->save();

        if ($request->device_token) {
            if ($device = DeviceTokens::where('device_token', $request->device_token)->first()) {
                $device->user_id = $user->id;
                $device->save();
            } else {
                $device = new DeviceTokens();
                $device->device_token = $request->device_token;
                $device->user_id = $user->id;
                $device->save();
            }
        }

        $token = JWTAuth::fromUser($user);
        $user->{"token"} = $token;
        return response()->json(new UsersResource($user));
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

    public function checkQuantity(Request $request)
    {
        $product = Products::find($request->product_id);
        if ($product->quantity >= $request->quantity) {
            return response()->json(
                [
                    'status' => 200,
                    'response' => true,
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => 400,
                    'response' => false,
                ]
            );
        }
    }

    public function halls(Request $request)
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        if (App::isLocale('ar')) {
            $halls = Hall::where(function ($query) use ($request) {
                if ($request->input('title')) {
                    $query->where('title', 'LIKE', "%" . $request->input('title') . "%");
                    $query->orWhere('title_en', 'LIKE', "%" . $request->input('title') . "%");
                }
            })
                ->where(function ($query) use ($request) {
                    if ($request->input('chairs')) {
                        $query->where('chairs', '>=', $request->input('chairs'));
                    }
                    if ($request->input('hall_type')) {
                        $query->whereIn('id', function ($query) use ($request) {
                            $query->select('hall_id')
                                ->from(with(new SupplierCategory())->getTable())
                                ->where('category_id', $request->hall_type);
                        });
                    }
                })
                ->where('status', 1)
                ->select(
                    "id",
                    "title",
                    'address',
                    'longitude',
                    'latitude',
                    'chairs',
                    'currency',
                    'price_per_hour',
                    DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
        * cos(radians(halls.latitude)) 
        * cos(radians(halls.longitude) - radians(" . $longitude . ")) 
        + sin(radians(" . $latitude . ")) 
        * sin(radians(halls.latitude))) AS distance")
                )
                ->orderBy("distance", 'ASC')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
                ->with(['photos' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name', 'code');
                }])
                ->paginate(5);
            $halls->{'halls'} = HallsResource::collection($halls);
            return response()->json($halls);
        } else {
            $halls = Hall::where(function ($query) use ($request) {
                if ($request->input('title')) {
                    $query->where('title', 'LIKE', "%" . $request->input('title') . "%");
                    $query->orWhere('title_en', 'LIKE', "%" . $request->input('title') . "%");
                }
            })
                ->where(function ($query) use ($request) {
                    if ($request->input('chairs')) {
                        $query->where('chairs', '>=', $request->input('chairs'));
                    }
                    if ($request->input('hall_type')) {
                        $query->whereIn('id', function ($query) use ($request) {
                            $query->select('hall_id')
                                ->from(with(new SupplierCategory())->getTable())
                                ->where('category_id', $request->hall_type);
                        });
                    }
                })
                ->where('status', 1)
                ->select(
                    "id",
                    "title_en as title",
                    'address_en as address',
                    'longitude',
                    'latitude',
                    'currency',
                    'chairs',
                    'price_per_hour',
                    DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
        * cos(radians(halls.latitude)) 
        * cos(radians(halls.longitude) - radians(" . $longitude . ")) 
        + sin(radians(" . $latitude . ")) 
        * sin(radians(halls.latitude))) AS distance")
                )
                ->orderBy("distance", 'ASC')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
                ->with(['photos' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name_en as name', 'code');
                }])
                ->paginate(5);
            $halls->{'halls'} = HallsResource::collection($halls);
            return response()->json($halls);
        }
    }

    public function hall_details($id = 0, Request $request)
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }
        if (!Hall::find($id)) {
            return response()->json([
                'message' => "No hall found"
            ], 400);
        }
        if (App::isLocale('ar')) {
            $hall = Hall::where('id', $id)->where('status', 1)
                ->select("id", "user_id", "title", "address", "longitude", "latitude", "currency", "chairs", "price_per_hour", 'capacity', 'terms', 'policy', 'description')
                ->selectRaw('(SELECT count(*) FROM ratings WHERE ratings.hall_id=halls.id) as rates_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
                ->with(['photos' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name', 'code');
                }])
                ->first();
            //            return response()->json(new HallResource($hall));
        } else {
            $hall = Hall::where('id', $id)
                ->select('*')
                ->select("id", "user_id", "title_en as title", "address_en as address", "longitude", "latitude", "currency", "chairs", "price_per_hour", 'capacity', 'terms_en as terms', 'policy_en as policy', 'description_en as description')
                ->selectRaw('(SELECT count(*) FROM ratings WHERE ratings.hall_id=halls.id) as rates_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.hall_id=halls.id) as likes_count')
                ->selectRaw('(SELECT count(*) FROM likes WHERE likes.user_id =' . $user_like . ' AND likes.hall_id=halls.id) as is_liked')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.hall_id=halls.id ) as hall_rate')
                ->with(['photos' => function ($query) {
                    $query->select('*');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name_en as name', 'code');
                }])
                ->first();
        }
        return response()->json(new HallResource($hall));
    }


    public function packages()
    {

        if (App::isLocale('ar')) {
            $packages = Packages::select('id', 'name', 'currency_id', 'price', 'days')
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->get();

            foreach ($packages as $package) {
                if ($package->id == 0) {
                    $package->{'points'} = [
                        'مسموح لك بإضافة ' . $package->allowed_ads . ' اعلانات في اليوم',
                        'يستطيع إضافة عدد لا نهائي من التعليقات',
                        'يستطيع مراسلة الأعضاء والمتاجر',
                        'الباقة مدى الحياة',
                    ];
                }
                if ($package->id == 1) {
                    $package->{'points'} = [
                        'مسموح لك بإضافة ' . $package->allowed_ads . ' اعلانات في اليوم',
                        'يستطيع إضافة عدد لا نهائي من التعليقات',
                        'يستطيع مراسلة الأعضاء والمتاجر',
                        'مدة العضوية ' . $package->days . ' أيام',
                    ];
                }
                if ($package->id == 2) {
                    $package->{'points'} = [
                        'مسموح لك بإضافة ' . $package->allowed_ads . ' اعلانات في اليوم',
                        'يستطيع إضافة عدد لا نهائي من التعليقات',
                        'يستطيع مراسلة الأعضاء والمتاجر',
                        'مدة العضوية ' . $package->days . ' أيام',
                    ];
                }
            }


            return response()->json(
                [
                    'status' => 200,
                    'data' => [
                        'packages' => $packages,

                    ]
                ]
            );
        } else {
            $packages = Packages::select('id', 'name_en as name', 'currency_id', 'price')
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' => [
                        'packages' => $packages,
                    ]
                ]
            );
        }
    }


    public function search_autocomplete(Request $request)
    {
        $term = Str::lower($request->keyword);
        $return_array = [];

        if (!$term) {
            return response()->json($return_array);
        }
        //        $data_en = DB::table("products")->distinct()->select('title_en','id')
        //            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
        //            ->where('title_en', 'LIKE', $term . '%')->where('is_archived', 0)->where('stop', 0)->groupBy('title_en')->take(10)->get();
        //        $data_ar = DB::table("products")->distinct()->select('title','id')
        //            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
        //            ->where('title', 'LIKE', $term . '%')->where('is_archived', 0)->where('stop', 0)->groupBy('title')->take(10)->get();

        $user_like = 0;
        try {

            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }
        $data_en = Products::distinct()->select('title_en', 'id')
            ->select('products.*', 'supplier_data.sort')
            // ->whereRaw('quantity - min_quantity >= min_warehouse_quantity')
            ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
            ->where('products.title_en', 'LIKE', '%' . $term . '%')
            ->where('products.is_archived', 0)->where('products.stop', 0)->groupBy('title_en')
            ->whereHas('supplier', function (Builder $query) {
                return $query->where('stop', 0);
            })
            ->join('supplier_data', 'products.provider_id', 'supplier_data.user_id')
            ->orderBy('supplier_data.sort', 'ASC')
            ->take(10)->get();
        $data_ar = Products::distinct()->select('title', 'id')
            ->select('products.*', 'supplier_data.sort')
            // ->whereRaw('quantity - min_quantity >= min_warehouse_quantity')
            ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
            ->where('products.title', 'LIKE', '%' . $term . '%')->where('products.is_archived', 0)->where('products.stop', 0)->groupBy('title')->take(10)
            ->whereHas('supplier', function (Builder $query) {
                return $query->where('stop', 0);
            })
            ->join('supplier_data', 'products.provider_id', 'supplier_data.user_id')
            ->orderBy('supplier_data.sort', 'ASC')
            ->get();

        foreach ($data_en as $v) {
            $return_array[] = ['value' => $v->title_en, 'photo' => $v->photo, 'id' => $v->id];
        }
        foreach ($data_ar as $v) {
            $return_array[] = ['value' => $v->title, 'photo' => $v->photo, 'id' => $v->id];
        }

        return response()->json($return_array);
    }

    public function search_products(Request $request)
    {

        return response()->json(
            $this->product_objects($request)
        );
    }

    public function product_objects(Request $request)
    {
        $user_like = 0;
        try {

            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }
        $tax = floatval(Settings::find(38)->value);

        $keyword = Str::lower($request->keyword);
        $category_id = $request->category_id;
        $mainCategory = $request->main_category;
        $subcategory_id = $request->subcategory_id;
        $sort = $request->sort;
        $not_equal = $request->not_equal ?: '';
        $shop_id = $request->shop_id;
        $get_favorite = $request->get_favorite ? true : false;
        $arrange_by = 'products.id';
        $arrange_type = 'DESC';
        if ($sort == 'new') {
            $arrange_by = 'products.created_at';
        } elseif ($sort == 'price') {
            $arrange_by = 'products.price';
            $arrange_type = 'ASC';
        }
        $select_description = App::getLocale() == "ar" ? 'description' : 'description';
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $supplier_name = App::getLocale() == "ar" ? 'supplier_data.supplier_name as shop_name' : 'supplier_data.supplier_name_en as shop_name';
        $select_category_name = App::getLocale() == "ar" ? 'categories.name as category_name' : 'categories.name_en as category_name';
        $select_deliver_status = App::getLocale() == "ar" ? 'deliver_status.name as deliver_status' : 'deliver_status.name_en as deliver_status';
        $select_measurement = App::getLocale() == "ar" ? 'measurement_units.name as measurement_unit' : 'measurement_units.name_en as measurement_unit';
        $objects = Products::whereRaw('quantity - min_quantity >= min_warehouse_quantity')
            ->join('users', 'users.id', 'products.provider_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->join('deliver_status', 'deliver_status.id', 'products.deliver_status')
            ->join('measurement_units', 'measurement_units.id', 'products.measurement_id')
            ->join('supplier_data', 'products.provider_id', 'supplier_data.user_id')
            ->where('users.is_archived', 0)
            ->where('products.is_archived', 0)
            ->where('products.stop', 0)
            ->Where(function ($query) use ($category_id, $subcategory_id, $keyword, $not_equal, $get_favorite, $shop_id, $user_like, $mainCategory) {
                if ($category_id) {
                    $query->whereIn('products.id', function ($query) use ($category_id) {
                        $query->select('product_id')
                            ->from(with(new ProductCategories())->getTable())
                            ->where('category_id', $category_id);
                    });

                    //                    $query->where('products.category_id', $category_id);
                } elseif ($mainCategory) {
                    $query->whereIn('products.id', function ($query) use ($mainCategory) {
                        $query->select('product_categories.product_id')
                            ->from(with(new ProductCategories())->getTable())
                            ->join('categories', 'categories.id', 'product_categories.category_id')
                            ->join('main_categories', 'main_categories.id', 'categories.parent_id')
                            ->where('main_categories.id', $mainCategory);
                    });
                }
                if ($subcategory_id) {
                    $query->where('products.subcategory_id', $subcategory_id);
                }
                /*  if ($keyword != '') {
                      $query->where('products.title', 'LIKE', "%" . $keyword . "%")
                          ->orWhere('products.title_en', 'LIKE', "%" . $keyword . "%");
                  }*/
                if ($not_equal) {
                    $query->where('products.id', '<>', $not_equal);
                }
                if ($get_favorite) {
                    $query->whereIn('products.id', function ($query) use ($user_like) {
                        $query->select('item_id')
                            ->from(with(new Favorite())->getTable())
                            ->where('type', 0)
                            ->where('user_id', $user_like);
                    });
                }
                if ($shop_id) {
                    $query->where('products.provider_id', $shop_id);
                }
            })
            ->where(function ($query) use ($keyword) {
                if ($keyword != '') {
                    $query->where('products.title', 'LIKE', "%" . $keyword . "%")
                        ->orWhere('products.title_en', 'LIKE', "%" . $keyword . "%");
                }
            })
            ->select(
                'products.id',
                'products.title',
                'products.' . $select_description,
                DB::raw('ROUND((products.price + (products.price * ' . ($tax / 100) . ')),2) as price'),
                'users.shipment_price',
                'products.provider_id',
                'products.category_id',
                $select_category_name,
                $select_measurement,
                'products.min_quantity',
                'products.quantity',
                'products.expiry',
                'products.temperature',
                'products.weight',
                'products.client_price',
                'products.has_cover',
                $select_deliver_status,
                $supplier_name
            )
            ->with(['photos' => function ($query) {
                $query->select('id', 'product_id');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/thumbs/", thumb)) as thumb');
            }])
            ->whereHas('supplier', function (Builder $query) {
                return $query->where('stop', 0);
            })
            //            ->with(['favorites' => function ($query)use($user_like) {
            //                $query->where('user_id', $user_like);
            //            }])

            //            ->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=products.id AND type=0) as is_liked')
            //            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.user_id =' . $user_like . ' AND cart_items.item_id=products.id AND type=1 and cart_items.order_id=0) as is_carted')
            //            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.item_id=products.id AND type=1 and cart_items.order_id!=0) as purchase_count')

            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.thumb)) as thumb')
            //            ->inRandomOrder()
            ->orderBy('supplier_data.sort', 'ASC')
            // ->orderBy($arrange_by, $arrange_type)

            ->paginate(20);
        //return $objects;
        ini_set('serialize_precision', -1);
        ProductsResource::using(['user_id' => $user_like]);

        $objects->{'products'} = ProductsResource::collection($objects);

        return $objects;
    }

    public function getProduct(Request $request)
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }
        $select_description = App::getLocale() == "ar" ? 'description' : 'description';
        //        $select_title = App::getLocale() == "ar" ? 'title' : 'title';

        $select_category_name = App::getLocale() == "ar" ? 'categories.name as category_name' : 'categories.name_en as category_name';
        $select_deliver_status = App::getLocale() == "ar" ? 'deliver_status.name as deliver_status' : 'deliver_status.name_en as deliver_status';
        $select_measurement = App::getLocale() == "ar" ? 'measurement_units.name as measurement_unit' : 'measurement_units.name_en as measurement_unit';
        $select_supplier_name = App::getLocale() == "ar" ? 'supplier_data.supplier_name' : 'supplier_data.supplier_name_en as supplier_name';


        $tax = floatval(Settings::find(38)->value);
        $resp = [];
        $object = Products::select(
            'products.id',
            'products.title',
            'products.' . $select_description,
            'products.original_price',
            DB::raw('(products.price +(products.price * ' . ($tax / 100) . ')) as price'),
            'users.shipment_price',
            'products.provider_id',
            'products.category_id',
            $select_category_name,
            $select_measurement,
            'products.min_quantity',
            'products.quantity',
            'products.expiry',
            'products.temperature',
            'products.weight',
            'products.client_price',
            'products.has_cover',
            $select_deliver_status
        )
            ->join('users', 'users.id', 'products.provider_id')
            ->join('categories', 'categories.id', 'products.category_id')
            ->join('deliver_status', 'deliver_status.id', 'products.deliver_status')
            ->join('measurement_units', 'measurement_units.id', 'products.measurement_id')
            ->where('users.is_archived', 0)
            ->where('products.is_archived', 0)
            ->where('products.stop', 0)
            ->with([
                'photos' => function ($query) {
                    $query->select('id', 'product_id');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
                    $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/thumbs/", thumb)) as thumb');
                }, 'user' => function ($query) use ($select_supplier_name, $user_like) {
                    $query->select($select_supplier_name, 'users.id', 'users.longitude', 'users.latitude')
                        ->join('supplier_data', 'supplier_data.user_id', 'users.id')
                        ->selectRaw('(CASE WHEN supplier_data.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", supplier_data.photo)) END) AS photo');
                    $query->selectRaw('(SELECT count(*) FROM products WHERE products.provider_id =users.id and products.is_archived =0 ) as shop_products_count');
                    $query->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=users.id AND type=1) as is_liked');
                }
            ])
            //                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM product_ratings WHERE product_ratings.item_id=products.id  and product_ratings.type=1 ) as product_rate')
            //            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.user_id =' . $user_like . ' AND cart_items.item_id=products.id AND type=1 and cart_items.order_id=0) as is_carted')
            //
            //            ->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=products.id AND type=0) as is_liked')
            //            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.item_id=products.id AND type=1 and cart_items.order_id!=0) as purchase_count')

            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.thumb)) as thumb')
            ->where('products.id', $request->product_id)
            ->first();
        if (!$object) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'المنتج غير متوفر',
                ]
            );
        }
        $request->category_id = $object->category_id;
        $request->not_equal = $request->product_id;
        $related_projects = $this->product_objects($request);
        $object->related_projects = $related_projects;
        ProductResources::using(['user_id' => $user_like]);
        $resp['product'] = new ProductResources($object);
        //$ratings=ProductRating::select('product_ratings.id','product_ratings.user_id',
        //    'product_ratings.rate','product_ratings.comment','users.username','product_ratings.created_at')
        //    ->join('users','users.id','product_ratings.user_id')
        //    ->selectRaw('(CASE WHEN users.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", users.photo)) END) AS photo')
        //
        //    ->where('product_ratings.item_id',$request->product_id)->where('type',1)->paginate(20);
        //        $resp['ratings']=ProductRatingsResource::collection($ratings) ;

        //        if($request->device_token){
        //            DeviceMake::updateOrCreate(['device_token' => $request->device_token],
        //                ['make_id' => $object->category_id]);
        //        }
        ini_set('serialize_precision', -1);
        return response()->json($resp);
    }

    public function main_page(Request $request)
    {
        $user_like = 0;
        $user = '';
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }


        $device_token = $request->device_token;
        $select_description = App::getLocale() == "ar" ? 'description' : 'description_en as description';
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $resp = [];
        $offers = Products::select(
            'products.id',
            'products.' . $select_title,
            'products.' . $select_description,
            'products.price',
            'users.shipment_price',
            'products.price_after_discount',
            'products.quantity',
            'products.part_no',
            'products.provider_id',
            'products.make_id'
        )
            ->join('users', 'users.id', 'products.provider_id')
            ->where('users.is_archived', 0)
            ->where('products.is_archived', 0)
            ->where('products.price_after_discount', '!=', '')
            ->where(function ($query) use ($device_token, $user_like) {

                if ($device_token) {
                    $query->whereIn('products.make_id', function ($query) use ($device_token) {
                        $query->select('make_id')
                            ->from(with(new DeviceMake())->getTable())
                            ->where('device_token', $device_token);
                    });
                }
                $query->orWhere('products.id', '!=', '');
            })
            ->with(['photos' => function ($query) {
                $query->select('id', 'product_id');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=products.id AND type=0) as is_liked')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.user_id =' . $user_like . ' AND cart_items.item_id=products.id AND type=1 and cart_items.order_id=0) as is_carted')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.thumb)) as thumb')
            ->inRandomOrder()
            ->paginate(30);
        $resp['offers'] = ProductsResource::collection($offers);

        $suggestions = Products::select(
            'products.id',
            'products.' . $select_title,
            'products.' . $select_description,
            'products.price',
            'users.shipment_price',
            'products.price_after_discount',
            'products.quantity',
            'products.part_no',
            'products.provider_id',
            'products.make_id'
        )
            ->join('users', 'users.id', 'products.provider_id')
            ->where('products.is_archived', 0)
            ->where('users.is_archived', 0)
            ->where(function ($query) use ($device_token, $user_like) {
                if ($device_token) {
                    $query->whereIn('products.make_id', function ($query) use ($device_token) {
                        $query->select('make_id')
                            ->from(with(new DeviceMake())->getTable())
                            ->where('device_token', $device_token);
                    });
                }
                $query->orWhere('products.id', '!=', '');
            })
            ->with(['photos' => function ($query) {
                $query->select('id', 'product_id');
                $query->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo');
            }])
            ->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=products.id AND type=0) as is_liked')
            ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.user_id =' . $user_like . ' AND cart_items.item_id=products.id AND type=1 and cart_items.order_id=0) as is_carted')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.thumb)) as thumb')
            ->inRandomOrder()
            ->paginate(30);

        $resp['suggestions'] = ProductsResource::collection($suggestions);
        $resp['cart_count'] = $user ? $user->cart->count() : 0;
        $resp['notification_count'] = $user ? $user->notifications->count() : 0;
        if ($user) {
            $balance = Balance::where('user_id', $user->id)->sum('price');
        } else {
            $balance = 0;
        }
        $resp['balance'] = round(@$balance, 2);

        return $resp;
    }

    public function getProductRates(Request $request)
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        $resp = [];
        $object = Products::where('is_archived', 0)
            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM product_ratings WHERE product_ratings.item_id=products.id  and product_ratings.type=1 ) as product_rate')
            ->where('id', $request->product_id)
            ->first();
        $resp['rate_avg'] = $object->product_rate;
        $ratings = ProductRating::select('product_ratings.id', 'product_ratings.user_id', 'product_ratings.rate', 'product_ratings.comment', 'users.username', 'product_ratings.created_at')
            ->join('users', 'users.id', 'product_ratings.user_id')
            ->selectRaw('(CASE WHEN users.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", users.photo)) END) AS photo')
            ->where('product_ratings.item_id', $request->product_id)->where('type', 1)->paginate(5);
        $ratings->{'ratings'} = ProductRatingsResource::collection($ratings);
        $resp['ratings'] = ($ratings);


        return response()->json($resp);
    }

    public function favorite_products(Request $request)
    {
        $request->get_favorite = true;
        return response()->json($this->product_objects($request));
    }

    public function favorite_shops(Request $request)
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        $objects = User::where('user_type_id', 3)
            ->select('id', 'username', 'longitude', 'latitude', 'address')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.item_id=users.id and ratings.type=1 ) as shop_rate')
            ->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=users.id AND type=1) as is_liked')
            ->whereIn('id', function ($query) use ($user_like) {
                $query->select('item_id')
                    ->from(with(new Favorite())->getTable())
                    ->where('user_id', $user_like)
                    ->where('type', 1);
            })
            ->where('is_archived', 0)
            ->paginate(10);
        return response()->json($objects);
    }

    public function shop_details(Request $request)
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }
        $select_supplier_name = App::getLocale() == "ar" ? 'supplier_data.supplier_name' : 'supplier_data.supplier_name_en as supplier_name';

        $user_obj = User::where('users.user_type_id', 3)
            ->select($select_supplier_name, 'users.address', 'users.id', 'users.about', 'users.email', 'users.phone', 'users.longitude', 'users.latitude')
            ->join('supplier_data', 'supplier_data.user_id', 'users.id')
            ->selectRaw('(CASE WHEN supplier_data.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", supplier_data.photo)) END) AS photo')

            //            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.item_id=users.id and ratings.type=1 ) as shop_rate')

            ->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=users.id AND type=1) as is_liked')
            ->where('users.id', $request->shop_id)
            ->first();

        //        $user_rates=Rating::select('id','rate','comment','user_id','created_at')
        //            ->with(['user'=>function($query){
        //                $query->select('id','username');
        //                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
        //
        //            }])
        //            ->where('item_id',$request->shop_id)->where('type',1)->paginate(15);
        $user_obj->products = $this->product_objects($request);
        $resp['shop'] = $user_obj;

        //        $user_rates->{'user_rates'} = ShopRatingsResource::collection($user_rates);

        //        $resp['rates'] = $user_rates ;

        return response()->json($resp);
    }

    public function getAutoParts(Request $request)
    {
        $term = Str::lower($request->keyword);
        if (!$term) return [];
        $data = DB::table("autoparts")->distinct()->select('name', 'id')->where('name', 'LIKE', $term . '%')->groupBy('name')->take(10);
        $all_data = DB::table("autoparts")->distinct()->select('name_en as name', 'id')->where('name_en', 'LIKE', $term . '%')->groupBy('name_en')->take(10)->union($data)->get();
        return response()->json($all_data);
    }


    public function getCountries()
    {

        if (App::isLocale('ar')) {
            $countries = Countries::select('id', 'name', 'phonecode')->get();
            return response()->json($countries);
        } else {
            $countries = Countries::select('id', 'name_en as name', 'phonecode')->get();
            return response()->json($countries);
        }
    }

    public function getCurrencies()
    {

        if (App::isLocale('ar')) {
            $countries = Currencies::select('id', 'name', 'code')->get();
            return response()->json($countries);
        } else {
            $countries = Currencies::select('id', 'name_en as name', 'code')->get();
            return response()->json($countries);
        }
    }

    public function getFaqs()
    {
        if (App::isLocale('ar')) {
            $faqs = Faqs::select('id', 'question', 'answer')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $faqs
                ]
            );
        } else {
            $faqs = Faqs::select('id', 'question_en as question', 'answer_en as answer')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $faqs
                ]
            );
        }
    }

    public function getCategories()
    {
        $select_name = App::isLocale('ar') ? 'categories.name' : 'categories.name_en as name';

        $objects = Categories::select('categories.id', $select_name)
            ->join('products', 'products.category_id', 'categories.id')
            ->join('users', 'users.id', 'products.provider_id')
            ->selectRaw('(CASE WHEN categories.photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", categories.photo)) END) AS photo')
            //                ->with(['subCategories' => function ($query) use($select_name){
            //                   $query->select('id','category_id',$select_name);
            //    }
            //    ,'subCategories.measurementUnits'=>function($query)use($select_name){
            //                    $query->select('measurement_id',$select_name);
            //                    }
            //    ])
            //            ->where('categories.parent_id', 0)
            ->where('categories.photo', '<>', '')
            ->where('categories.stop', 0)
            ->where('products.is_archived', 0)
            ->where('products.stop', 0)
            ->where('users.is_archived', 0)
            ->orderBy('categories.sort', 'asc')
            ->groupBy('categories.id')
            ->get();
        return response()->json(($objects));
    }

    public function getMainCategories()
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }
        $select_name = App::isLocale('ar') ? 'main_categories.name' : 'main_categories.name_en as name';

        $objects = MainCategories::select('id', $select_name)
            ->with(
                [
                    'subCategories' => function ($query) {
                        $select_name = App::isLocale('ar') ? 'categories.name' : 'categories.name_en as name';

                        $query->select('categories.id', 'categories.parent_id', $select_name, 'categories.sort')
                            //                        ->join('product_categories', 'product_categories.category_id', 'categories.id')
                            ->selectRaw('(CASE WHEN categories.photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", categories.photo)) END) AS photo')
                            ->where('categories.photo', '<>', '')
                            ->where('categories.stop', 0)
                            ->orderBy('categories.sort', 'asc')
                            ->groupBy('categories.id');
                    }
                ]
            )
            ->where('main_categories.stop', 0)
            ->where('main_categories.is_archived', 0)
            ->orderBy('main_categories.sort', 'asc')
            ->groupBy('main_categories.id')
            ->get();
        return response()->json([
            'status' => 200,
            'categories' => $objects,
            'new_notifications' => Notification::where('reciever_id', $user_like)->where('status', 0)->count(),
            'contacts' => [
                'phone' => Settings::find(17)->value,
                'whatsapp' => Settings::find(18)->value,
                'email' => Settings::find(19)->value
            ],
            'delete_account' => (int) Settings::find(50)->value,
        ]);
        return response()->json(($objects));
    }

    public function shopCategories()
    {
        $select_name = App::isLocale('ar') ? 'name' : 'name_en as name';

        $objects = ServicesCategories::select('id', $select_name)
            ->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->orderBy('sort', 'asc')->get();
        return response()->json(($objects));
    }

    public function getSuppliers(Request $request)
    {
        $user_like = 0;
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }
        $category_id = $request->category_id;
        $keyword = Str::lower($request->keyword);

        $select_supplier_name = App::getLocale() == "ar" ? 'supplier_data.supplier_name' : 'supplier_data.supplier_name_en as supplier_name';
        $objects = User::select($select_supplier_name, 'users.id', 'users.longitude', 'users.latitude')
            ->join('supplier_data', 'supplier_data.user_id', 'users.id')
            ->join('products', 'products.provider_id', 'users.id')
            ->selectRaw('(CASE WHEN supplier_data.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", supplier_data.photo)) END) AS photo')
            ->selectRaw('(SELECT count(*) FROM products WHERE products.provider_id =users.id and products.is_archived =0 ) as shop_products_count')
            ->where(function ($query) use ($category_id, $keyword) {
                if ($category_id) {
                    $query->whereIn('users.id', function ($query) use ($category_id) {
                        $query->select('user_id')
                            ->from(with(new SupplierCategory())->getTable())->where('category_id', $category_id);
                    });
                }
                if ($keyword != '') {
                    $query->where('supplier_data.supplier_name', 'LIKE', "%" . $keyword . "%")
                        ->orWhere('supplier_data.supplier_name_en', 'LIKE', "%" . $keyword . "%");
                }
            })
            ->where('users.is_archived', 0)
            ->where('supplier_data.stop', 0)
            ->groupBy('users.id')
            ->orderBy('supplier_data.sort', 'ASC')
            ->paginate(20);
        return response()->json(($objects));
    }

    public function getSelections($sub_category_id = 0)
    {
        if (App::isLocale('ar')) {
            $categories = CategoriesSelections::select('id', 'selection_id')
                ->where('category_id', $sub_category_id)
                ->with(['selection.options' => function ($query) {
                    $query->select('id', 'name', 'selection_id', 'parent_id');
                }])
                ->with(['selection' => function ($query) {
                    $query->select('id', 'name', 'parent_id');
                }])
                ->orderBy('sort', 'asc')
                ->inRandomOrder()
                ->get();
            return response()->json($categories);
        } else {
        }
    }


    public function getAllStores($latitude = "", $longitude = "", Request $request)
    {
        if (App::isLocale('ar')) {
            $stores = Stores::where(function ($query) use ($request) {
                if ($request->input('name')) {
                    $query->where('name', 'LIKE', "%" . $request->input('name') . "%");
                }
            })->select("id", "name", 'address', 'longitude', 'latitude', "category_id", 'photo')
                ->select(
                    "*",
                    DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
        * cos(radians(stores.latitude)) 
        * cos(radians(stores.longitude) - radians(" . $longitude . ")) 
        + sin(radians(" . $latitude . ")) 
        * sin(radians(stores.latitude))) AS distance")
                )
                ->orderBy("distance", 'ASC')
                ->with(['getCategory' => function ($query) {
                    $query->select('id', 'name');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->paginate(20);
            return response()->json(
                [
                    'status' => 200,
                    'data' => StoresResource::collection($stores)
                ]
            );
        } else {
            $stores = Stores::where(function ($query) use ($request) {
                if ($request->input('name')) {
                    $query->where('name_en', 'LIKE', "%" . $request->input('name') . "%");
                }
            })->select("id", "name_en as name", "address_en as address", "longitude", "latitude", "category_id", 'photo')
                ->select(
                    "*",
                    DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
        * cos(radians(stores.latitude)) 
        * cos(radians(stores.longitude) - radians(" . $longitude . ")) 
        + sin(radians(" . $latitude . ")) 
        * sin(radians(stores.latitude))) AS distance")
                )
                ->orderBy("distance", 'ASC')
                ->with(['getCategory' => function ($query) {
                    $query->select('id', 'name');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->paginate(20);
            return response()->json(
                [
                    'status' => 200,
                    'data' => StoresResource::collection($stores)
                ]
            );
        }
    }

    public function getByCategory($id = 0, $latitude = "", $longitude = "", Request $request)
    {
        if (App::isLocale('ar')) {
            $stores = Stores::where(function ($query) use ($request) {
                if ($request->input('name')) {
                    $query->where('name', 'LIKE', "%" . $request->input('name') . "%");
                }
            })->where('category_id', $id)->select("id", "name", 'address', 'longitude', 'latitude', "category_id", 'photo')
                ->select(
                    "*",
                    DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
        * cos(radians(stores.latitude)) 
        * cos(radians(stores.longitude) - radians(" . $longitude . ")) 
        + sin(radians(" . $latitude . ")) 
        * sin(radians(stores.latitude))) AS distance")
                )
                ->orderBy("distance", 'ASC')
                ->with(['getCategory' => function ($query) {
                    $query->select('id', 'name');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->paginate(20);
            return response()->json(
                [
                    'status' => 200,
                    'data' => StoresResource::collection($stores)
                ]
            );
        } else {
            $stores = Stores::where(function ($query) use ($request) {
                if ($request->input('name')) {
                    $query->where('name_en', 'LIKE', "%" . $request->input('name') . "%");
                }
            })->where('category_id', $id)->select("id", "name_en as name", "address_en as address", "longitude", "latitude", "category_id", 'photo')
                ->select(
                    "*",
                    DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
        * cos(radians(stores.latitude)) 
        * cos(radians(stores.longitude) - radians(" . $longitude . ")) 
        + sin(radians(" . $latitude . ")) 
        * sin(radians(stores.latitude))) AS distance")
                )
                ->orderBy("distance", 'ASC')
                ->with(['getCategory' => function ($query) {
                    $query->select('id', 'name');
                    $query->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->paginate(20);
            return response()->json(
                [
                    'status' => 200,
                    'data' => StoresResource::collection($stores)
                ]
            );
        }
    }


    public function last_sellers()
    {
        $sellers = User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from(with(new Products())->getTable());
        })
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->orderBy('id', 'DESC')->limit(10)->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $sellers
            ]
        );
    }

    public function sellers()
    {
        $sellers = User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from(with(new Products())->getTable());
        })
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . \url('/') . '/site/images/no-image.png" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->orderBy('id', 'DESC')->paginate(12);
        return response()->json(
            [
                'status' => 200,
                'postData' => $sellers
            ]
        );
    }


    public function main_categories()
    {
        $object = [];
        $category = Categories::where('mobile', 1)->first() ?: Categories::first();
        $sub_categories = Subcategories::where('mobile', 1)
            ->select('*')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo')
            ->limit(4)
            ->get();
        $object["category"] = $category;
        $object["sub_categories"] = $sub_categories;
        return response()->json(
            [
                'status' => 200,
                'data' => $object
            ]
        );
    }

    public function cards()
    {
        $cards = CardsCategories::where('hidden', 0)
            ->select('*')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo')
            ->with(['getCards' => function ($query) {
                $query->select('*');
                $query->where('price', '!=', 0)->get();
            }])->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $cards
            ]
        );
    }

    public function get_slider()
    {
        $slider = Slider::select('title', 'description', 'updated_at')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as image')
            //            ->where(function ($query) {
            //                if (App::getLocale() == "ar") {
            //                    $query->where('is_en', 0);
            //                } else {
            //                    $query->where('is_en', 1);
            //
            //                }
            //            })
            ->get();
        return response()->json($slider);

        //        return response()->json(
        //            [
        //                'status' => 200,
        //                'data' => $slider
        //            ]
        //        );
    }

    public function home_page()
    {

        $user_like = 0;
        $user = '';
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                $user_like = $user->id;
            }
        } catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

        $slider = Slider::select('sliders.title', 'sliders.description', 'sliders.updated_at', 'sliders.has_link', 'sliders.item_type', 'item_types.name', 'sliders.item_id')
            ->leftJoin('item_types', 'item_types.id', 'sliders.item_type')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", sliders.photo)) as image')
            ->with([
                'category' => function ($query) {
                    $select_name = App::isLocale('ar') ? 'name' : 'name_en as name';
                    $query->select('id', 'parent_id', $select_name, 'sort');
                },
                'shop' => function ($query) {
                    $select_name = App::getLocale() == "ar" ? 'supplier_name' : 'supplier_name_en as supplier_name';
                    $query->select('users.id', $select_name)
                        ->join('supplier_data', 'supplier_data.user_id', 'users.id');
                }
            ])
            //            ->where(function ($query) {
            //                if (App::getLocale() == "ar") {
            //                    $query->where('is_en', 0);
            //                } else {
            //                    $query->where('is_en', 1);
            //
            //                }
            //            })
            ->get();
        $select_name = App::getLocale() == "ar" ? 'supplier_name' : 'supplier_name_en as supplier_name';

        $partners = User::select('users.id', $select_name)
            ->join('supplier_data', 'supplier_data.user_id', 'users.id')
            ->selectRaw('(CASE WHEN supplier_data.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", supplier_data.photo)) END) AS photo')
            ->where('users.user_type_id', 3)->where('users.is_archived', 0)->where('users.block', 0)->inRandomOrder()->paginate(6);
        $cart_count = $user ? $user->cart->count() : 0;

        return response()->json([
            'slider' => $slider,
            'partners' => $partners,
            'cart_count' => $cart_count,
            'activate' => 1,
            //            'activate' => $user_like!=0 ? $user->activate : 0,
            'cancel_reason' => $user ? $user->cancel_reason : '',

        ]);
        //        return response()->json(
        //            [
        //                'status' => 200,
        //                'data' => $slider
        //            ]
        //        );
    }

    public function car_makes_list(Request $request)
    {
        $select_name = App::getLocale() == "ar" ? 'name' : 'name_en as name';
        $makes = Make::select('id', $select_name, 'sort')->where(function ($query) use ($request) {
            if ($request->is_special_order == 1) {
                $query->where('is_special_order', 1);
            } else {
                $query->where('stop', 0);
            }
        })
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", image)) as image')
            ->where('is_archived', 0)
            ->orderBy('sort', 'ASC')
            ->get();

        return response()->json($makes);
    }

    public function car_make_years_list(Request $request)
    {
        $make_id = $request->make_id;
        $years = MakeYear::select('id', 'year', 'make_id')->where('make_id', $make_id)
            ->where('is_archived', 0)
            ->orderBy('year', 'ASC')
            ->get();
        return response()->json($years);
    }

    public function car_year_models_list(Request $request)
    {
        $year_id = $request->year_id;
        if (App::getLocale() == "ar") {
            $models = Models::select('id', 'name')->where('makeyear_id', $year_id)
                ->where('is_archived', 0)->get();
        } else {
            $models = Models::select('id', 'name_en as name')->where('makeyear_id', $year_id)
                ->where('is_archived', 0)->get();
        }
        return response()->json($models);
    }

    public function getBranches($id = 0)
    {
        $branches = Branches::select('*')->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $branches
            ]
        );
    }


    public function get_all_states()
    {
        if (App::isLocale('ar')) {
            $countries = States::select('id', 'name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $countries
                ]
            );
        } else {
            $countries = States::select('id', 'name_en as name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $countries
                ]
            );
        }
    }


    public function cities()
    {
        if (App::isLocale('ar')) {
            $countries = States::select('id', 'name')->get();
            return response()->json($countries);
        } else {
            $countries = States::select('id', 'name_en as name')->get();
            return response()->json($countries);
        }
    }

    public function banks()
    {
        if (App::isLocale('ar')) {
            $countries = Banks::select('id', 'name')->get();
            return response()->json($countries);
        } else {
            $countries = Banks::select('id', 'name_en as name')->get();
            return response()->json($countries);
        }
    }

    public function bank_accounts()
    {
        if (App::isLocale('ar')) {
            $countries = BankAccounts::select('id', 'bank_name', 'account_name', 'account_number')->get();
            return response()->json($countries);
        } else {
            $countries = Banks::select('id', 'name_en as name')->get();
            return response()->json($countries);
        }
    }

    public function notification_types()
    {
        $catecories = NotificationTypes::select("id", "name")
            //                ->selectRaw('(CASE WHEN photo = "" THEN "'.url('/')."/images/placeholder.png".'" ELSE (CONCAT ("'.URL::to('/').'/uploads/", photo)) END) AS photo')
            ->get();
        return response()->json($catecories);
    }


    public function getStates($id = 0)
    {
        if (App::isLocale('ar')) {
            $states = States::select('id', 'name', 'country_id')->where('country_id', $id)->get();
            return response()->json(
                $states
            );
        } else {
            $states = States::select('id', 'name_en as name', 'country_id')->where('country_id', $id)->get();
            return response()->json(
                $states

            );
        }
    }


    public function getSubCategories($id = 0)
    {
        $select_name = App::isLocale('ar') ? 'name' : 'name_en as name';
        $categories = Subcategories::select('id', $select_name, 'category_id')
            ->where('category_id', $id)->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $categories
            ]
        );
    }

    public function getProducts($id = 0)
    {
        $tax = floatval(Settings::find(38)->value);

        $products = Products::select(
            '*',
            DB::raw('ROUND((price +(price * ' . ($tax / 100) . ')),2) as price')
        )
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo')
            ->where('sub_category_id', $id)
            ->with('getSpecification')
            ->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $products
            ]
        );
    }

    public function get_contact_categories($id = 0)
    {
        if (App::isLocale('ar')) {
            $types = ContactTypes::select('id', 'name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $types
                ]
            );
        } else {
            $types = ContactTypes::select('id', 'name_en as name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $types
                ]
            );
        }
    }


    public function getBrands()
    {
        if (App::isLocale('ar')) {
            $cars = Cars::select('id', 'name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $cars
                ]
            );
        } else {
            $cars = Cars::select('id', 'name_en as name')->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $cars
                ]
            );
        }
    }


    public function testResponse()
    {
        return response()->json([
            'data' => [
                'notification_type' => 1,
                'notification_title' => "New message",
                'notification_message' => "Yoh have new message",
                'notification_data' => null
            ]
        ]);
    }

    public function getYears()
    {
        $years = Years::select('id', 'name')->orderBy('name', 'DESC')->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $years
            ]
        );
    }

    public function getAdmins()
    {
        $admins = User::select('id', 'username', 'first_name', 'last_name')->where('user_type_id', 1)->orderBy('id', 'DESC')->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $admins
            ]
        );
    }


    public function getModels($id = 0)
    {
        if (App::isLocale('ar')) {
            $models = CarsModels::select('id', 'name')->where('cars_category_id', $id)->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $models
                ]
            );
        } else {
            $models = CarsModels::select('id', 'name_en as name')->where('cars_category_id', $id)->get();
            return response()->json(
                [
                    'status' => 200,
                    'data' => $models
                ]
            );
        }
    }


    public function services_price($id = 0)
    {
        $services = Services::where('id', $id)->first();
        return response()->json(
            [
                'status' => 200,
                'data' => ['price' => $services->price]
            ]
        );
    }

    public function min_shipments($id = 0)
    {
        $services = Services::where('id', $id)->first();
        return response()->json(
            [
                'status' => 200,
                'data' => ['min_shipments' => $services->min_shipments]
            ]
        );
    }

    public function min_order_price()
    {
        $price = Settings::find(14)->value;
        return response()->json(
            [
                'status' => 200,
                'data' => $price
            ]
        );
    }

    public function checkout($request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'payment_type' => 'required',
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {


            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }

        $order = Orders::where('id', $request->order_id)->where('service_id', 8)->first();
        if (!$order) {
            return response()->json([
                'status' => 400,
                'message' => "عفوا هناك خطأ في في رقم الطلب",
            ]);
        }

        $url = "https://test.oppwa.com/v1/checkouts";
        $data = "entityId=" . Settings::find(18)->value .
            "&amount=" . $request->amount .
            "&currency=SAR" .
            "&paymentType=" . $request->payment_type;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . Settings::find(17)->value
        ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);

        //        echo "<pre>";
        $values = json_decode($responseData);
        //        echo $values->id;

        $order->payment_id = $values->id;
        $order->price_after_fees = $request->amount;
        $order->save();


        return response()->json(
            [
                'status' => 200,
                'message' => "تم إنشاء رقم الدفع بنجاح",
                'payment_id' => $values->id
            ]
        );
    }

    public function payment_status(Request $request)
    {

        $id = $request->payment_id ? $request->payment_id : $request->id;
        $order = Orders::where('payment_id', $id)->first();
        if (!$order) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "عفوا هذه العملية غير مربوطة بطلب",
                ]
            );
        }

        $url = "https://test.oppwa.com/v1/checkouts/" . $id . "/payment";
        $url .= "?entityId=" . Settings::find(18)->value;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . Settings::find(17)->value
        ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $values = json_decode($responseData);

        if (preg_match("/^(000\.000\.|000\.100\.1|000\.[36])/", $values->result->code) || preg_match("/^(000\.400\.0[^3]|000\.400\.100)/", $values->result->code)) {

            $token = Settings::find(15)->value;
            $curl = curl_init();

            $cards = [];
            foreach ($order->getDetails as $details) {
                $cards[] = [
                    'cardId' => @$details->geCard->card_id,
                    'quantity' => @$details->quantity
                ];
            }

            $data2 = [
                'cards' => $cards,
                'sms' => false,
                'charged' => true,
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.rasseed.com/api/method/buy",
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
                $error = "cURL Error #:" . $err;

                return response()->json(
                    [
                        'status' => 400,
                        'message' => $error,
                    ]
                );
            } else {
                $resp = json_decode($response);
                $your_money_is = $resp->message->orderID;
                $order->card_order_id = $your_money_is;
                $order->status = 2;
                $order->save();


                //make notification


                $notification55 = new Notification();
                $notification55->sender_id = 1;
                $notification55->reciever_id = $order->user_id;
                $notification55->order_id = $order->id;
                $notification55->type = 17;
                $notification55->message = "تم شراء كروت الشحن بنجاح";
                $notification55->message_en = "You bought charged cards successfully.";
                $notification55->save();

                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60 * 20);

                if ($order->getUser->lang == "en") {
                    $notification_title = "تأكيد دفع كروت الشحن";
                    $notification_message = $notification55->message_en;
                } else {
                    $notification_title = "Payment cards confirmed";
                    $notification_message = $notification55->message;
                }
                $notificationBuilder = new PayloadNotificationBuilder($notification_title);
                $notificationBuilder->setBody($notification_message)
                    ->setSound('default');

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData([
                    'data' => [
                        'notification_type' => (int)$notification55->type,
                        'notification_title' => $notification_title,
                        'notification_message' => $notification_message,
                        'notification_data' => $order
                    ]
                ]);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                $token = @$notification55->getReciever->device_token;

                if ($token) {
                    $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
                    $downstreamResponse->numberSuccess();
                    $downstreamResponse->numberFailure();
                    $downstreamResponse->numberModification();
                }
            }

            return response()->json(
                [
                    'status' => 200,
                    'message' => "Successful payment",
                    'data' => $values
                ]
            );
        }
        return response()->json(
            [
                'status' => 400,
                'message' => "Failed payment",
                'data' => $values
            ]
        );
    }


    public function payment_money_status(Request $request)
    {

        $id = $request->payment_id ? $request->payment_id : $request->id;
        $payment = MandoobPayments::where('payment_id', $id)->first();
        if (!$payment) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => "عفوا هذه العملية غير مربوطة بطلب",
                ]
            );
        }

        $url = "https://test.oppwa.com/v1/checkouts/" . $id . "/payment";
        $url .= "?entityId=" . Settings::find(18)->value;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . Settings::find(17)->value
        ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $values = json_decode($responseData);

        if (preg_match("/^(000\.000\.|000\.100\.1|000\.[36])/", $values->result->code) || preg_match("/^(000\.400\.0[^3]|000\.400\.100)/", $values->result->code)) {


            $payment->status = 1;
            $payment->save();


            $object = new Balance();
            $object->user_id = $payment->user_id;
            $object->price = $payment->money;
            $object->balance_type_id = 2;
            $object->notes = "شحن رصيد من خلال بوابات الدفع ";
            $object->save();


            $notification55 = new Notification();
            $notification55->sender_id = 1;
            $notification55->reciever_id = $object->user_id;
            $notification55->type = 15;
            $notification55->message = "تم شحن رصيد بقيمة " . $object->price . " ريال  ";
            $notification55->message_en = "Your balance charged with " . $object->price . " SAR ";
            $notification55->save();


            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            if (@$notification55->getReciever->lang == "en") {
                $notification_title = "Charge your account";
                $notification_message = $notification55->message_en;
            } else {
                $notification_title = "شحن بالبطاقة الائتمانية";
                $notification_message = $notification55->message;
            }
            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($notification_message)
                ->setSound('default');

            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData([
                'data' => [
                    'notification_type' => (int)$notification55->type,
                    'notification_title' => $notification_title,
                    'notification_message' => $notification_message,
                    'notification_data' => null
                ]
            ]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $token = @$notification55->getReciever->device_token;

            if ($token) {
                $downstreamResponse = FCM::sendTo($token, $option, @$notification55->getReciever->device_type == "android" ? null : $notification, $data);
                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
            }


            return response()->json(
                [
                    'status' => 200,
                    'message' => "Successful payment",
                    'data' => $values
                ]
            );
        }
        return response()->json(
            [
                'status' => 400,
                'message' => "Failed payment",
                'data' => $values
            ]
        );
    }


    public function test()
    {
        $response = \request()->input('response');
        if (preg_match("/^(000\.000\.|000\.100\.1|000\.[36])/", $response) || preg_match("/^(000\.400\.0[^3]|000\.400\.100)/", $response)) {
            echo "the url $response contains guru";
        } else {
            echo "the url $response does not contain guru";
        }
    }

    public function contactCategories()
    {
        if (App::isLocale('ar')) {
            $catecories = ContactTypes::select("id", "name")
                //                ->selectRaw('(CASE WHEN photo = "" THEN "'.url('/')."/images/placeholder.png".'" ELSE (CONCAT ("'.URL::to('/').'/uploads/", photo)) END) AS photo')
                ->get();
        } else {
            $catecories = ContactTypes::select("id", "name_en as name")
                //                ->selectRaw('(CASE WHEN photo = "" THEN "'.url('/')."/images/placeholder.png".'" ELSE (CONCAT ("'.URL::to('/').'/uploads/", photo)) END) AS photo')
                ->get();
        }


        return response()->json([
            'contact_categories' => $catecories,
            'youtube' => Settings::find(15)->value,
            'facebook' => Settings::find(5)->value,
            'twitter' => Settings::find(11)->value,
            'instagram' => Settings::find(7)->value,
            'snapchat' => Settings::find(6)->value,
        ]);
    }


    public function deliveryTimes()
    {
        if (App::isLocale('ar')) {
            $deliveries = DeliveryTimes::select("id", "name")
                ->orderBy('orders', 'asc')
                ->get();
        } else {
            $deliveries = DeliveryTimes::select("id", "name_en as name")
                ->orderBy('orders', 'asc')
                ->get();
        }


        return response()->json($deliveries);
    }

    public function payment_status1(Request $request)
    {

        $id = $request->payment_id ? $request->payment_id : $request->id;
        $order = Orders::where('payment_id', $id)->first();
        if (!$order) {
            return response()->json('', 400);
        }

        $url = "https://test.oppwa.com/v1/checkouts/" . $id . "/payment";
        $url .= "?entityId=8ac7a4c76c27e69f016c2881196001c8";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer OGFjN2E0Yzc2YzI3ZTY5ZjAxNmMyODdkZDI3NjAxYzF8Z3MzdzRLcmpNZA=='
        ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $values = json_decode($responseData);

        if (preg_match("/^(000\.000\.|000\.100\.1|000\.[36])/", $values->result->code) || preg_match("/^(000\.400\.0[^3]|000\.400\.100)/", $values->result->code)) {
            return response()->json('', 200);
        }
        return response()->json('', 400);
    }


    public function payment_money_status1(Request $request)
    {

        $id = $request->payment_id ? $request->payment_id : $request->id;

        $url = "https://test.oppwa.com/v1/checkouts/" . $id . "/payment";
        $url .= "?entityId=8ac7a4c76c27e69f016c2881196001c8";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer OGFjN2E0Yzc2YzI3ZTY5ZjAxNmMyODdkZDI3NjAxYzF8Z3MzdzRLcmpNZA=='
        ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $values = json_decode($responseData);

        if (preg_match("/^(000\.000\.|000\.100\.1|000\.[36])/", $values->result->code) || preg_match("/^(000\.400\.0[^3]|000\.400\.100)/", $values->result->code)) {

            //After success

            return response()->json('', 200);
        }
        return response()->json('', 400);
    }


    public function testBuy(Request $request)
    {
        $token = Settings::find(15)->value;
        $curl = curl_init();

        $order = Orders::find(924);
        $cards = [];
        foreach ($order->getDetails as $details) {
            $cards[] = [
                'cardId' => @$details->geCard->card_id,
                'quantity' => @$details->quantity
            ];
        }

        $data2 = [
            'cards' => $cards,
            'sms' => false,
            'charged' => true,
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.rasseed.com/api/method/buy",
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
            $your_money_is = $resp->message->orderID;
            echo "<pre>";
            $order->card_order_id = $your_money_is;
            $order->status = 2;
            $order->save();
            print_r($resp->message->orderID);
        }
    }


    public function testDisplay(Request $request)
    {
        $token = Settings::find(15)->value;
        $curl = curl_init();

        $order = Orders::find(924);
        $cards = [];

        $data2 = [
            'orders' => $order->card_order_id,

        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.rasseed.com/api/method/string",
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
            print_r($resp);
        }
    }


    public function get_credentials()
    {
        return response()->json(
            [
                'status' => 200,
                'data' => [
                    'authorization' => Settings::find(17)->value,
                    'entity' => Settings::find(18)->value,
                ]
            ]
        );
    }


    public function payment_methods()
    {
        $PaymentMethods = PaymentMethods::select('id', 'name')->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $PaymentMethods
            ]
        );
    }


    public function getIllustrations()
    {
        if (App::isLocale('ar')) {
            return response()->json(
                Illustrations::select('id', 'title', 'description')
                    ->selectRaw('CONCAT ("' . url('/') . '/uploads/", photo)  AS url')
                    ->get()
            );
        } else {
            return response()->json(
                Illustrations::select('id', 'title_en as title', 'description_en as description')
                    ->selectRaw('CONCAT ("' . url('/') . '/uploads/", photo)  AS url')
                    ->get()
            );
        }
    }

    public function hall_types()
    {
        if (App::isLocale('ar')) {
            return response()->json(
                Categories::select('id', 'name')->get()
            );
        } else {
            return response()->json(
                Categories::select('id', 'name_en as name')->get()
            );
        }
    }


    public function getPages()
    {
        return response()->json(
            [
                'status' => 200,
                'data' => Content::select('id', 'page_name', 'content')
                    ->selectRaw('(CASE WHEN icon = "" THEN "" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", icon)) END) AS photo')
                    ->get()
            ]
        );
    }

    public function about()
    {
        if (App::isLocale('ar')) {
            return response()->json(
                [
                    'data' => Content::find(1)->content
                ]
            );
        } else {
            return response()->json(
                [
                    'data' => Content::find(1)->content_en
                ]
            );
        }
    }


    public function terms()
    {
        if (App::isLocale('ar')) {
            return response()->json(
                [
                    'data' => Content::find(2)->content
                ]
            );
        } else {
            return response()->json(
                [
                    'data' => Content::find(2)->content_en
                ]
            );
        }
    }

    public function MainPage()
    {
        $longitude = \request()->get('longitude') ?: '46.2620616';
        $latitude = \request()->get('longitude') ?: '24.7241504';
        $category_id = \request()->get('category_id') ?: @Categories::orderBy('sort', 'asc')->first()->category_id;
        if (App::isLocale('ar')) {

            $offers_slider = OffersCategories::select('id', 'name')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->selectRaw('(CASE WHEN icon = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", icon)) END) AS icon')
                ->orderBy('sort', 'asc')
                ->get();

            $resp['offers_slider'] = OffersCategoriesResources::collection($offers_slider);

            $restaurant_categories = Categories::select('id', 'name')->orderBy('sort', 'asc')->get();

            $resp['restaurant_categories'] = RestuarantCategoriesResources::collection($restaurant_categories);


            $restaurants = Restaurants::select(
                'id',
                'title',
                'description',
                'delivery_price',
                DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
                        * cos(radians(restaurants.latitude)) 
                        * cos(radians(restaurants.longitude) - radians(" . $longitude . ")) 
                        + sin(radians(" . $latitude . ")) 
                        * sin(radians(restaurants.latitude))) AS distance")
            )
                ->selectRaw('(CASE WHEN logo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", logo)) END) AS logo')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.restaurant_id=restaurants.id ) as restaurant_rate')
                ->where('approved', 1)
                ->where('stop', 0)
                ->where(function ($query) use ($category_id) {
                    if ($category_id) {
                        $query->whereIn('id', function ($query) use ($category_id) {
                            $query->select('restaurant_id')
                                ->from(with(new RestaurantCategories())->getTable())
                                ->where('category_id', $category_id);
                        });
                    }
                    if (\request()->get('restaurant_name')) {
                        $query->where('title', 'LIKE', "%" . \request()->get('restaurant_name') . "%");
                    }
                })
                ->orderBy("distance", 'ASC')
                ->paginate(10);

            $restaurants->{'restaurants'} = RestuarantsResources::collection($restaurants);
            $resp['restaurants'] = $restaurants;
        } else {

            $offers_slider = OffersCategories::select('id', 'name_en as name')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->selectRaw('(CASE WHEN icon = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", icon)) END) AS icon')
                ->orderBy('sort', 'asc')
                ->get();

            $resp['offers_slider'] = OffersCategoriesResources::collection($offers_slider);


            $restaurant_categories = Categories::select('id', 'name_en as name')->orderBy('sort', 'asc')->get();

            $resp['restaurant_categories'] = RestuarantCategoriesResources::collection($restaurant_categories);

            $restaurants = Restaurants::select(
                'id',
                'title_en as title',
                'description_en as description',
                'delivery_price',
                DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
                        * cos(radians(restaurants.latitude)) 
                        * cos(radians(restaurants.longitude) - radians(" . $longitude . ")) 
                        + sin(radians(" . $latitude . ")) 
                        * sin(radians(restaurants.latitude))) AS distance")
            )
                ->selectRaw('(CASE WHEN logo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", logo)) END) AS logo')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.restaurant_id=restaurants.id ) as restaurant_rate')
                ->where('approved', 1)
                ->where('stop', 0)
                ->where(function ($query) use ($category_id) {
                    if ($category_id) {
                        $query->whereIn('id', function ($query) use ($category_id) {
                            $query->select('restaurant_id')
                                ->from(with(new RestaurantCategories())->getTable())
                                ->where('category_id', $category_id);
                        });
                    }
                    if (\request()->get('restaurant_name')) {
                        $query->where('title_en', 'LIKE', "%" . \request()->get('restaurant_name') . "%");
                    }
                })
                ->orderBy("distance", 'ASC')
                ->paginate(10);
            $restaurants->{'restaurants'} = RestuarantsResources::collection($restaurants);
            $resp['restaurants'] = $restaurants;
        }

        return response()->json($resp);
    }

    public function offers()
    {
        $longitude = \request()->get('longitude') ?: '46.2620616';
        $latitude = \request()->get('longitude') ?: '24.7241504';
        $category_id = \request()->get('category_id') ?: @OffersCategories::orderBy('sort', 'asc')->first()->category_id;

        if (App::isLocale('ar')) {

            $offers_slider = OffersCategories::select('id', 'name')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->selectRaw('(CASE WHEN icon = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", icon)) END) AS icon')
                ->orderBy('sort', 'asc')
                ->get();

            $resp['offers_slider'] = OffersCategoriesResources::collection($offers_slider);

            //            $restaurant_categories =  Categories::select('id','name') ->orderBy('sort', 'asc')->get();
            //
            //            $resp['restaurant_categories'] = RestuarantCategoriesResources::collection($restaurant_categories);


            $restaurants = Restaurants::select(
                'id',
                'title',
                'description',
                'delivery_price',
                DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
                        * cos(radians(restaurants.latitude)) 
                        * cos(radians(restaurants.longitude) - radians(" . $longitude . ")) 
                        + sin(radians(" . $latitude . ")) 
                        * sin(radians(restaurants.latitude))) AS distance")
            )
                ->selectRaw('(CASE WHEN logo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", logo)) END) AS logo')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.restaurant_id=restaurants.id ) as restaurant_rate')
                ->where('approved', 1)
                ->where('stop', 0)
                ->where(function ($query) use ($category_id) {
                    if ($category_id) {
                        $query->whereIn('id', function ($query) use ($category_id) {
                            $query->select('restaurant_id')
                                ->from(with(new MeasurementUnitsCategories())->getTable())
                                ->where('category_id', $category_id);
                        });
                    }
                    if (\request()->get('restaurant_name')) {
                        $query->where('title', 'LIKE', "%" . \request()->get('restaurant_name') . "%");
                    }
                })
                ->orderBy("distance", 'ASC')
                ->paginate(10);

            $restaurants->{'restaurants'} = RestuarantsResources::collection($restaurants);
            $resp['restaurants'] = $restaurants;
        } else {

            $offers_slider = OffersCategories::select('id', 'name_en as name')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->selectRaw('(CASE WHEN icon = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", icon)) END) AS icon')
                ->orderBy('sort', 'asc')
                ->get();

            $resp['offers_slider'] = OffersCategoriesResources::collection($offers_slider);


            //            $restaurant_categories =  Categories::select('id','name_en as name') ->orderBy('sort', 'asc')->get();
            //            $resp['restaurant_categories'] = RestuarantCategoriesResources::collection($restaurant_categories);

            $restaurants = Restaurants::select(
                'id',
                'title_en as title',
                'description_en as description',
                'delivery_price',
                DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
                        * cos(radians(restaurants.latitude)) 
                        * cos(radians(restaurants.longitude) - radians(" . $longitude . ")) 
                        + sin(radians(" . $latitude . ")) 
                        * sin(radians(restaurants.latitude))) AS distance")
            )
                ->selectRaw('(CASE WHEN logo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", logo)) END) AS logo')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.restaurant_id=restaurants.id ) as restaurant_rate')
                ->where('approved', 1)
                ->where('stop', 0)
                ->where(function ($query) use ($category_id) {
                    if ($category_id) {
                        $query->whereIn('id', function ($query) use ($category_id) {
                            $query->select('restaurant_id')
                                ->from(with(new MeasurementUnitsCategories())->getTable())
                                ->where('category_id', $category_id);
                        });
                    }
                    if (\request()->get('restaurant_name')) {
                        $query->where('title_en', 'LIKE', "%" . \request()->get('restaurant_name') . "%");
                    }
                })
                ->orderBy("distance", 'ASC')
                ->paginate(10);
            $restaurants->{'restaurants'} = RestuarantsResources::collection($restaurants);
            $resp['restaurants'] = $restaurants;
        }

        return response()->json($resp);
    }

    public function restaurant($id = 0)
    {

        if (!Restaurants::where('approved', 1)->where('stop', 0)->where('id', $id)->first()) {
            return response()->json(
                ['message' => 'No restaurant'],
                400
            );
        }

        if (App::isLocale('ar')) {
            $restaurants = Restaurants::select('id', 'meal_menu_id', 'user_id', 'title', 'description', 'delivery_price', 'min_order_price', 'free_delivery', 'delivery_limit', 'longitude', 'latitude', 'address')
                ->selectRaw('(CASE WHEN logo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", logo)) END) AS logo')
                ->selectRaw('(CASE WHEN cover = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", cover)) END) AS cover')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.restaurant_id=restaurants.id ) as restaurant_rate')
                ->where('approved', 1)
                ->where('stop', 0)
                ->where('id', $id)
                ->first();
        } else {
            $restaurants = Restaurants::select('id', 'meal_menu_id', 'user_id', 'title_en as title', 'description', 'delivery_price', 'min_order_price', 'free_delivery', 'delivery_limit', 'longitude', 'latitude', 'address')
                ->selectRaw('(CASE WHEN logo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", logo)) END) AS logo')
                ->selectRaw('(CASE WHEN cover = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", cover)) END) AS cover')
                ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM ratings WHERE ratings.restaurant_id=restaurants.id ) as restaurant_rate')
                ->where('approved', 1)
                ->where('stop', 0)
                ->where('id', $id)
                ->first();
        }
        return response()->json(new RestuarantResources($restaurants));
    }


    public function product($id = 0, $delivery_time = "45")
    {

        if (!Products::where('id', $id)->first()) {
            return response()->json(
                ['message' => 'No product'],
                400
            );
        }

        if (App::isLocale('ar')) {
            $product = Products::select('id', 'calories', 'title', 'description', 'price', 'user_id')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->where('id', $id)
                ->with(['getPhotos' => function ($query) {
                    $query->select('id', 'product_id')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->first();
            $product->{'delivery_time'} = $delivery_time;
        } else {
            $product = Products::select('id', 'calories', 'title_en as title', 'description_en as description', 'price', 'user_id')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->where('id', $id)
                ->with(['getPhotos' => function ($query) {
                    $query->select('id', 'product_id')
                        ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
                }])
                ->first();
            $product->{'delivery_time'} = $delivery_time;
        }
        return response()->json(new ProductResources($product));
    }


    public function privacy()
    {
        if (App::isLocale('ar')) {
            return response()->json(
                [
                    'data' => Content::find(3)->content
                ]
            );
        } else {
            return response()->json(
                [
                    'data' => Content::find(3)->content_en
                ]
            );
        }
    }


    public function get_phone()
    {
        if (App::isLocale('ar')) {
            return response()->json(
                [
                    'status' => 200,
                    'data' => \App\Models\Settings::find(8)->value
                ]
            );
        } else {
            return response()->json(
                [
                    'status' => 200,
                    'data' => \App\Models\Settings::find(8)->value
                ]
            );
        }
    }


    public function about1()
    {
        if (App::isLocale('ar')) {

            $arr = [
                'content' => Content::find(2)->content,
                'email' => Settings::find(1)->value,
                'phone' => Settings::find(9)->value,
                'instagram_url' => Settings::find(2)->value,
                'twitter_url' => Settings::find(3)->value,
            ];

            return response()->json(
                [
                    'status' => 200,
                    'data' => $arr
                ]
            );
        } else {
            $arr = [
                'content' => Content::find(2)->content_en,
                'email' => Settings::find(1)->value,
                'phone' => Settings::find(9)->value,
                'instagram_url' => Settings::find(2)->value,
                'twitter_url' => Settings::find(3)->value,
            ];
            return response()->json(
                [
                    'status' => 200,
                    'data' => $arr
                ]
            );
        }
    }

    public function join_us(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|max:60|min:3',
            'email' => 'required|email|unique:join_us,email',
            'phone' => 'required|unique:join_us,phone',
            'phonecode' => 'required',
            'city_id' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {


            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        }


        $user = new JoinUs();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->message = $request->message;
        $user->city_id = $request->city_id;
        $user->phonecode = $request->phonecode;
        $user->save();


        return response()->json(
            [
                'status' => 200,
                'message' => trans('messages.your_request_sent_successfully'),
            ]
        );
    }


    public function contact_us(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }
        $user = new Contacts();
        $user->name = $request->name;
        $user->subject = $request->subject;
        $user->message = $request->message;
        $user->email = $request->email ?: '';
        $user->phone = $request->phone ?: '';
        $user->save();
        //        $system_email=Settings::find(19)->value;
        //        if($system_email){
        //            \Illuminate\Support\Facades\Mail::send('emails.contact', [ 'subject' => $user->subject,'contact_message'=>$user->message], function ($m) use ($user,$system_email) {
        //                $m->from($user->email, 'To Cars contact');
        //                $m->to($system_email, $user->username)->subject($user->subject);
        //            });
        //
        //        }

        return response()->json(
            [
                'status' => 200,
                'message' => __('messages.Message was sent successfully'),
            ]
        );
    }

    public function suggestions(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }
        $user = new Suggestions();
        $user->message = $request->message;
        $user->save();


        return response()->json(
            [
                'message' => "Suggestion was sent successfully .",
            ]
        );
    }

    public function request_hotel(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phonecode' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                400
            );
        }
        $user = new RequestProvider();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phonecode = $request->phonecode;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->details = $request->details;
        $user->save();


        return response()->json(
            [
                'message' => "Request was sent successfully .",
            ]
        );
    }

    public function get_bank_accounts()
    {
        $bank_accounts = BankAccounts::select('*')->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo')->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $bank_accounts
            ]
        );
    }


    public function membership_benefits()
    {

        if (App::isLocale('ar')) {
            $packages = Packages::select('id', 'name', 'currency_id', 'price')
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->get();
            $benefits = MembershipBenefits::select('id', 'name')->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' => [
                        'packages' => $packages,
                        'benefits' => $benefits

                    ]
                ]
            );
        } else {
            $packages = Packages::select('id', 'name_en as name', 'currency_id', 'price')
                ->with(['getCurrency' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->get();

            $benefits = MembershipBenefits::select('id', 'name_en as name')->get();

            return response()->json(
                [
                    'status' => 200,
                    'data' => [
                        'packages' => $packages,
                        'benefits' => $benefits
                    ]
                ]
            );
        }
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_id' => 'required',
        ]);
        if ($validator->fails()) {


            return response()->json(
                [
                    'status' => 400,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened'),
                ]
            );
        } else {

            if (App::getLocale() == "ar") {
                $reports = Reports::where('id', $request->report_id)
                    ->where('order_id', "!=", 0)
                    ->with(['getService' => function ($query) {
                        $query->select('id', 'name');
                    }])
                    //                    ->whereIn('order_id', function ($query) {
                    //                        $query->select('id')
                    //                            ->from(with(new Orders())->getTable());
                    ////                            ->where('price','!=', "");
                    //                    })
                    ->with(['getOrder.getCurrency' => function ($query) {
                        $query->select('id', 'name');
                    }])
                    ->select('id', 'date_of_report', 'service_id', 'order_id')
                    ->selectRaw('(CONCAT ("' . url('/') . '/' . App::getLocale() . '/single-report/", id)) as url')
                    ->orderBy('id', 'DESC')
                    ->paginate(10);
            } else {
                $reports = Reports::where('id', $request->report_id)
                    ->where('order_id', "!=", 0)
                    //                    ->whereIn('order_id', function ($query) {
                    //                        $query->select('id')
                    //                            ->from(with(new Orders())->getTable());
                    ////                            ->where('price','!=', "");
                    //                    })
                    ->with(['getService' => function ($query) {
                        $query->select('id', 'name_en as name');
                    }])
                    ->with(['getOrder.getCurrency' => function ($query) {
                        $query->select('id', 'name_en as name');
                    }])
                    ->select('id', 'date_of_report', 'service_id', 'order_id')
                    ->selectRaw('(CONCAT ("' . url('/') . '/' . App::getLocale() . '/single-report/", id)) as url')
                    ->orderBy('id', 'DESC')
                    ->paginate(10);
            }
            if ($reports) {
                return response()->json(
                    [
                        'status' => 200,
                        'data' => $reports,
                    ]
                );
            } else {

                return response()->json(
                    [
                        'status' => 400,
                        'message' => trans('messages.no_reports_found'),
                    ]
                );
            }
        }
    }


    public function hall_ratings(Request $request)
    {

        $hall = Hall::find($request->hall_id);
        if (!$hall) {
            return response()->json([
                'message' => 'Sorry wrong hall'
            ], 400);
        }
        $ratings = Rating::where('hall_id', $hall->id)->with(['getUser' => function ($query) {
            $query->select('username', 'id')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
        }])->paginate(10);


        //        $user= new UsersResource($user);


        return response()->json($ratings);
    }

    public function returnPayment(Request $request)
    {
        return $request->order_id ?: 0;
    }

    //    public function return_url(Request $request){
    //        $pt = paytabs::getInstance("tocars10@gmail.com", "uNwUWwYU49CtBq8dJhcaBxBoluZw3kmx7Sr4pqZuzBXb26oMnBWY8xnu1n05ImCQf4UFvU95w7gydEkAghzoOXSQkTV2gySwDNb8");
    //        $result = $pt->verify_payment($request->payment_reference);
    //        if($result->response_code == 100){
    //            $order=Orders::find($result->reference_no);
    //            if($order){
    //                $order->payment_method=2;
    //                $order->save();
    //                $user_id=$order->user_id;
    //                $select_title = App::getLocale() == "en" ? 'title' : 'title_en as title';
    //                $objects = CartItem::select('cart_items.shop_id', 'cart_items.user_id', 'cart_items.type', 'users.username as shop_name','users.shipment_price','users.texes')
    //                    ->join('users', 'cart_items.shop_id', 'users.id')
    //                    ->where('cart_items.order_id', 0)
    //                    ->where('cart_items.user_id', $user_id)
    //                    ->groupBy('users.id')->get();
    //
    //                foreach ($objects as $object) {
    //                    $shipment = new OrderShipments();
    //                    $shipment->order_id = $order->id;
    //                    $shipment->user_id = $user_id;
    //                    $shipment->shop_id = $object->shop_id;
    //                    $shipment->delivery_date = 'after two days';
    //                    $shipment->delivery_price = $object->shipment_price;
    //                    $shipment->taxes = $object->taxes;
    //
    //                    $shipment->status = 1;
    //                    $shipment->save();
    //                    $cart_items=[];
    //                    $pricing_items=CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'pricing_order_parts.part_name as title', 'cart_items.shop_id')
    //                        ->where('cart_items.type', 2)
    //                        ->where('cart_items.order_id', 0)
    //                        ->where('shop_id',$object->shop_id)
    //                        ->where('cart_items.user_id', $object->user_id)
    //                        ->selectRaw('(CASE WHEN pricing_order_parts.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", pricing_order_parts.photo)) END) AS photo')
    //                        ->join('pricing_offers', 'cart_items.item_id', 'pricing_offers.id')
    //
    //                        ->join('pricing_order_parts', 'pricing_order_parts.id', 'pricing_offers.part_id')->get();
    //
    //                    $shop_items=CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
    //                        ->where('cart_items.type', 1)
    //                        ->where('cart_items.order_id', 0)
    //                        ->where('shop_id',$object->shop_id)
    //                        ->where('cart_items.user_id', $object->user_id)
    //                        ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
    //                        ->join('products', 'cart_items.item_id', 'products.id')->get();
    //
    //                    $cart_items=$shop_items->merge($pricing_items);
    //
    //                    foreach ($cart_items as $item) {
    //                        $cart_item = CartItem::find($item->id);
    //                        if ($cart_item) {
    //                            $cart_item->order_id = $order->id;
    //                            $cart_item->shipment_id = $shipment->id;
    //                            $cart_item->status=1;
    //                            $cart_item->save();
    //                        }
    //                    }
    //                    foreach ($shop_items as $shop_item){
    //                        $product=Products::find($shop_item->item_id);
    //                        $product->quantity=$product->quantity-$shop_item->quantity;
    //                        $product->save();
    //                    }
    //
    //                    $user=User::find($user_id);
    //                    $notification55 = new Notification();
    //                    $notification55->sender_id = $user->id;
    //                    $notification55->reciever_id = $object->shop_id;
    //                    $notification55->ads_id = $shipment->id;
    //                    $notification55->type = 13;
    //                    $notification55->url="/provider-panel/order-details/".$shipment->id;
    //                    $notification55->message = "قام " . $user->username . " بشراء منتجات من متجرك ";
    //                    $notification55->message_en = @$user->username . " bought products from your shop.";
    //                    $notification55->save();
    //                    $optionBuilder = new OptionsBuilder();
    //                    $optionBuilder->setTimeToLive(60 * 20);
    //
    //                    if ($order->getUser->lang == "en") {
    //                        $notification_title = "new order";
    //                        $notification_message = $notification55->message_en;
    //                    } else {
    //                        $notification_title = "طلب شراء جديد";
    //                        $notification_message = $notification55->message;
    //                    }
    //                    $notificationBuilder = new PayloadNotificationBuilder($notification_title);
    //                    $notificationBuilder->setBody($notification_message)
    //                        ->setSound('default');
    //                    $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');
    //
    //
    //                    $dataBuilder = new PayloadDataBuilder();
    //                    $dataBuilder->addData(['data' => [
    //                        'notification_type' => (int)$notification55->type,
    //                        'notification_title' => $notification_title,
    //                        'notification_message' => $notification_message,
    //                        'notification_data' => new NotificationsResource($notification55)
    //                    ]
    //                    ]);
    //
    //                    $option = $optionBuilder->build();
    //                    $notification = $notificationBuilder->build();
    //                    $data = $dataBuilder->build();
    //
    //
    //                    $token = @$notification55->getReciever->devices->count();
    //                    $tokens = DeviceTokens::where('user_id', $notification55->reciever_id)->pluck('device_token')->toArray();
    //                    $notification_ = @$notification55->getReciever->notification;
    //
    //                    if ($token > 0 && $notification_) {
    //                        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
    //                        $downstreamResponse->numberSuccess();
    //                        $downstreamResponse->numberFailure();
    //                        $downstreamResponse->numberModification();
    //                    }
    //
    //                }
    //
    //                $c_c = CreditCards::where('user_id',$order->user_id)->where('pt_token',$request->pt_token)->first();
    //                if(!$c_c){
    //                    $c_c = new CreditCards();
    //                    $c_c -> user_id = $order->user_id?:0;
    //                    $c_c ->pt_customer_password = $request->pt_customer_password?:'';
    //                    $c_c ->pt_customer_email = $request->pt_customer_email?:'';
    //                    $c_c ->pt_token = $request->pt_token?:'';
    //                    $c_c->save();
    //                }
    //            }
    //            return redirect('/api/v1/payment_done');
    //        }
    //        return view('payment-error')->with('pay_error',$result->result);
    //    }
    //    public function pricingSendPayOrder(Request $request){
    //        $pt = paytabs::getInstance("tocars10@gmail.com", "OkAgQvMrq3wggFEt7VTTAwgv33A4kwJ8QjpHPb9gfFxvkbZs7SlHoBdsFNwZkf8bzxTpJ3GYIdt3pswwkRdJtqb9eH9yXhKwPDWy");
    //        $result = $pt->verify_payment($request->payment_reference);
    //        if($result->response_code == 100){
    //            $order=PricingOrder::find($result->reference_no);
    //            if($order){
    //                $order->payment_method=2;
    //                $order->save();
    //                $user=User::find($order->user_id);
    //                $shops = User::where('accept_pricing', 1)->where('block', 0)->get();
    //                $notify_message = 'قام المستخدم ' . $user->username . ' باضافة طلب جديد لتسعير قطع الغيار';
    //                foreach ($shops as $shop) {
    //                    $notify = new Notification();
    //                    $notify->sender_id = $user->id;
    //                    $notify->reciever_id = $shop->id;
    //                    $notify->type = 3;
    //                    $notify->url = '/provider-panel/pricing-orders/' . $order->id;
    //                    $notify->message = $notify_message;
    //                    $notify->message_en = 'new parts pricing order by ' . $user->username;
    //                    $notify->ads_id = $order->id;
    //                    $notify->save();
    //
    //                }
    //                $optionBuilder = new OptionsBuilder();
    //                $optionBuilder->setTimeToLive(60 * 20);
    //                $optionBuilder->setContentAvailable(true);
    //
    //                $notification_title = "طلب تسعير جديد";
    //
    //                $notificationBuilder = new PayloadNotificationBuilder($notification_title);
    //                $notificationBuilder->setBody($notify_message)
    //                    ->setSound('default');
    //                $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');
    //
    //
    //                $dataBuilder = new PayloadDataBuilder();
    //
    //                $dataBuilder->addData(['data' => [
    //                    'notification_type' => 3,
    //                    'notification_title' => $notification_title,
    //                    'notification_message' => $notify_message,
    //                    'key' => $order->id,
    //                    'notification_data' => '{ads_id:' . $order->id . '}'
    //                ]
    //                ]);
    //                $option = $optionBuilder->build();
    //                $notification = $notificationBuilder->build();
    //                $data = $dataBuilder->build();
    //
    //                $tokens = DeviceTokens::whereIn('user_id', function ($query) {
    //                    $query->select('id')
    //                        ->from(with(new User())->getTable())
    //                        ->where('accept_pricing', 1)
    //                        ->where('block', 0)
    //                        ->where('notification', 1);
    //                })->pluck('device_token')->toArray();
    //                if (count($tokens)) {
    //
    //                    $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
    //                    $downstreamResponse->numberSuccess();
    //                    $downstreamResponse->numberFailure();
    //                    $downstreamResponse->numberModification();
    //                }
    //                $c_c = CreditCards::where('user_id',$order->user_id)->where('pt_token',$request->pt_token)->first();
    //                if(!$c_c){
    //                    $c_c = new CreditCards();
    //                    $c_c -> user_id = $order->user_id?:0;
    //                    $c_c ->pt_customer_password = $request->pt_customer_password?:'';
    //                    $c_c ->pt_customer_email = $request->pt_customer_email?:'';
    //                    $c_c ->pt_token = $request->pt_token?:'';
    //                    $c_c->save();
    //                }
    //            }
    //            return redirect('/api/v1/payment_done');
    //        }
    //        return view('payment-error')->with('pay_error',$result->result);
    //    }
    //    public function damageSendPayOrder(Request $request){
    //        $pt = paytabs::getInstance("tocars10@gmail.com", "OkAgQvMrq3wggFEt7VTTAwgv33A4kwJ8QjpHPb9gfFxvkbZs7SlHoBdsFNwZkf8bzxTpJ3GYIdt3pswwkRdJtqb9eH9yXhKwPDWy");
    //        $result = $pt->verify_payment($request->payment_reference);
    //        if($result->response_code == 100){
    //            $order=DamageEstimate::find($result->reference_no);
    //            if($order){
    //                $order->payment_method=2;
    //                $order->save();
    //                $user=User::find($order->user_id);
    //                $shops = User::where('accept_estimate', 1)->where('block', 0)->get();
    //                $notify_message = 'قام المستخدم ' . $user->username . ' باضافة طلب جديد لتقدير الاضرار';
    //                foreach ($shops as $shop) {
    //                    $notify = new Notification();
    //                    $notify->sender_id = $user->id;
    //                    $notify->reciever_id = $shop->id;
    //                    $notify->type = 4;
    //                    $notify->url = '/provider-panel/damage-estimates/' . $order->id;
    //                    $notify->message = $notify_message;
    //                    $notify->message_en = 'new damage estimate order by ' . $user->username;
    //                    $notify->ads_id = $order->id;
    //                    $notify->save();
    //
    //                }
    //                $optionBuilder = new OptionsBuilder();
    //                $optionBuilder->setTimeToLive(60 * 20);
    //                $optionBuilder->setContentAvailable(true);
    //
    //                $notification_title = "طلب تقدير اضرار جديد";
    //
    //                $notificationBuilder = new PayloadNotificationBuilder($notification_title);
    //                $notificationBuilder->setBody($notify_message)
    //                    ->setSound('default');
    //                $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');
    //
    //
    //                $dataBuilder = new PayloadDataBuilder();
    //
    //                $dataBuilder->addData(['data' => [
    //                    'notification_type' => 4,
    //                    'notification_title' => $notification_title,
    //                    'notification_message' => $notify_message,
    //                    'key' => $order->id,
    //                    'notification_data' => '{ads_id:' . $order->id . '}'
    //                ]
    //                ]);
    //                $option = $optionBuilder->build();
    //                $notification = $notificationBuilder->build();
    //                $data = $dataBuilder->build();
    //
    //                $tokens = DeviceTokens::whereIn('user_id', function ($query) use ($shop) {
    //                    $query->select('id')
    //                        ->from(with(new User())->getTable())
    //                        ->where('accept_estimate', 1)
    //                        ->where('block', 0)
    //                        ->where('notification', 1);
    //                })->pluck('device_token')->toArray();
    //                if (count($tokens)) {
    //                    $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
    //                    $downstreamResponse->numberSuccess();
    //                    $downstreamResponse->numberFailure();
    //                    $downstreamResponse->numberModification();
    //
    //                }
    //                $c_c = CreditCards::where('user_id',$order->user_id)->where('pt_token',$request->pt_token)->first();
    //                if(!$c_c){
    //                    $c_c = new CreditCards();
    //                    $c_c -> user_id = $order->user_id?:0;
    //                    $c_c ->pt_customer_password = $request->pt_customer_password?:'';
    //                    $c_c ->pt_customer_email = $request->pt_customer_email?:'';
    //                    $c_c ->pt_token = $request->pt_token?:'';
    //                    $c_c->save();
    //                }
    //            }
    //            return redirect('/api/v1/payment_done');
    //        }
    //
    //        return view('payment-error')->with('pay_error',$result->result);
    //    }


    // rajhi payment handel


    public function shopPayment($order_id)
    {
        if ($order_id) {


            $order = Orders::find($order_id);
            if ($order) {
                $order->payment_method = 2;
                $order->save();
                $user_id = $order->user_id;
                $select_title = App::getLocale() == "en" ? 'title' : 'title_en as title';
                $objects = CartItem::select('cart_items.shop_id', 'cart_items.user_id', 'cart_items.type', 'users.username as shop_name', 'users.shipment_price')
                    ->join('users', 'cart_items.shop_id', 'users.id')
                    ->where('cart_items.order_id', 0)
                    ->where('cart_items.user_id', $user_id)
                    ->groupBy('users.id')->get();

                foreach ($objects as $object) {
                    $shipment = new OrderShipments();
                    $shipment->order_id = $order->id;
                    $shipment->user_id = $user_id;
                    $shipment->shop_id = $object->shop_id;
                    $shipment->delivery_date = 'after two days';
                    $shipment->delivery_price = $object->shipment_price;
                    $shipment->taxes = $object->taxes ?: '';

                    $shipment->status = 1;
                    $shipment->save();
                    $cart_items = [];
                    $pricing_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'pricing_order_parts.part_name as title', 'cart_items.shop_id')
                        ->where('cart_items.type', 2)
                        ->where('cart_items.order_id', 0)
                        ->where('shop_id', $object->shop_id)
                        ->where('cart_items.user_id', $object->user_id)
                        ->selectRaw('(CASE WHEN pricing_order_parts.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", pricing_order_parts.photo)) END) AS photo')
                        ->join('pricing_offers', 'cart_items.item_id', 'pricing_offers.id')
                        ->join('pricing_order_parts', 'pricing_order_parts.id', 'pricing_offers.part_id')->get();

                    $shop_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id')
                        ->where('cart_items.type', 1)
                        ->where('cart_items.order_id', 0)
                        ->where('shop_id', $object->shop_id)
                        ->where('cart_items.user_id', $object->user_id)
                        ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                        ->join('products', 'cart_items.item_id', 'products.id')->get();

                    $cart_items = $shop_items->merge($pricing_items);

                    foreach ($cart_items as $item) {
                        $cart_item = CartItem::find($item->id);
                        if ($cart_item) {
                            $cart_item->order_id = $order->id;
                            $cart_item->shipment_id = $shipment->id;
                            $cart_item->status = 1;
                            $cart_item->save();
                        }
                    }
                    foreach ($shop_items as $shop_item) {
                        $product = Products::find($shop_item->item_id);
                        $product->quantity = $product->quantity - $shop_item->quantity;
                        $product->save();
                    }

                    $user = User::find($user_id);
                    $notification55 = new Notification();
                    $notification55->sender_id = $user->id;
                    $notification55->reciever_id = $object->shop_id;
                    $notification55->ads_id = $shipment->id;
                    $notification55->type = 13;
                    $notification55->url = "/provider-panel/order-details/" . $shipment->id;
                    $notification55->message = "قام " . $user->username . " بشراء منتجات من متجرك ";
                    $notification55->message_en = @$user->username . " bought products from your shop.";
                    $notification55->save();
                    $optionBuilder = new OptionsBuilder();
                    $optionBuilder->setTimeToLive(60 * 20);

                    if ($order->getUser->lang == "en") {
                        $notification_title = "new order";
                        $notification_message = $notification55->message_en;
                    } else {
                        $notification_title = "طلب شراء جديد";
                        $notification_message = $notification55->message;
                    }
                    $notificationBuilder = new PayloadNotificationBuilder($notification_title);
                    $notificationBuilder->setBody($notification_message)
                        ->setSound('default');
                    $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');


                    $dataBuilder = new PayloadDataBuilder();
                    $dataBuilder->addData([
                        'data' => [
                            'notification_type' => (int)$notification55->type,
                            'notification_title' => $notification_title,
                            'notification_message' => $notification_message,
                            'notification_data' => new NotificationsResource($notification55)
                        ]
                    ]);

                    $option = $optionBuilder->build();
                    $notification = $notificationBuilder->build();
                    $data = $dataBuilder->build();


                    $token = @$notification55->getReciever->devices->count();
                    $tokens = DeviceTokens::where('user_id', $notification55->reciever_id)->pluck('device_token')->toArray();
                    $notification_ = @$notification55->getReciever->notification;

                    if ($token > 0 && $notification_) {
                        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                        $downstreamResponse->numberSuccess();
                        $downstreamResponse->numberFailure();
                        $downstreamResponse->numberModification();
                    }
                }
            }
            return redirect('/api/v1/payment_done');
        }
        return redirect('/api/v1/payment-error');
    }

    public function pricingSendPaymentOrder($order_id)
    {
        if ($order_id) {
            $order = PricingOrder::find($order_id);
            if ($order) {
                $order->payment_method = 2;
                $order->save();
                $is_review = Settings::where('option_name', 'pricing_review')->first();
                if (!$is_review || $is_review == '0') {
                    return redirect('/api/v1/payment_done');
                }
                $user = User::find($order->user_id);
                $shops = User::where('accept_pricing', 1)->where('block', 0)->get();
                $notify_message = 'قام المستخدم ' . $user->username . ' باضافة طلب جديد لتسعير منتجات';
                foreach ($shops as $shop) {
                    $notify = new Notification();
                    $notify->sender_id = $user->id;
                    $notify->reciever_id = $shop->id;
                    $notify->type = 3;
                    $notify->url = '/provider-panel/pricing-orders/' . $order->id;
                    $notify->message = $notify_message;
                    $notify->message_en = 'new parts pricing order by ' . $user->username;
                    $notify->ads_id = $order->id;
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

                $dataBuilder->addData([
                    'data' => [
                        'notification_type' => 3,
                        'notification_title' => $notification_title,
                        'notification_message' => $notify_message,
                        'key' => $order->id,
                        'notification_data' => '{ads_id:' . $order->id . '}'
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
            }
            return redirect('/api/v1/payment_done');
        }
        return redirect('/api/v1/payment-error');
    }

    public function damageSendPaymentOrder($order_id)
    {
        if ($order_id) {
            $order = DamageEstimate::find($order_id);
            if ($order) {
                $order->payment_method = 2;
                $order->save();
                $is_review = Settings::where('option_name', 'damage_review')->first();
                if (!$is_review || $is_review == '0') {
                    return redirect('/api/v1/payment_done');
                }
                $user = User::find($order->user_id);
                $shops = User::where('accept_estimate', 1)->where('block', 0)->get();
                $notify_message = 'قام المستخدم ' . $user->username . ' باضافة طلب خدمة جديد ';
                foreach ($shops as $shop) {
                    $notify = new Notification();
                    $notify->sender_id = $user->id;
                    $notify->reciever_id = $shop->id;
                    $notify->type = 4;
                    $notify->url = '/provider-panel/damage-estimates/' . $order->id;
                    $notify->message = $notify_message;
                    $notify->message_en = 'new service order by ' . $user->username;
                    $notify->ads_id = $order->id;
                    $notify->save();
                }
                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60 * 20);
                $optionBuilder->setContentAvailable(true);

                $notification_title = "طلب خدمة جديد";

                $notificationBuilder = new PayloadNotificationBuilder($notification_title);
                $notificationBuilder->setBody($notify_message)
                    ->setSound('default');
                $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');


                $dataBuilder = new PayloadDataBuilder();

                $dataBuilder->addData([
                    'data' => [
                        'notification_type' => 4,
                        'notification_title' => $notification_title,
                        'notification_message' => $notify_message,
                        'key' => $order->id,
                        'notification_data' => '{ads_id:' . $order->id . '}'
                    ]
                ]);
                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                $tokens = DeviceTokens::whereIn('user_id', function ($query) use ($shop) {
                    $query->select('id')
                        ->from(with(new User())->getTable())
                        ->where('accept_estimate', 1)
                        ->where('block', 0)
                        ->where('notification', 1);
                })->pluck('device_token')->toArray();
                if (count($tokens)) {
                    $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                    $downstreamResponse->numberSuccess();
                    $downstreamResponse->numberFailure();
                    $downstreamResponse->numberModification();
                }
            }
            return redirect('/api/v1/payment_done');
        }
        return redirect('/api/v1/payment-error');
    }

    public function paymentError()
    {
        return view('payment-error');
    }

    public function paymentDone()
    {
        return view('payment-done');
    }

    public function getRegions(Request $request)
    {

        $select_name = App::getLocale() == "ar" ? 'name' : 'name_en as name';
        $regions = Regions::select('id', $select_name)
            ->with(['getStates' => function ($query) use ($select_name) {
                $query->select('id', 'region_id', $select_name);
            }])->where('country_id', 188)->where('is_archived', 0)->get();
        return response()->json(
            [
                'regions' => $regions,
            ],
            202
        );
    }
}
