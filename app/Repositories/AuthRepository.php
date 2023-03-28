<?php

namespace App\Repositories;


use App\Entities\LocaleType;
use App\Entities\SmsDetails;
use App\Entities\UserType;
use App\Http\Requests\Api\UserForm;
use App\Http\Resources\ProductResources;
use App\Http\Resources\ProductsResource;
use App\Http\Resources\UsersResource;
use App\Jobs\SendSmsAndEmail;
use App\Models\Addresses;
use App\Models\CartItem;
use App\Models\Categories;
use App\Models\DeviceTokens;
use App\Models\Favorite;
use App\Models\MotivationCobon;
use App\Models\Products;
use App\Models\ProductSubCategories;
use App\Models\Settings;
use App\Models\Shop_product;
use App\Models\Slider;
use App\Models\User;
use App\Repositories\Community\SocialRepository;
use App\Repositories\HomeRepository;
use App\Repositories\Utils\UtilsRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use phpDocumentor\Reflection\Types\Self_;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthRepository
{
    public static function checkValue($value)
    {
        $value = trim($value, " ");;
        $value_column = '';
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $value_column = 'email';
            $user = User::where($value_column, $value)->first();
            return $value_column;
        } else {
            $value_column = 'phone';
            $phone = (new self)->convertNum($value);
            $phone1 = (new self)->convertNum(ltrim($phone, '0'));
            $phone2 = '0' . $phone1;
            $value = $phone1;
            $user = User::where($value_column, $phone1)->orWhere($value_column, $phone2)->first();
            return $value_column;
        }
        return 400;
    }

    public static function checkUser($request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                    'status' => 400,
                ], 400
            );
        }
        $value = trim($request->value, " ");;
        $value_column = '';
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $value_column = 'email';
            $validator = Validator::make($request->all(), [
                'value' => 'email',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'message' => $validator->errors()->first(),
                        'status' => 400,
                    ], 400
                );
            }
            $user = User::where($value_column, $value)->first();
        } else {
            $validator = Validator::make($request->all(), [
                'value' => 'numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'message' => $validator->errors()->first(),
                        'status' => 400,

                    ], 400
                );
            }
            $value_column = 'phone';
            $phone = (new self)->convertNum($value);
            $phone1 = (new self)->convertNum(ltrim($phone, '0'));
            $phone2 = '0' . $phone1;
            $value = $phone1;
            $user = User::where($value_column, $phone1)->orWhere($value_column, $phone2)->first();

        }

//        $user = User::where($value_column, $value)->first();
        if ($user) {

            if ($value_column == "phone" && $user->active_phone == 0) {
                return response()->json([
                    'message' => 'الجوال غير مفعل',
                    'type' => $value_column,

                ], 202);
            } elseif ($value_column == "email" && $user->active_email == 0) {
                return response()->json([
                    'message' => 'البريد الألكتروني غير مفعل',
                    'type' => $value_column,
                ], 202);
            } else {
                return response()->json([
                    'message' => __('messages.this_user_exists'),
                    'type' => $value_column,

                ], 200);

            }
        } else {

            return response()->json([
                'message' => __('messages.go_to_register_page'),
                'type' => $value_column,
            ], 400);
        }
    }

    public static function login($request, $platform = 'api')
    {
        $value = trim($request->value, " ");;
        $password = $request->password;

        $value_column = '';
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $value_column = 'email';
        } else {
            $value_column = 'phone';

            $phone = (new self)->convertNum($value);
            $phone1 = (new self)->convertNum(ltrim($phone, '0'));
            $value = $phone1;
        }
        if ($platform == 'api') {
            if (JWTAuth::attempt([$value_column => $value, 'password' => $password, 'block' => 1])) {
                if ($token = JWTAuth::getToken()) {
                    JWTAuth::invalidate($token);
                }
                return response()->json(['message' => __('messages.you_are_blocked_from_admin'), 'status' => 400], 400);
            } elseif (JWTAuth::attempt([$value_column => $value, 'password' => $password, 'active_' . $value_column => 0])) {
                if ($token = JWTAuth::getToken()) {
                    JWTAuth::invalidate($token);
                }
                return response()->json(['message' => __('messages.you_are_not_activated_yet'), 'status' => 403, 'type' => $value_column], 403);
            } elseif (JWTAuth::attempt([$value_column => $value, 'password' => $password, 'active_' . $value_column => 1, 'block' => 0])) {
                $token = JWTAuth::getToken();
            } else {
                return response()->json(['message' => __('messages.incorrect_login_data')], 400);
            }
        } else {
            if (User::where([$value_column => $value, 'block' => 1])->first()) {
                return response()->json(['message' => __('messages.you_are_blocked_from_admin'), 'status' => 400], 400);
            } elseif ($userVerify = User::where([$value_column => $value, 'active_' . $value_column => 0])->first()) {
                if (empty($userVerify->activation_code)) {
                    $activation_code = self::createVerificationCode();
                    $userVerify->activation_code = $activation_code;
                    $userVerify->save();
                    $activation_code = $userVerify->activation_code;
                    if ($value_column == 'email') {
                        try {
                            UtilsRepository::sendEmail($userVerify->email, __('messages.activate_account'),
                                view('emails.reminder', compact('activation_code'))->render());
                        } catch (\Exception $ex) {
                        }
                    } elseif ($value_column == 'phone') {
                        $phone = (new self)->convertNum($value);
                        $phone1 = (new self)->convertNum(ltrim($phone, '0'));
                        $phone_value = $phone1;
                        $smsMessage = 'كود التفعيل في تطبيق تريتاب : ' . $activation_code;
                        $phone_number = '966' . ltrim($phone_value, '0');
                        $resp = (new self)->send4SMS($smsMessage, $phone_number);
                    }
                }
                return response()->json(['message' => __('messages.you_are_not_activated_yet'), 'status' => 403, 'type' => $value_column], 403);
            } elseif (Auth::guard('client')->attempt([$value_column => $value, 'password' => $password, 'active_' . $value_column => 1, 'block' => 0])) {
                //login here
            } else {
                return response()->json(['message' => __('messages.incorrect_login_data')], 400);
            }
        }

        $user = User::where($value_column, $value)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->selectRaw('(SELECT SUM(price) from balance where balance.user_id=users.id)as balance')
            ->with(['country' => function ($query) {
                if (App::getLocale() == "ar") {
                    $query->select('id', 'name', 'phonecode');
                } else {
                    $query->select('id', 'name_en as name', 'phonecode');
                }
            }, 'defaultAddress' => function ($query) {
                $query->select('user_id', 'id', 'street', 'address_type', 'longitude', 'latitude')
                    ->with(['user' => function ($query) {
                        $query->select('username');
                    }]);
            }])
            ->first();


        $user->last_login = date('Y-m-d H:i:s');

        $user->save();

        if ($request->device_token) {
            if ($device = DeviceTokens::where('device_token', $request->device_token)->first()) {
                $device->user_id = $user->id;
                $device->ios_token = (isset($request->ios_token)) ? $request->ios_token : null;
                $device->save();
            } else {
                $device = new DeviceTokens();
                $device->device_token = $request->device_token;
                $device->user_id = $user->id;
                $device->ios_token = (isset($request->ios_token)) ? $request->ios_token : null;
                $device->save();
            }

        }
        if ($platform == 'api') {
            $token = JWTAuth::fromUser($user);
            $user->{"token"} = $token;
        } else {
            auth('client')->loginUsingId($user->id);
        }
        $user->save();
        return response()->json(new UsersResource($user));
    }

    public static function register($request)
    {

        $phone_value = '';
        if ($request->phone) {
            $phone = (new self)->convertNum($request->phone);
            $phone1 = (new self)->convertNum(ltrim($phone, '0'));
            $phone_value = $phone1;
            $request->merge([
                'phone' => ($phone1),
            ]);
            $phone_length = strlen($phone1);
            if ($phone_length != 9) {
                return response()->json(
                    [
                        'message' => __('trans.invalid Phone number'),
                        'status' => 400
                    ]
                    , 400);
            }
        }

        $LoginRequest = new UserForm();

        $validator = Validator::make($request->all(), $LoginRequest->rules(), $LoginRequest->messages());

//        $validator = Validator::make($request->all(), [
//            'email' => 'email|unique:users,email',
//            'phone' => 'required|unique:users,phone',
//            'username' => 'required',
//            'password' => 'required|min:6',
//            'password_confirmation' => 'required|same:password',
//            'type' => 'required',
//
////            'photo' => $request->photo ? 'required|image' : '',
//        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                    'status' => 400
                ]
                , 400);
        }

        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email ?: '';
        $user->phone = $phone_value ?: '';
        $user->activate = 0;
        $user->user_type_id = 5;
//        $user->birth_date=$request->birth_date;
        $user->password = bcrypt($request->password);
        $activation_code = self::createVerificationCode();
        $user->activation_code = $activation_code;

        $user->save();

        $request->type = 'phone';

//        if ($request->type == 'email') {
            try {
                UtilsRepository::sendEmail($user->email, __('messages.activate_account'),
                    view('emails.reminder', compact('activation_code'))->render());
            } catch (\Exception $ex) {
            }
//            $response_message = __('messages.you_are_registered_successfully_activate_your_account_email');
//        } elseif ($request->type == 'phone') {
            $smsMessage = 'كود التفعيل في تطبيق تريتاب : ' . $activation_code;
            $phone_number = '966' . ltrim($phone_value, '0');
            $resp = (new self)->send4SMS($smsMessage, $phone_number);
            $response_message = __('messages.you_are_registered_successfully_activate_your_account_phone');
//        }
        return response()->json(
            [
                'status' => 200,
                'message' => $response_message,
//                'activation_code' => $activation_code,
                'activation_code' => ''
            ], 200);
    }

    public static function activate($request, $platform = 'api')
    {
        $user = null;
        $activation_code = $request->activation_code;
        if ($request->type == 'email') {
            $user = User::select('*')->where('email', $request->value)
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->where('activation_code', $activation_code)->first();
            if ($user) $user->active_email = 1;
        }
        if ($request->type == 'phone' || $request->type == null) {
            $phone = (new self)->convertNum($request->value);
            $phone1 = (new self)->convertNum(ltrim($phone, '0'));
            $phone2 = "0" . $phone1;

            $user = User::select('*')->where('activation_code', $activation_code)
                ->where(function ($query) use ($phone1, $phone2) {
                    $query->orWhere('phone', $phone1)
                        ->orWhere('phone', $phone2);
                })
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->with(['defaultAddress' => function ($query) {
                    $query->select('user_id', 'id', 'street', 'address_type', 'longitude', 'latitude')
                        ->with(['user' => function ($query) {
                            $query->select('username');
                        }]);
                }])
                ->first();
            if ($user) {
                $user->active_phone = 1;
            }
        }

        if (!$user) {
            return response()->json([
                'message' => __('messages.incorrect_activation_code'),
                'status' => 400,
            ], 400);
        }
        $user->activation_code = '';
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
        if ($platform == 'api') {
            $token = JWTAuth::fromUser($user);
            $user->{"token"} = $token;
        } else {
            auth('client')->loginUsingId($user->id);
        }
        $user->save();
        return response()->json(new UsersResource($user));

    }

    public static function createVerificationCode()
    {
        $activation_code = UtilsRepository::createVerificationCode(0, 4);
        $user = User::where(['activation_code' => $activation_code])->first();
        if ($user) {
            $activation_code = self::createVerificationCode();
        }
        return $activation_code;
    }

    public static function SendActivationCode($request)
    {
        $activation_code = self::createVerificationCode();

        if ($request->type == 'email') {
            $user = User::where('email', $request->value)->first();
            if (!$user) {
                return response()->json(
                    [
                        'message' => 'user not found',
                    ], 400
                );

            }
            $user->activation_code = $activation_code;
            $user->save();
            try {
                UtilsRepository::sendEmail($user->email, __('messages.activate_account'),
                    view('emails.reminder', compact('activation_code'))->render());
            } catch (\Exception $ex) {

            }
            $response_message = __('messages.you_are_registered_successfully_activate_your_account_email');
        } elseif ($request->type == 'phone') {
            $phone = (new self)->convertNum($request->value);
            $phone1 = (new self)->convertNum(ltrim($phone, '0'));
            $phone2 = "0" . $phone1;

            $user = User::where('phone', $phone1)->orWhere('phone', $phone2)->first();
            if (!$user) {
                return response()->json(
                    [
                        'message' => __('messages.incorrect_activation_code'),
                    ], 400
                );
            }
            $user->activation_code = $activation_code;
            $user->save();

            $smsMessage = 'كود تفعيل رقم الجوال لتطبيق Treatab : ' . $activation_code;
            $phone_number = '966' . ltrim($user->phone, '0');
            $resp = (new self)->send4SMS($smsMessage, $phone_number);
            $response_message = __('messages.you_are_registered_successfully_activate_your_account_phone');
        }
        return response()->json(
            [
                'status' => 200,
                'message' => $response_message,
//                'activation_code' => $activation_code,
                'activation_code' => ''
            ], 200);

    }

    public static function setPassword($request, $platform = 'api')
    {
        $activation_code = $request->activation_code;
        $value = $request->value;
        $passsword = bcrypt($request->password);
        $value_column = 'email';
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $value_column = 'email';
            $validator = Validator::make($request->all(), [
                'value' => 'email',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'message' => $validator->errors()->first(),
                    ], 400
                );
            }

        } else {
            $validator = Validator::make($request->all(), [
                'value' => 'numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'message' => $validator->errors()->first(),
                    ], 400
                );
            }

            $value_column = 'phone';
            $phone = (new self)->convertNum($value);
            $phone1 = (new self)->convertNum(ltrim($phone, '0'));
            $value = $phone1;
        }


        $validator = Validator::make($request->all(), [
            'value' => 'required',
            'activation_code' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ], 400
            );
        }
        $user = User::where('activation_code', $activation_code)->where($value_column, $value)->first();
        if (!$user) {
            return response()->json(
                [
                    'message' => trans('messages.error_code'),
                ], 400);
        } else {
            $user =
                User::where('id', $user->id)
                    ->select('*')->first();
            $user->last_login = date('Y-m-d H:i:s');
            $user->activation_code = '';
            $user->password = $passsword;
            $user->save();

            $user = User::where($value_column, $value)
                ->select('*')
                ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
                ->with(['country' => function ($query) {
                    if (App::getLocale() == "ar") {
                        $query->select('id', 'name', 'phonecode');
                    } else {
                        $query->select('id', 'name_en as name', 'phonecode');
                    }
                }])
//                ->with(['currency' => function ($query) {
//                    if (App::getLocale() == "ar") {
//                        $query->select('id', 'name', 'code');
//                    } else {
//                        $query->select('id', 'name_en as name', 'code');
//                    }
//                }])
                ->first();
            if ($platform == 'website') {
                auth('client')->loginUsingId($user->id);

            } else {
                $token = JWTAuth::fromUser($user);
                $user->{"token"} = $token;
            }

            return response()->json(new UsersResource($user), 200);
        }
    }


    public static function send4SMS($messageContent, $mobileNumber)
    {
        $sms_provider = Settings::find(53)->value;
        if ($sms_provider == 2) {
            return UtilsRepository::gatewaySms($mobileNumber, $messageContent);
        }
        $user = SmsDetails::USERNAME;//treatab
        $password = SmsDetails::PASSWORD;//908060
        $sendername = SmsDetails::SENDERNAME;//Treatab
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
            ));

        $opts = array('http' =>
            array(
                'method' => 'GET',
                'header' => 'Content-Type: text/html; charset=utf-8',
            )
        );

        $context = stream_context_create($opts);

        $response = (new self)->get_data('https://www.4jawaly.net/api/sendsms.php?' . $getdata, false, $context);

        return $response;

// auth call

//        $url = "http://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E&return=full";

//لارجاع القيمه json
//$url = "http://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text
//&sender=$sendername&unicode=E&return=json";
// لارجاع القيمه xml
//$url = "http://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E&return=xml";
// لارجاع القيمه string
//$url = "http://www.4jawaly.net/api/sendsms.php?username=$user&password=$password&numbers=$to&message=$text&sender=$sendername&unicode=E";
// Call API and get return message
//fopen($url,"r");

//        $ret = file_get_contents($url);
//        echo nl2br($ret);
    }

    public static function get_data($url)
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

    public static function convertNum($number)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $english = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        return str_replace($arabic, $english, $number);
    }


    public static function product_objects($request, $user, $platform = 'api')
    {

        $user_like = $user ? $user->id : 0;
        $lang = App::getLocale();
        $select_product_title = $lang == 'ar' ? 'products.title' : 'products.title_en as title';
        $select_product_description = $lang == 'ar' ? 'products.description' : 'products.description_en as description';
        $select_brand = $lang == 'ar' ? 'brands.name as brand_name' : 'brands.name_en as brand_name';
        $select_supplier_name = $lang == 'ar' ? 'user_data.user_name as supplier_name' : 'user_data.user_name_en as supplier_name';
        $latitude = $user && $user->latitude ? $user->latitude : 24.774265;
        $longitude = $user && $user->longitude ? $user->longitude : 46.738586;
        if ($user && $user->defaultAddress) {
            $latitude = $user->defaultAddress->latitude;
            $longitude = $user->defaultAddress->longitude;
        }
        $objects = Shop_product::select(
            'shop_products.id'
            , 'shop_products.product_id'
            , 'shop_products.user_id'
            , 'products.title' . ($lang == 'ar' ? '' : '_en') . ' as slug'
            , $select_product_title
            , 'products.client_price'
            , $select_product_description
            , $select_supplier_name
//            , 'products.uses'
//            , 'products.uses_en'
//            , 'products.features'
//            , 'products.features_en'
//            , 'products.benefits'
//            , 'products.benefits_en'
//            , 'products.how_to_use'
//            , 'products.how_to_use_en'
//            ,'products.weight'
//            , 'products.preparation_period'
//            , 'products.length'
//            , 'products.width'
//            , 'products.height'
            , $select_brand
            , 'shop_products.min_quantity'
            , 'shop_products.quantity'
            , 'products.category_id'
            , 'products.has_tax'
            , 'products.video_type'
            , 'products.video',
            DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                        * cos(radians(user_data.latitude))
                        * cos(radians(user_data.longitude) - radians(" . $longitude . "))
                        + sin(radians(" . $latitude . "))
                        * sin(radians(user_data.latitude))) AS distance"))
            ->with([
                'product_offer' => function ($query) use ($user_like) {
                    $query->select('shop_offer_items.shop_product_id', 'shop_offers.user_id', 'shop_offer_items.id', 'shop_offer_items.product_id', 'shop_offer_items.offer_id',
                        'shop_offers.type_id', 'shop_offers.price_discount', 'shop_offers.is_free', 'shop_offers.percentage',
                        'shop_offers.quantity', 'shop_offers.get_quantity', 'shop_offers.number_of_users', 'shop_offers.one_user_use', 'shop_offer_types.name_en')
                        ->join('shop_offers', 'shop_offers.id', 'shop_offer_items.offer_id')
                        ->join('shop_offer_types', 'shop_offer_types.id', 'shop_offers.type_id')
                        ->join('products', 'products.id', 'shop_offer_items.product_id')
                        ->where('shop_offer_items.group', 1)
                        ->where('shop_offers.status', 1)
                        ->where('shop_offer_items.deleted_at', null)
                        ->where('shop_offers.deleted_at', null)
                        ->whereDate('shop_offers.start_date', '<=', Carbon::today())
                        ->whereDate('shop_offers.end_date', '>=', Carbon::today())
                        ->withCount([
                            'product AS is_offer' => function ($query) {
                                $query->where('shop_offer_items.user_id', 'product.user_id');
                            },
                            'cart_items AS offer_usage_count' => function ($query) {
                                $query->where('status', '<>', 5)
                                    ->where('shop_offer_items.offer_id', 'cart_items.offer_id');
                            },
                            'cart_items AS user_offer_usage_count' => function ($query) use ($user_like) {
                                $query->where('cart_items.user_id', $user_like)
                                    ->where('status', '<>', 5)
                                    ->where('shop_offer_items.offer_id', 'cart_items.offer_id');
                            }
                        ]);
//                    ->selectRaw('(SELECT count(*) FROM shop_offer_items WHERE shop_offer_items.product_id=products.id AND shop_offer_items.user_id=products.user_id) as is_offer')
//                    ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.item_id = shop_offer_items.shop_product_id and cart_items.offer_id=shop_offer_items.offer_id AND status <> 5) as offer_usage_count')
//                    ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.user_id =' . $user_like . ' AND cart_items.item_id = shop_offer_items.shop_product_id and cart_items.offer_id=shop_offer_items.offer_id AND status <> 5) as user_offer_usage_count');
                }
                , 'photos' => function ($query) {
                    $query->select('id', 'product_id')
                        ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo')
                        ->selectRaw('(CONCAT ("' . url('/') . '/uploads/thumbs/", thumb)) as thumb');;
                }])
//            ->whereHas('product' ,function ($query) use ($request) {
//                if (isset($request->keyword) && !empty($request->keyword)) {
//                    $query->search($request->keyword);
//                }
//            })
            ->withCount('recommendations')
            ->join('products', 'products.id', 'shop_products.product_id')
            ->join('categories', 'products.category_id', 'categories.id')
            ->join('users', 'shop_products.user_id', 'users.id')
            ->join('user_data', 'user_data.user_id', 'users.id')
            ->join('users as supplier_user', 'products.user_id', 'supplier_user.id')
            ->join('user_data as supplier_data', 'supplier_data.user_id', 'supplier_user.id')
            ->leftJoin('shop_offer_items', 'shop_offer_items.shop_product_id', 'shop_products.id')
            ->leftJoin('shop_offers', 'shop_offers.id', 'shop_offer_items.offer_id')
            ->leftJoin('brands', 'brands.id', 'products.brand_id')
            ->Where(function ($query) use ($request, $user_like, $platform) {
                if ($request) {
                    if ($request->category_id) {
                        if ($platform == 'api') {
                            $query->where('products.category_id', $request->category_id);
                        } else {
                            $query->whereIn('products.category_id', json_decode($request->category_id));

                        }
                    }
                    if ($request->subcategory_id) {
                        $sub_id = $request->subcategory_id;
                        $query->whereIn('products.id', function ($query) use ($sub_id, $platform) {
                            $query->select('product_id')//
                            ->from(with(new ProductSubCategories())->getTable());
                            if ($platform == 'api') {
                                $query->where('sub_category_id', $sub_id);
                            } else {
                                $query->whereIn('sub_category_id', json_decode($sub_id));
                            }

                        });
                    }
                    if ($request->keyword != '') {
                        $query->whereIn('shop_products.product_id', collect(Products::search($request->keyword)->get())
                            ->pluck('id')->toArray());
                    }
//                    $keywords = explode(' ' , $request->keyword);
//                        $query->where(function ($query) use ($request) {
//                            $query->where('products.title', 'REGEXP', self::generate_pattern($request->keyword))
//                                ->orWhere('products.title_en', 'LIKE', "%" . $request->keyword . "%")
//                                ->orWhere('products.title_en', 'LIKE', "%" . str_replace(' ', '%', $request->keyword) . "%")
//                                ->orWhere('products.title', 'LIKE', "%" . str_replace(' ', '%', $request->keyword) . "%");
//                        });
//                    foreach ($keywords as $keyword) {
//                        $query->orWhere('products.title_en', 'LIKE', "%" . $keyword . "%");
//                    }
//                    }
                    if ($request->not_equal) {
                        $query->where('shop_products.id', '<>', $request->not_equal);
                    }
                    if ($request->get_favorite) {
                        $query->whereIn('shop_products.id', function ($query) use ($user_like) {
                            $query->select('item_id')//
                            ->from(with(new Favorite())->getTable())
                                ->where('type', 0)
                                ->where('user_id', $user_like);
                        });
                    }
                    if ($request->shop_id) {
                        $query->where('shop_products.user_id', $request->shop_id);
                    }
                    if ($request->brand_id) {
                        $query->where('products.brand_id', $request->brand_id);
                    }
                    if ($request->product) {
                        $query->where('shop_products.product_id', $request->product);
                    }
                    if ($request->get_offers) {
                        $query->where('shop_offer_items.group', 1)
                            ->where('shop_offers.status', 1)
                            ->where('shop_offer_items.deleted_at', null)
                            ->whereDate('shop_offers.start_date', '<=', Carbon::today())
                            ->whereDate('shop_offers.end_date', '>=', Carbon::today())
                            ->where('shop_offers.deleted_at', null);
                    }
                }
            })
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
//            ->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=shop_products.id AND type=0) as is_liked')
//            ->withCount([
//                'favourites AS is_liked' => function ($query) use ($user_like) {
//                    $query->where('type', 0)
//                        ->where('favorites.user_id', $user_like);
//                },
//            ])
//            ->withSum([
//                'cart_items as is_carted' => function ($query) use ($user_like) {
//                    $query->where('cart_items.user_id', $user_like)
//                        ->where('cart_items.shipment_id', 0)
//                        ->where('cart_items.cart_id', 0);
//                }],
//                'quantity'
//            )
//            ->selectRaw('(SELECT sum(cart_items.quantity) FROM cart_items WHERE cart_items.user_id =' . $user_like . ' AND cart_items.item_id=shop_products.id  and cart_items.shipment_id=0 and cart_items.cart_id=0) as is_carted')
            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM product_ratings WHERE product_ratings.item_id=products.id  and product_ratings.type=1 ) as product_rate')
            ->where('products.photo', '!=', '')
            ->whereNull('products.deleted_at')
            ->where('users.block', 0)
            ->where('supplier_data.stop', 0)
            ->where('categories.is_shop', 1)
            ->where('users.deleted_at', null)
            ->where('users.user_type_id', UserType::PHARMACY_PROVIDER)
            ->where('is_gift', 0)
            ->where('user_data.stop', 0)//
            ->groupBy('shop_products.id');
        return $objects;
    }

    public static function getPharmacyObjects($request, $user, $productsIds, $platform = 'api')
    {
        $user_like = $user ? $user->id : 0;
        $lang = App::getLocale();
        $select_product_title = $lang == 'ar' ? 'products.title' : 'products.title_en as title';
        $select_product_description = $lang == 'ar' ? 'products.description' : 'products.description_en as description';

        $latitude = 24.774265;
        $longitude = 46.738586;
        if ($user && $user->defaultAddress) {
            $latitude = $user->defaultAddress->latitude;
            $longitude = $user->defaultAddress->longitude;
        }
        $objects = Shop_product::
        select(
            'shop_products.id'
            , 'shop_products.product_id'
            , 'shop_products.user_id'
            , 'products.title' . ($lang === 'ar' ? '' : '_en') . ' as slug'
            , $select_product_title
            , 'products.client_price'
            , $select_product_description
//            , 'products.uses'
//            , 'products.uses_en'
//            , 'products.features'
//            , 'products.features_en'
//            , 'products.benefits'
//            , 'products.benefits_en'
//            , 'products.how_to_use'
//            , 'products.how_to_use_en'
//            ,'products.weight'
//            , 'products.preparation_period'
//            , 'products.length'
//            , 'products.width'
//            , 'products.height'
            , 'shop_products.min_quantity'
            , 'shop_products.quantity'
            , 'products.category_id'
            , 'products.has_tax'
            , 'products.video_type'
            , 'products.video',
            DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                        * cos(radians(user_data.latitude))
                        * cos(radians(user_data.longitude) - radians(" . $longitude . "))
                        + sin(radians(" . $latitude . "))
                        * sin(radians(user_data.latitude))) AS distance"))
            ->with(['product_offer' => function ($query) use ($user_like) {
                $query->select('shop_offer_items.shop_product_id', 'shop_offers.user_id', 'shop_offer_items.id', 'shop_offer_items.product_id', 'shop_offer_items.offer_id',
                    'shop_offers.type_id', 'shop_offers.price_discount', 'shop_offers.is_free', 'shop_offers.percentage',
                    'shop_offers.quantity', 'shop_offers.get_quantity', 'shop_offers.number_of_users', 'shop_offers.one_user_use', 'shop_offer_types.name_en')
                    ->selectRaw('(SELECT count(*) FROM shop_offer_items WHERE shop_offer_items.product_id=products.id AND shop_offer_items.user_id=products.user_id) as is_offer')
                    ->join('shop_offers', 'shop_offers.id', 'shop_offer_items.offer_id')
                    ->join('shop_offer_types', 'shop_offer_types.id', 'shop_offers.type_id')
                    ->join('products', 'products.id', 'shop_offer_items.product_id')
                    ->where('shop_offer_items.group', 1)
                    ->where('shop_offers.status', 1)
                    ->where('shop_offer_items.deleted_at', null)
                    ->where('shop_offers.deleted_at', null)
                    ->whereDate('shop_offers.start_date', '<=', Carbon::today())
                    ->whereDate('shop_offers.end_date', '>=', Carbon::today())
                    ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.item_id = shop_offer_items.shop_product_id and cart_items.offer_id=shop_offer_items.offer_id AND status <> 5) as offer_usage_count')
                    ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.user_id =' . $user_like . ' AND cart_items.item_id = shop_offer_items.shop_product_id and cart_items.offer_id=shop_offer_items.offer_id AND status <> 5) as user_offer_usage_count');
            }
                , 'photos' => function ($query) {
                    $query->select('id', 'product_id')
                        ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo')
                        ->selectRaw('(CONCAT ("' . url('/') . '/uploads/thumbs/", thumb)) as thumb');;
                }])
            ->withCount('recommendations')
            ->join('products', 'products.id', 'shop_products.product_id')
            ->join('categories', 'products.category_id', 'categories.id')
            ->join('users', 'shop_products.user_id', 'users.id')
            ->join('user_data', 'user_data.user_id', 'users.id')
            ->leftJoin('shop_offer_items', 'shop_offer_items.shop_product_id', 'shop_products.id')
            ->leftJoin('shop_offers', 'shop_offers.id', 'shop_offer_items.offer_id')
            ->Where(function ($query) use ($request, $user_like, $platform) {
                if ($request) {
                    if ($request->category_id) {
                        if ($platform == 'api') {
                            $query->where('products.category_id', $request->category_id);
                        } else {
                            $query->whereIn('products.category_id', json_decode($request->category_id));

                        }
                    }
                    if ($request->subcategory_id) {
                        $sub_id = $request->subcategory_id;
                        $query->whereIn('products.id', function ($query) use ($sub_id, $platform) {
                            $query->select('product_id')//
                            ->from(with(new ProductSubCategories())->getTable());
                            if ($platform == 'api') {
                                $query->where('sub_category_id', $sub_id);
                            } else {
                                $query->whereIn('sub_category_id', json_decode($sub_id));
                            }

                        });
                    }
                    if ($request->keyword != '') {
//                    $keywords = explode(' ' , $request->keyword);
                        $query->where(function ($query) use ($request) {
                            $query->where('products.title', 'REGEXP', self::generate_pattern($request->keyword))
                                ->orWhere('products.title_en', 'LIKE', "%" . $request->keyword . "%")
                                ->orWhere('products.title_en', 'LIKE', "%" . str_replace(' ', '%', $request->keyword) . "%")
                                ->orWhere('products.title', 'LIKE', "%" . str_replace(' ', '%', $request->keyword) . "%");
                        });
//                    foreach ($keywords as $keyword) {
//                        $query->orWhere('products.title_en', 'LIKE', "%" . $keyword . "%");
//                    }
                    }
                    if ($request->not_equal) {
                        $query->where('shop_products.id', '<>', $request->not_equal);
                    }
                    if ($request->get_favorite) {
                        $query->whereIn('shop_products.id', function ($query) use ($user_like) {
                            $query->select('item_id')//
                            ->from(with(new Favorite())->getTable())
                                ->where('type', 0)
                                ->where('user_id', $user_like);
                        });
                    }
                    if ($request->shop_id) {
                        $query->where('shop_products.user_id', $request->shop_id);
                    }
                    if ($request->brand_id) {
                        $query->where('products.brand_id', $request->brand_id);
                    }
                    if ($request->product) {
                        $query->where('shop_products.product_id', $request->product);
                    }
                    if ($request->get_offers) {
                        $query->where('shop_offer_items.group', 1)->where('shop_offers.status', 1)
                            ->where('shop_offer_items.deleted_at', null)
                            ->whereDate('shop_offers.start_date', '<=', Carbon::today())
                            ->whereDate('shop_offers.end_date', '>=', Carbon::today())
                            ->where('shop_offers.deleted_at', null);
                    }
                }
            })
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
            ->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=shop_products.id AND type=0) as is_liked')
            ->selectRaw('(SELECT sum(cart_items.quantity) FROM cart_items WHERE cart_items.user_id =' . $user_like . ' AND cart_items.item_id=shop_products.id  and cart_items.shipment_id=0 and cart_items.cart_id=0) as is_carted')
            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM product_ratings WHERE product_ratings.item_id=products.id  and product_ratings.type=1 ) as product_rate')
            ->where('products.photo', '<>', '')
            ->whereNull('products.deleted_at')
            ->where('users.block', 0)
            ->where('categories.is_wasfa', 1)
            ->where('users.deleted_at', null)
            ->where('users.user_type_id', UserType::PHARMACY_PROVIDER)
            ->where('is_gift', 0)
            ->whereNotIn('shop_products.id', $productsIds)
            ->where('user_data.stop', 0)//
            ->groupBy('shop_products.id');
        return $objects;
    }

    public static function generate_pattern($search_string)
    {
        $patterns = array("/(ا|إ|أ|آ)/", "/(ه|ة)/", "/(ى|ي|ئ)/");
        $replacements = array("[ا|إ|أ|آ]", "[ه|ة]", "[ى|ي|ئ]");
        return preg_replace($patterns, $replacements, $search_string);
    }

    public static function productPage($request, $user, $platform = 'api', $title = null)
    {
        $user_like = $user == null ? 0 : $user->id;
        $resp = [];
        $lang = App::getLocale();
        $select_product_title = $lang == 'ar' ? 'products.title' : 'products.title_en as title';
        $select_product_description = $lang == 'ar' ? 'products.description' : 'products.description_en as description';
        $select_brand = $lang == 'ar' ? 'brands.name as brand_name' : 'brands.name_en as brand_name';
        $select_supplier_name = $lang == 'ar' ? 'supplier_data.user_name as supplier_name' : 'supplier_data.user_name_en as supplier_name';
        $select_uses = $lang == 'ar' ? 'products.uses as uses' : 'products.uses_en as uses';
        $select_features = $lang == 'ar' ? 'products.features as features' : 'products.features_en as features';
        $select_benefits = $lang == 'ar' ? 'products.benefits as benefits' : 'products.benefits_en as benefits';
        $select_uses = $lang == 'ar' ? 'products.uses as uses' : 'products.uses_en as uses';
        $select_how_to_use = $lang == 'ar' ? 'products.how_to_use as how_to_use' : 'products.how_to_use_en as how_to_use';

        $select_meta_description = $lang == 'ar' ? 'products.meta_description as meta_description' : 'products.meta_description_en as meta_description';
        $select_meta_keywords = $lang == 'ar' ? 'products.meta_keywords as meta_keywords' : 'products.meta_keywords_en as meta_keywords';

        if ($platform == 'website' && $user != null) {
            $latitude = $user->latitude ? $user->latitude : 24.774265;
            $longitude = $user->longitude ? $user->longitude : 46.738586;
        } else {
            $latitude = $request->latitude ? $request->latitude : 24.774265;
            $longitude = $request->longitude ? $request->longitude : 46.738586;
        }
        $cobon_percentage = 0;
        $cobon_value = 0;
        $cobon_code = '';
        $shop_product = Shop_product::find($request->product_id);
        /*if cobon*/
        $cobon = MotivationCobon::whereHas('motivation', function (Builder $query) use ($shop_product) {
            $query->where('status', 1)
                ->where('user_id', $shop_product ? $shop_product->user_id : 0);
        })
            ->where(function ($q) use ($shop_product, $request) {
                if ($request->code) {
                    $q->where('code', $request->code);
                    $q->whereHas('cart_items', function (Builder $query) use ($shop_product) {
                        $query->havingRaw('COUNT(*) < motivation_cobons.usage');
                    });
                }
            })
            ->with('motivation')
            ->where('shop_product_id', $request->product_id)
            ->first();
        if ($cobon) {
            $cart_cobons = CartItem::where('status', '>', 1)->where('motivation_cobon_id', $cobon->id)->count();
            if ($cart_cobons < $cobon->usage) {
                $cobon_percentage = $cobon->percent;
                $cobon_value = $cobon->cobone_value;
                $cobon_code = $cobon->code;
            }
        }
        //end cobon
        $object = Shop_product::
        select('shop_products.id'
            , 'shop_products.product_id'
            , 'products.title' . ($lang === 'ar' ? '' : '_en') . ' as slug'
            , 'shop_products.user_id'
            , $select_product_title
            , $select_product_description
            , $select_brand
            , $select_meta_description
            , $select_meta_keywords
            , $select_supplier_name
            , 'products.client_price'
            , $select_uses
            , $select_features
            , $select_benefits
            , $select_how_to_use
            , 'products.title' . ($lang === 'ar' ? '' : '_en') . ' as slug'
            , 'products.how_to_use_en'
            , 'products.weight'
            , 'products.preparation_period'
            , 'products.length'
            , 'products.width'
            , 'products.height'
            , 'shop_products.min_quantity'
            , 'shop_products.quantity'
            , 'products.category_id'
            , 'products.has_tax'
            , 'products.video_type'
            , 'products.video'
//            ,'shop_offer_items.offer_id'
            , DB::raw("6371 * acos(cos(radians(" . $latitude . "))
                        * cos(radians(user_data.latitude))
                        * cos(radians(user_data.longitude) - radians(" . $longitude . "))
                        + sin(radians(" . $latitude . "))
                        * sin(radians(user_data.latitude))) AS distance"))
            ->with(['product_offer' => function ($query) use ($request, $user_like) {
                $query->select('shop_offer_items.shop_product_id', 'shop_offers.user_id', 'shop_offer_items.id', 'shop_offer_items.product_id', 'shop_offer_items.offer_id',
                    'shop_offers.type_id', 'shop_offers.price_discount', 'shop_offers.is_free', 'shop_offers.percentage',
                    'shop_offers.quantity', 'shop_offers.get_quantity', 'shop_offers.number_of_users', 'shop_offers.one_user_use', 'shop_offer_types.name_en')
                    ->join('shop_offers', 'shop_offers.id', 'shop_offer_items.offer_id')
                    ->join('shop_offer_types', 'shop_offer_types.id', 'shop_offers.type_id')
                    ->where('shop_offer_items.group', 1)
                    ->where('shop_offers.status', 1)
                    ->whereDate('shop_offers.start_date', '<=', Carbon::today())
                    ->whereDate('shop_offers.end_date', '>=', Carbon::today())
                    ->where('shop_offers.deleted_at', null)
                    ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.item_id = shop_offer_items.shop_product_id and cart_items.offer_id=shop_offer_items.offer_id AND status <> 5) as offer_usage_count')
                    ->selectRaw('(SELECT count(*) FROM cart_items WHERE cart_items.user_id =' . $user_like . ' AND cart_items.item_id = shop_offer_items.shop_product_id and cart_items.offer_id=shop_offer_items.offer_id AND status <> 5) as user_offer_usage_count');

            }
//                , 'offer_products_api' => function ($query) {
//                    $query->where('group', 1)->where('status', 1)
//                        ->whereDate('start_date', '<=', Carbon::today())
//                        ->whereDate('end_date', '>=', Carbon::today());
//                }
                , 'photos' => function ($query) {
                    $query->select('id', 'product_id')
                        ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo')
                        ->selectRaw('(CONCAT ("' . url('/') . '/uploads/thumbs/", thumb)) as thumb');;
                },
                'user' => function ($query) use ($lang, $user_like) {
                    $select_user_name = $lang == 'ar' ? 'user_data.user_name' : 'user_data.user_name_en as user_name';
                    $select_bio = $lang == 'ar' ? 'user_data.bio' : 'user_data.bio_en as bio';

                    $query->select($select_user_name, $select_bio, 'users.id', 'users.longitude', 'users.latitude')
                        ->join('user_data', 'user_data.user_id', 'users.id')
                        ->selectRaw('(CASE WHEN user_data.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", user_data.photo)) END) AS photo');
                    $query->selectRaw('(SELECT count(*) FROM products WHERE products.user_id =users.id and products.stop =0 ) as shop_products_count');
                    $query->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=users.id AND type=1) as is_liked');
                },
                'product_barcodes' => function ($query) {
                    $query->select('id', 'product_id', 'barcode');
                },
                'ratings' => function ($query) {
                    $query->select('product_ratings.id', 'product_ratings.item_id', 'product_ratings.user_id', 'product_ratings.rate', 'product_ratings.comment', 'users.username', 'product_ratings.created_at')
                        ->join('users', 'users.id', 'product_ratings.user_id')
                        ->selectRaw('(CASE WHEN users.photo = "" or users.photo is null  THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", users.photo)) END) AS photo')
                        ->limit(10);
                }
            ])
            ->join('products', 'products.id', 'shop_products.product_id')
            ->join('users', 'shop_products.user_id', 'users.id')
            ->join('user_data', 'user_data.user_id', 'users.id')
            ->join('users as supplier_user', 'products.user_id', 'supplier_user.id')
            ->join('user_data as supplier_data', 'supplier_data.user_id', 'supplier_user.id')
            ->leftJoin('shop_offer_items', 'products.id', 'shop_offer_items.product_id')
            ->leftJoin('shop_offers', 'shop_offers.id', 'shop_offer_items.offer_id')
            ->leftJoin('brands', 'brands.id', 'products.brand_id')
            ->leftJoin('cart_items', 'cart_items.item_id', 'shop_products.id')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
            ->selectRaw('(SELECT count(*) FROM favorites WHERE favorites.user_id =' . $user_like . ' AND favorites.item_id=shop_products.id AND type=0) as is_liked')
            ->selectRaw('(SELECT sum(cart_items.quantity) FROM cart_items WHERE cart_items.user_id =' . $user_like . ' AND cart_items.item_id=shop_products.id AND type=1 and cart_items.shipment_id=0 and cart_items.cart_id=0) as is_carted')
            ->selectRaw('(SELECT count(*) FROM shop_offer_items
             WHERE shop_offer_items.shop_product_id = shop_products.id 
             and shop_offers.status=1 and shop_offer_items.group=1 
            and shop_offer_items.user_id <> shop_products.user_id
             and shop_offers.start_date <= "' . Carbon::today() . '" and shop_offers.end_date >= "' . Carbon::today() . '"
               and shop_offers.number_of_users
               ) as offers_count')
            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM product_ratings WHERE product_ratings.item_id=products.id  and product_ratings.type=1 ) as product_rate')
            ->where('users.block', 0)
            ->where('user_data.stop', 0)
            ->where('supplier_data.stop', 0)
            ->where('shop_products.id', $request->product_id)
            ->with(['recommendations' => function ($q) {
                $q->select('*', 'user_id');
                $q->with('user:id,username,photo');
            }])->first();
        if (!$object) {
            if ($platform == 'website') {
                return redirect()->to(url('/search'));
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'error'
                ], 400);
            }
        }
        if ($object->is_carted && $cobon_percentage > 0 && $cobon) {
            $if_user_used_cobon = CartItem::where('status', '>', 1)->where('motivation_cobon_id', $cobon->id)->where('user_id', $user_like)->first();
            if (!$if_user_used_cobon) {
                if ($user_like > 0) {
                    $request->plus = true;
                    $request->recalcItem = true;
                    CartOrderRepository::addToCart($request, $user);
                }
            } else {
                if ($user_like > 0 && ($if_user_used_cobon->motivation_cobon_id != 0 || $if_user_used_cobon->motivation_cobon_id != null)) {
                    $request->plus = true;
                    $request->recalcItem = true;
                    CartOrderRepository::addToCart($request, $user);
                }
            }
        }
        $request->category_id = $object->category_id;
        $request->not_equal = $request->product_id;
//        return $object;
        $related_projects = (new self)->product_objects($request, $user, 'api')
            ->inRandomOrder()
            ->limit(8)
            ->get();
        $settings = Settings::where('option_name', 'tax_fees')->first();
        ProductsResource::using(['tax' => @$settings->value]);
        $object->{'related_projects'} = ProductsResource::collection($related_projects);
//        $object->related_projects = $related_projects;
        ProductResources::using([
            'tax' => @$settings->value,
            'cobon_percentage' => $cobon_percentage,
            'cobon_code' => $cobon_code,
            'cobon_value' => $cobon_value,
        ]);
        if ($platform == 'website') {
            if ($title && str_contains($object->title, str_replace(['-' , '@'], [' ' , '#'], $title)) === false && !isset($request->redirect)) {
                return redirect()->to(\url('/product/' . $object->id . '/' . str_replace([' ' , '#'], ['-' , '@'], $object->title)) . '?redirect=1' . (isset($request->code)? '&code='.$request->code : ''));
            }else if ($title == null){
                return redirect()->to(\url('/product/' . $object->id . '/' . str_replace([' ' , '#'], ['-' , '@'], $object->title)) . '?redirect=1'. (isset($request->code)? '&code='.$request->code : ''));
            }
            ProductResources::extra(['platform' => 'website']);
            $object = new ProductResources($object);
            if (\auth('client')->id() == 429) {
//                return $object;
            }

            $objects = json_encode($object);
//            return $object;
            $singleProduct = true;
            return view('website.single-product', compact('object', 'objects', 'singleProduct'));
        }
        $resp['product'] = new ProductResources($object);

        return response()->json($resp);

    }


    public static function update_profile($request, $user, $platform = 'api')
    {

        $validator = Validator::make($request->all(), [
//            'email' => 'required|email|unique:users,email,' . $user->id . ',id',
            'username' => 'required',
            'photo' => $request->photo ? 'mimes:jpeg,bmp,png,jpg' : '',
//            'birth_date' => 'required',
//            'phone' => 'required|unique:users,phone,' . $user->id . ',id',
//            'phonecode' => 'required',
//            'gender' => 'required',
            'password' => $request->password ? 'same:password_confirmation|min:6' : '',
            'password_confirmation' => $request->password_confirmation ? 'same:password' : '',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ], 400
            );
        }

        $user = User::find($user->id);
        $user->username = $request->input('username');
        $user->birth_date = $request->birth_date ?: '';
        $user->address = $request->address ?: '';

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
//        $user->state_id = $request->state_id;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-' . time() . '-' . uniqid() . '.webp' ; //. $file->getClientOriginalExtension();
            $destinationPath = 'uploads/';
            $upload_success = \Intervention\Image\Facades\Image::make($file->getRealPath())->resize('500', '500', function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . $fileName);

//            $destinationPath = 'uploads';
//            $request->file('photo')->move($destinationPath, $fileName);
            $user->photo = $fileName;
        }


        $user->save();


        $user = User::where('id', $user->id)
            ->select('*')
            ->with(['state' => function ($query) {
                if (App::getLocale() == "ar") {
                    $query->select('id', 'name');
                } else {
                    $query->select('id', 'name_en as name');
                }
//                $query->selectRaw('(CASE WHEN photo = "" THEN "'.url('/')."/images/placeholder.png".'" ELSE (CONCAT ("'.url('/').'/flags/", photo)) END) AS photo');
            }, 'defaultAddress' => function ($query) {
                $query->select('user_id', 'id', 'street', 'address_type', 'longitude', 'latitude')
                    ->with(['user' => function ($query) {
                        $query->select('username');
                    }]);
            }])
            ->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/public/') . '/uploads/", photo)) END) AS photo')
            ->first();


        return response()->json(new UsersResource($user), 200);

    }


    public static function home_products(Request $request)
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
        $settings = Settings::where('option_name', 'tax_fees')->first();
        ProductsResource::using(['tax' => @$settings->value]);

        $recommended_products = ProductsResource::collection((new self)->product_objects($request)->limit(5)->get());

        $request->get_offers = true;
        ProductsResource::using(['tax' => @$settings->value]);
        $latest_offers = ProductsResource::collection((new self)->product_objects($request)->limit(5)->get());

//
        return response()->json(
            [
                'status' => 200,
                'recommended_products' => $recommended_products,
                'latest_offers' => $latest_offers,
//                'recommended_products' => ProductsResource::collection($recommended_products),
            ]
        );

    }

    public static function home_page($request)
    {
//        return DB::select('SELECT * FROM categories');
        $lang = App::getLocale();
        if (auth('client')->check()) {
            $user = auth('client')->user();
        } else {
            $user = null;
        }

        $sliders = Slider::select('id',
            $lang == 'ar' ? 'title' : 'title_en as title',
            $lang == 'ar' ? 'description' : 'description_en as description',
            'sliders.has_link', 'sliders.item_type', 'sliders.item_id',
            'updated_at', 'main_slider_id')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as image')
            ->where(function ($query) {
                if (App::getLocale() == "ar") {
                    $query->whereIn('locale', [LocaleType::BOTH, LocaleType::AR]);
                } else {
                    $query->whereIn('locale', [LocaleType::BOTH, LocaleType::EN]);
                }
            })->where('main_slider_id', 1)
            ->get();
//        $categories = Categories::where('category_type', 5)
//            ->where('stop', 0)
//            ->select('id', $lang == 'ar' ? 'name' : 'name_en as name', 'photo')
//            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo')
//            ->with(['subCategories' => function ($query) use ($lang) {
//                $query->where('stop', 0);
//                $query->select('sub_categories.id', $lang == 'ar' ? 'sub_categories.name' : 'sub_categories.name_en as name', 'sub_categories.category_id');
//                $query->selectRaw('(CASE WHEN sub_categories.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", sub_categories.photo)) END) AS photo');
//            }])->orderBy('sort')
//            ->get();

//        $contacts = Settings::select('option_name', 'name', 'value')->where('input_type', 'contact_options')
//            ->orWhere('input_type', 'fees')
//            ->orWhereIn('id', [22, 38])
//            ->orWhere('input_type', 'app_links')
//            ->get();
//        $defaultAddress = null;
//        if ($user) {
//            $defaultAddress = Addresses::select('id', 'street', 'address_type', 'longitude', 'latitude')->where('user_id', $user->id)->where('is_archived', 0)->where('is_home', 1)->first();
//        }

        $offersSlider = Slider::select('sliders.id',
            $lang == 'ar' ? 'sliders.title' : 'sliders.title_en as title',
            $lang == 'ar' ? 'sliders.description' : 'sliders.description_en as description',
            'sliders.updated_at', 'sliders.has_link', 'sliders.item_type', 'sliders.item_id')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", sliders.photo)) as image')
            ->where(function ($query) {
                if (App::getLocale() == "ar") {
                    $query->whereIn('sliders.locale', [LocaleType::BOTH, LocaleType::AR]);
                } else {
                    $query->whereIn('sliders.locale', [LocaleType::BOTH, LocaleType::EN]);
                }
            })
            ->where('sliders.main_slider_id', 8)
            ->get();


//        $settings = Settings::where('option_name', 'tax_fees')->first();
//        ProductsResource::using(['tax' => @$settings->value, 'user' => $user]);
//
////        $recommended_products = (new self)->product_objects($request, $user)->limit(8)->get();
//        $recommended_products = AuthRepository::product_objects($request, $user)
//            ->whereHas('recommendations')->limit(8)->get();
//        $recommended_products = ProductsResource::collection($recommended_products);
//        $recommended_products = json_encode($recommended_products);
//        $request->get_offers = true;
//        ProductsResource::using(['tax' => @$settings->value, 'user' => $user]);
//        $latest_offers = ProductsResource::collection((new self)->product_objects($request, $user)->limit(8)->get());
//        $latest_offers = json_encode($latest_offers);
//
        $botiques_doctors = null;
        $request->home = 1;
//        $botiques_doctors = RecommendRepository::getDoctorsBotiquesRecommended($request, $user, 'website');

        $response = HomeRepository::getHomeBotiquesPharmaciesData($user ? $user->id : 0, 'website');
        $botiques = $response['botiques'];
        $pharmacies = $response['pharmacies'];

        $response = HomeRepository::getHomeClinicsDoctorsData($user ? $user->id : 0, 'website');
        $clinics = $response['clinics'];
        $doctors = $response['doctors'];

        $postsCount = SocialRepository::getPosts($request, $user, 'website', true);


        $active = 'home';

        return view('website.home', compact(
            'sliders',
            'active',
//            'categories',
//            'recommended_products',
//            'latest_offers',
//            'botiques_doctors',
            'postsCount',
            'botiques',
            'pharmacies',
            'offersSlider',
            'clinics',
            'doctors'
        ));
        return response()->json(
            [
                'status' => 200,
                'slider' => $slider,
                'categories' => $categories,
                'defaultAddress' => $defaultAddress ? [$defaultAddress] : [],
                'recommended_products' => $recommended_products,
                'latest_offers' => $latest_offers,
            ]
        );

    }


    public static function changeEmail($request, $user, $platform = 'api')
    {

        $validator = Validator::make($request->all(), [
//            'phone' => 'required|regex:/[0-9]/|min:9|unique:users,phone,'.$object->id.',id',
            'email' => 'required|email|unique:users,email,' . $user->id . ',id',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first()
                ]
                , 400);
        }

        $activation_code = self::createVerificationCode();

//            $user = User::where('email', $request->email)->where('id',$user->id)->first();
//            if (!$user) {
//                return response()->json(
//                    [
//                        'message' => 'user not found',
//                    ], 400
//                );
//
//            }
        $user->active_email = 0;
        $user->email = $request->email;
        $user->activation_code = $activation_code;
        $user->save();
        try {
            UtilsRepository::sendEmail($user->email, __('messages.activate_account'),
                view('emails.reminder', compact('activation_code'))->render());
        } catch (\Exception $ex) {

        }
        $response_message = __('messages.you_are_registered_successfully_activate_your_account_email');
        return response()->json(
            [
                'status' => 200,
                'message' => $response_message,
//                'activation_code' => $activation_code,
                'activation_code' => ''
            ], 200);

    }

    public static function changePhone($request, $user, $platform = 'api')
    {

        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/[0-9]/|min:9|unique:users,phone,' . $user->id . ',id',
//            'email' => 'required|email|unique:users,email,'.$user->id.',id' ,
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first()
                ]
                , 400);
        }

        $activation_code = self::createVerificationCode();
//            $user = User::where('phone', $request->phone)->where('id',$user->id)->first();
//            if (!$user) {
//                return response()->json(
//                    [
//                        'message' => __('messages.incorrect_activation_code'),
//                    ], 400
//                );
//            }
        $phone_value = '';
        if ($request->phone) {
            $phone = (new self)->convertNum($request->phone);
            $phone1 = (new self)->convertNum(ltrim($phone, '0'));
            $phone_value = $phone1;
            $request->merge([
                'phone' => ($phone1),
            ]);

        }
        $user->activation_code = $activation_code;
        $user->active_phone = 0;
        $user->phone = $phone_value;
        $user->save();

        $smsMessage = 'كود التفعيل في تطبيق Treatab : ' . $activation_code;
        $phone_number = '966' . ltrim($user->phone, '0');
//        $resp =$this->send4SMS('mydoctor','565656',$smsMessage,$phone_number,'My Doctor');
        $resp = (new self)->send4SMS($smsMessage, $phone_number);

        $response_message = __('messages.you_are_registered_successfully_activate_your_account_phone');
        return response()->json(
            [
                'status' => 200,
                'message' => $response_message,
//                'activation_code' => $activation_code,
                'activation_code' => ''
            ], 200);

    }


}
