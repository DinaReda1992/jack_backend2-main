<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Resources\AdsDetailsResources;
use App\Http\Resources\AdsResources;
use App\Http\Resources\CategoriesResources;
use App\Http\Resources\CompaniesResources;
use App\Http\Resources\HallResource;
use App\Http\Resources\HallsResource;
use App\Http\Resources\Notifications as NotificationsResource;
use App\Http\Resources\OffersCategoriesResources;
use App\Http\Resources\OrderDetailsResources;
use App\Http\Resources\ProductRatingsResource;
use App\Http\Resources\ProductResources;
use App\Http\Resources\ProductsResource;
use App\Http\Resources\RestuarantCategoriesResources;
use App\Http\Resources\RestuarantResources;
use App\Http\Resources\RestuarantsResources;
use App\Http\Resources\ShopRatingsResource;
use App\Http\Resources\StoresResources;
use App\Http\Resources\UsersResource;
use App\Models\ActivationCodes;
use App\Models\Arbpg;
use App\Models\Balance;
use App\Models\Banks;
use App\Models\Banners;
use App\Models\CardsCategories;
use App\Models\CartItem;
use App\Models\CategoriesSelections;
use App\Models\ClientTypes;
use App\Models\CreditCards;
use App\Models\Currencies;
use App\Models\DamageEstimate;
use App\Models\DeliveryTimes;
use App\Models\DeviceMake;
use App\Models\DeviceTokens;
use App\Models\Favorite;
use App\Models\Follows;
use App\Models\Hall;
use App\Models\Regions;
use App\Models\SupplierCategory;
use App\Models\Make;
use App\Models\MakeYear;
use App\Models\MandoobPayments;
use App\Models\Models;
use App\Models\NotificationTypes;
use App\Models\OffersCategories;
use App\Models\MeasurementUnitsCategories;
use App\Models\OrderShipments;
use App\Models\PricingOrder;
use App\Models\ProductMakeYear;
use App\Models\ProductRating;
use App\Models\RequestProvider;
use App\Models\RestaurantCategories;
use App\Models\Restaurants;
use App\Models\ServicesCategories;
use App\Models\Slider;
use App\Models\SocialProvider;
use App\Models\Suggestions;
use App\Models\SupplierData;
use App\Models\UserCar;
use Damas\Paytabs\paytabs;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\BankAccounts;
use App\Models\Branches;
use App\Models\ContactTypes;
use App\Models\Faqs;
use App\Models\JoinUs;
use App\Models\MembershipBenefits;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\PaymentMethods;
use App\Models\Privileges;
use App\Models\Products;
use App\Models\Sliders;
use App\Models\Stores;
use App\Models\Years;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests;
use App\Models\Answers;
use App\Models\ApprovedProjects;
use App\Models\ArticleComments;
use App\Models\ArticlePhotos;
use App\Models\ArticleReports;
use App\Models\Articles;
use App\Models\BlogCategories;
use App\Models\BlogSubcategories;
use App\Models\Countries;
use App\Models\Illustrations;
use App\Models\Notification;
use App\Models\ProjectOffers;
use App\Models\ProjectPhotos;
use App\Models\Projects;
use App\Models\Questions;
use App\Models\Rating;
use App\Models\Services;
use App\Models\ServicesPhotos;
use App\Models\Steps;
use App\Models\Styles;
use App\Models\WhyUs;
use App\Models\WorkPhotos;
use App\Models\Works;
use Illuminate\Http\Request;
use App\Models\Cities;
use App\Models\Categories;
use App\Models\Subcategories;
use App\Models\Cars;
use App\Models\Contacts;
use App\Models\CarsModels;
use App\Models\States;
use App\Models\User;
use App\Models\Ads;
use App\Models\AdsPhotos;
use App\Models\AdsOrders;
use App\Models\AdsNotify;
use App\Models\Settings;
use App\Models\Comments;
use App\Models\CommentsFollows;
use App\Models\CommentsNotify;
use App\Models\Messages;
use App\Models\Marchant;
use App\Models\Companies;
use App\Models\Museums;
use App\Models\Likes;
use App\Models\Content;
use App\Models\Reports;
use App\Models\FollowCar;
use App\Models\PayAccount;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use \Carbon\Carbon;
use Illuminate\Support\Str;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

use Mail;
use Illuminate\Support\Facades\Input;


use JWTAuth;
use telesign\sdk\messaging\MessagingClient;
use \Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use function foo\func;
use \Alhoqbani\SmsaWebService\Smsa;
use \Alhoqbani\SmsaWebService\Models\Shipment;
use \Alhoqbani\SmsaWebService\Models\Customer;
use \Alhoqbani\SmsaWebService\Models\Shipper;
use App\Http\Controllers\Controller;


class AuthProviderController extends Controller
{

    public function __construct(Request $request)
    {
        $language = $request->headers->get('Accept-Language') ? $request->headers->get('Accept-Language') : 'ar';
        App::setLocale($language);
        \Carbon\Carbon::setLocale(App::getLocale());

    }




    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ], 400
            );
        }
        $phone = $this->convertNum($request->phone);
        $phone1 = $this->convertNum(ltrim($phone, '0'));
//        return $this->convertNum($phone1);
        $phone2 = "0" . $phone;
        $phonecode = '966';
        $is_sms_active = Settings::find(24)->value;
        if (User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1, $phone2])->where('block', 1)->where('user_type_id',3)->first()) {
            return response()->json([
                'message' => trans('messages.you_are_blocked'),
            ], 400);
        } elseif (User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1, $phone2])->where('block', 0)->where('user_type_id',3)->first()) {
            $user = User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1, $phone2])->where('block', 0)->where('user_type_id',3)->first();
            $user->activate = 0;
            $user->save();
            $activation = ActivationCodes::where('user_id', $user->id)->whereIn('phone', [$phone, $phone1, $phone2])->first();
            if ($activation && $user) {
                if ($phone == "123456789" || $phone == "090909090") {
                    $activation->activation_code = 1234;
                } else {
                    $activation->activation_code = mt_rand(1000, 9999);
                }
                $activation->save();
            } else if($user && !$activation){
                $activation = new ActivationCodes();
                $activation->user_id = $user->id;
                $activation->phonecode = $request->phonecode?:966;
                $activation->phone = $phone;
                if ($phone == "123456789" || $phone == "090909090") {
                    $activation->activation_code = 1234;
                } else {
                    $activation->activation_code = mt_rand(1000, 9999);
                }
                $activation->save();
            }else{
                return response()->json([
                    'message' => __('messages.incorrect_login_data'),
                ], 400);
            }
            if ($is_sms_active == '0') {

                $smsMessage = 'كود تفعيل حسابك فى تطبيق الطريق الذهبي : ' . $activation->activation_code;
                $final_num = $this->convertNum(ltrim($user->phone, '0'));

                $phone_number = '+' . $user->phonecode . $final_num;
                $customer_id = Settings::find(25)->value;
                $api_key = Settings::find(26)->value;
                $resp = $this->send4SMS($customer_id, $api_key, $smsMessage, $phone_number, 'GoldenRoad');
            //    Log::alert($resp);
            }
            // if no errors are encountered we can return a JWT
            return response()->json([
//                'login_status' => 1,
                'message' => trans('messages.please_activate_your_phone'),
                'sms_active' => (int)$is_sms_active,
//                'code' => $send_sms_response,
                'activation_code' => $activation->activation_code,
//                'lang'=>App::getLocale()
            ], 200);


        }else{
            return response()->json([
                'status' => 202,
                'message' => trans('messages.incorrect_login_data'),
            ], 202);
        }

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

    public function activate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'activation_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ], 400
            );
        }
        $activation_code = $request->activation_code;
        $phone = $this->convertNum($request->phone);
        $phone1 = $this->convertNum(ltrim($phone, '0'));
        $phone2 = "0".+$phone1;
        $phonecode = $request->phonecode;


        $user = ActivationCodes::where('activation_code', $activation_code)->whereIn('phone', [$phone, $phone1, $phone2])->where('phonecode', $phonecode)->first();
        if (!$user) {
            return response()->json(
                [
                    'message' => trans('messages.error_code'),
                ], 400);
        } else {
             $this_user1 = User::where('phonecode', $phonecode)->whereIn('phone', [$phone, $phone1, $phone2])->where('user_type_id',3)->first();
            if ($user->getUser || $this_user1) {
                $user = $user->getUser ? $user->getUser : $this_user1;
                $this_user = User::where('id', $user->id)
                    ->where('user_type_id',3)
                    ->select('*')
                    ->selectRaw('(CASE WHEN photo = "" THEN "" ELSE (CONCAT ("' . url('/') . '/uploads/", photo)) END) AS photo')
                    ->first();

//              $this_user -> username = $this_user -> username == null ?  '' : $this_user -> username;
                $this_user->last_login = date('Y-m-d H:i:s');
                $this_user->device_token = $request->device_token;
                if ($request->device_type) {
                    $this_user->device_type = $request->device_type;
                }
                $this_user->activate=1;
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
                $this_user->save();


                return response()->json(new UsersResource($this_user), 200);


            } else {
                return response()->json(
                    [
                        'message' => trans('messages.invalid_activation_code'),
                    ], 202);
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
            ));

        $opts = array('http' =>
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

    public function getResend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ], 400
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
                ], 400);
        }


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

    public function become_provider(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'name' => 'required' ,
//            'email' => 'required',
//            'country_id' => 'required',
            'region_id' => 'required',
            'state_id' => 'required',
        ]);

        if (!$validator->passes()) {
            return response()->json([
                'status'=>201,
                'errors'=>$validator->errors()->all(),
            ],201);
        }


        $input=$request->all();
        $input['country_id']=188;
        $data=RequestProvider::create($input);
        return response()->json([
            'status'=>200,
            'message'=>'تم اضافة طلبك بنجاح',
        ],200);


    }

    public function privacy()
    {
        if (App::isLocale('ar')) {
            return response()->json(
                [
                    'data' => Content::find(11)->content
                ]
            );
        } else {
            return response()->json(
                [
                    'data' => Content::find(11)->content_en
                ]
            );
        }
    }


}
