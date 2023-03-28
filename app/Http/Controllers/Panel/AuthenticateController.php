<?php

namespace App\Http\Controllers\Panel;

use App\Models\ActivationCodes;
use App\Models\BankTransfer;
use App\Models\Likes;
use App\Models\Messages;
use App\Models\Notification;
use App\Models\Orders;
use App\Models\Prices;
use App\Models\Projects;
use App\Models\Reports;
use App\Models\Services;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Requests;
use Validator;
use JWTAuth;
use \Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthenticateController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct();
        $language = $request->headers->get('lang')  ? $request->headers->get('lang') : 'ar' ;
        App::setLocale($language);
        $this->middleware('jwt.auth', ['except' => ['authenticate','register','activate','get_phone_code']]);
        \Carbon\Carbon::setLocale(App::getLocale());
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                if ($user->block == 1) {
                    JWTAuth::invalidate(JWTAuth::getToken());
                    return response()->json([
                            'status'=> 401,
                            'message'=>'You are blocked',
                        ]
                    );

                }
            }
        }catch (TokenExpiredException $e) {
        } catch (TokenInvalidException $e) {
        } catch (JWTException $e) {
        }

    $this->test();

    }


    public function test(){
       die(Request::url()) ;
    }


    public function activate(Request $request)
    {
        $activation_code= $request->activation_code;
        $phone = $request->phone;
        $phone1 = ltrim($request->phone, '0');
        $phone2 =  "0".$request->phone;
        $phonecode = $request->phonecode;




        $user = ActivationCodes::where('activation_code', $activation_code)->whereIn('phone',[$phone,$phone1,$phone2])->where('phonecode', $phonecode)->first();
        if (!$user) {
            return response()->json(
                [
                    'status' => 400 ,
                    'message' => trans('messages.error_code') ,
                ]);
        } else {
            $this_user1=User::where('phonecode',$phonecode)->whereIn('phone',[$phone,$phone1,$phone2])->first();
            if($user->getUser || $this_user1){
                $user = $user->getUser ? $user->getUser : $this_user1;
                $this_user = User::find($user->id);
                $this_user -> username = $this_user -> username == null ?  '' : $this_user -> username;
                $this_user -> days = $this_user -> days == null ?  '' : $this_user -> days;
                $this_user -> date_of_package = $this_user -> date_of_package == null ?  '' : $this_user -> date_of_package;
                $this_user -> last_login = date('Y-m-d H:i:s');
                $this_user -> device_token = $request->device_token;
                if($request->device_type){
                    $this_user -> device_type = $request->device_type;
                }
                $this_user -> save();

                $token = JWTAuth::fromUser($this_user);
                $this_user->{"token"}=$token;
                return response()->json(
                    [
                        'status'=>200,
                        'data'=>$this_user
                    ]);


            }else{
                $user->activate=1;
                $user->save();
                return response()->json(
                    [
                        'status' => 202 ,
                        'message' => trans('messages.go_to_register_page') ,
                    ]);
            }
        }
    }


    public function check_upgrade(){
        $user =   JWTAuth::parseToken()->authenticate();
        if($user->user_type_id==2){
            return response()->json([
                    'status'=> 400,
                    'message'=>'You Are golden member'
                ]
            );
        }else{
            return response()->json([
                    'status'=> 200,
                    'message'=>'You ar normal user'
                ]
            );
        }
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                        'status'=> 401,
                        'message'=>'User not found'
                    ]
                );
            }

        } catch (TokenExpiredException $e) {

            return response()->json([
                    'status'=> 401,
                    'message'=> 'Token expired'
                ]
            );
        } catch (TokenInvalidException $e) {

            return response()->json([
                    'status'=> 401,
                    'message'=>'Token invalid',
                ]
            );

        } catch (JWTException $e) {

            return response()->json([
                'status'=> 401,
                'message'=>'Token absent'
            ]);
        }
        $token = JWTAuth::fromUser($user);

        $user = User::where('id',$user->id)
        ->with('getState')
        ->with('getCountry')
        ->first();

        $user->{"token"}=$token;




        // the token is valid and we have found the user via the sub claim
        return response()->json([ 'status'=>200,'data'=>$user]);
    }





    public function logout()
    {


        $user =   JWTAuth::parseToken()->authenticate();
        $userd= User::find($user->id);
        $userd->device_token="";
        $userd->device_type="";
        $userd->save();

        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(
            [
            'status'=>200,
            'message'=>'logged_out_successfully'
            ]);
    }



    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function testUrl()
    {
        echo "Hi there!";
    }




    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:60|min:3',
            'last_name' => 'required|max:60|min:3',
            'email' => 'email|unique:users,email',
            'phone' => 'required',
            'phonecode' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }

        if(!ActivationCodes::where('phonecode',$request->phonecode)->whereIn('phone',[$request->phone,ltrim($request->phone,0)])->where('activate',1)->first()){
            return response()->json(
                [
                    'status' => 402 ,
                    'message' => trans('messages.you_have_to_activate_your_phone_first') ,
                ]
            );
        }

            $user = new User();
            $user->first_name= $request->first_name;
            $user->last_name= $request->last_name;
            $user->email = $request->email;
            $user->password = bcrypt('000000');
            $user->phone = $request->phone;
            $user->activate = 0;
            $user->user_type_id = 3;
            $user->phonecode = $request->phonecode;
            if($request->device_type){
                $user -> device_type = $request->device_type;
            }
            $user -> save();
            $user -> username = $user -> username == null ?  '' : $user -> username;
            $user -> days = $user -> days == null ?  '' : $user -> days;
            $user -> date_of_package = $user -> date_of_package == null ?  '' : $user -> date_of_package;
            $user -> device_token = $request->device_token;
            $user -> save();
            $activation_code = ActivationCodes::where('phonecode',$user->phonecode)->whereIn('phone',[$user->phone,ltrim($user->phone,0)])->first();
            $activation_code->user_id = $user->id;
            $activation_code->save();

            $token = JWTAuth::fromUser($user);
            $user->{"token"}=$token;

            return response()->json(
                [
                    'status' => 200 ,
                    'data' => $user ,
                    'message' => trans('messages.you_are_registered_successfiully') ,
                ]);

    }

    public function sendSMS($userAccount, $passAccount, $msg, $numbers, $sender)
    {


        $getdata = http_build_query(
            $fields = array(
                "Username" => "s12-".$userAccount,
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

        $results = file_get_contents('http://api.yamamah.com/SendSMSV2?' . $getdata, false, $context);


        return $results;
    }



public function authenticate(Request $request)
    {


            $phone = $request->phone;
            $phone1 = ltrim($request->phone, '0');
            $phonecode = $request->phonecode;


            if(User::where('phonecode',$phonecode)->whereIn('phone',[$phone,$phone1])->where('block',1)->first()){
                return response()->json([
                    'status' => 400,
                    'message' => trans('messages.you_are_blocked'),

                ]);
            }elseif (User::where('phonecode',$phonecode)->whereIn('phone',[$phone,$phone1])->where('block',0)->first()) {
                $user = User::where('phonecode',$phonecode)->whereIn('phone',[$phone,$phone1])->where('block',0)->first();
                $user -> activate = 0 ;
                $user -> save();

                $activation = ActivationCodes::where('user_id',$user->id)->first();
                $activation->activation_code = mt_rand(100000,999999);
                $activation->save();

                $smsMessage = 'عميلنا العزيز كود تفعيل الدخول في إفحص هو: ' . $activation->activation_code;
                $phone_number = $user->phonecode.ltrim($user->phone, '0');
                $send_sms_response=$this->sendSMS('Efhes','Efhes@2018',$smsMessage,$phone_number,"Efhes");
                // if no errors are encountered we can return a JWT
                return response()->json([
                    'status'=>200,
                    'message'=> trans('messages.please_activate_your_pnone'),
                    'code'=> $send_sms_response,
                    'activation_code' => $activation->activation_code,
                    'lang'=>App::getLocale()
                ]);

            }else{

                if($activation=ActivationCodes::where('phonecode',$phonecode)->whereIn('phone',[$phone1,$phone])->first()){
                    $activation->activation_code = mt_rand(100000,999999);
                    $activation->save();
                }else{
                    $activation = new ActivationCodes();
                    $activation->phonecode = $request->phonecode;
                    $activation->phone = $request->phone;
                    $activation->activation_code = mt_rand(100000,999999);
                    $activation->save();
                }



                $smsMessage = 'عميلنا العزيز كود تفعيل الدخول في إفحص هو: ' . $activation->activation_code;
                $phone_number = $activation->phonecode.ltrim($activation->phone, '0');
                $send_sms_response=$this->sendSMS('Efhes','Efhes@2018',$smsMessage,$phone_number,"Efhes");
                // if no errors are encountered we can return a JWT
                return response()->json([
                    'status'=>200,
                    'message'=> trans('messages.please_activate_your_pnone'),
                    'code'=> $send_sms_response,
                    'activation_code' => $activation->activation_code
                ]);
            }


    }


    public function get_phone_code(Request $request)
    {


        $phone = $request->phone;
        $phone1 = ltrim($request->phone, '0');
        $phonecode = $request->phonecode;

        if($activation=ActivationCodes::where('phonecode',$phonecode)->whereIn('phone',[$phone1,$phone])->first()){
            $activation->activation_code = mt_rand(100000,999999);
            $activation->save();
            echo $activation->activation_code;
        }else{
            $activation = new ActivationCodes();
            $activation->phonecode = $request->phonecode;
            $activation->phone = $request->phone;
            $activation->activation_code = mt_rand(100000,999999);
            $activation->save();
            echo $activation->activation_code;
        }



    }


    public function my_orders(Request $request){
        $user =   JWTAuth::parseToken()->authenticate();
        if($request->headers->get('lang')=="ar"){
            $orders = Orders::where('user_id',$user->id)
                ->with(['getService'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->with(['getYear'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->with(['getBrand'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->with(['getModel'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->with(['getStatus'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->with(['getCurrency'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->where('status','<=',3)->paginate(10);

            return response()->json(
                [
                    'status' => 200,
                    'data' =>$orders
                ]
            );
        }else{
            $orders = Orders::where('user_id',$user->id)
                ->with(['getService'=> function ($query) {
                    $query->select('id' ,'name_en as name' );
                }])
                ->with(['getYear'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->with(['getBrand'=> function ($query) {
                    $query->select('id' ,'name_en as name' );
                }])
                ->with(['getModel'=> function ($query) {
                    $query->select('id' ,'name_en as name' );
                }])
                ->with(['getStatus'=> function ($query) {
                    $query->select('id' ,'name_en as name' );
                }])
                ->with(['getCurrency'=> function ($query) {
                    $query->select('id' ,'name_en as name' );
                }])
                ->where('status','<=',3)->paginate(10);

            return response()->json(
                [
                    'status' => 200,
                    'data' =>$orders
                ]
            );
        }



        return response()->json(
            [
                'status' => 200 ,
                'data' => $orders ,
            ]);

    }

    public function get_notifications_count(){
        $user =   JWTAuth::parseToken()->authenticate();
        return response()->json(
            [
                'status' => 200 ,
                'data' => Notification::where('reciever_id',$user->id)->where('status',0)->orderBy('id','DESC')->count(),
                'message'=>'notification count',
            ]);

    }

    public function notifications_read(){
        $user =   JWTAuth::parseToken()->authenticate();
        $notifications = Notification::where('reciever_id',$user->id)->where('status',0)->get();
        foreach ($notifications as $notification){
            $notification->status=1;
            $notification->save();
        }
        return response()->json(
            [
                'status' => 200 ,
                'message'=>'Notification read',
            ]);
    }

    public function notifications(Request $request){
        $user =   JWTAuth::parseToken()->authenticate();
        if($request->headers->get('lang')=="ar"){


            $orders = Notification::where('reciever_id',$user->id)->select('id','message','created_at','order_id','type')->orderBy('id','DESC')->paginate(10);
            $res=[];
            foreach ($orders as $order){
                $order->{"created_time"}= Carbon::parse($order->created_at)->diffForHumans();
                $res[]=$order;
            }
            foreach ($orders as $one) {
                if (Orders::find($one->order_id)){
                    $order = Orders::where('id', $one->order_id)
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
//                    ->with('getTime')
                        ->first();
                $one->{"order"} = $order;
                $res[] = $order;
                }
            }
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$orders
                ]
            );
        }else{
            $orders = Notification::where('reciever_id',$user->id)->select('id','message_en as message','created_at','order_id','type')->orderBy('id','DESC')->paginate(10);
            $res=[];
            foreach ($orders as $order){
                $order->{"created_time"}= Carbon::parse($order->created_at)->diffForHumans();
                $res[]=$order;
            }
            foreach ($orders as $one) {
                if (Orders::find($one->order_id)) {
                    $order = Orders::where('id', $one->order_id)
                        ->with(['getService' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }])
                        ->with(['getYear' => function ($query) {
                            $query->select('id', 'name');
                        }])
                        ->with(['getBrand' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }])
                        ->with(['getModel' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }])
                        ->with(['getStatus' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }])
                        ->with(['getCurrency' => function ($query) {
                            $query->select('id', 'name_en as name');
                        }])
//                    ->with('getTime')
                        ->first();
                    $one->{"order"} = $order;
                    $res[] = $order;
                }
            }
            return response()->json(
                [
                    'status' => 200,
                    'data' =>$orders
                ]
            );
        }



        return response()->json(
            [
                'status' => 200 ,
                'data' => $orders ,
            ]);

    }


    public function my_bills(Request $request){
        $user =   JWTAuth::parseToken()->authenticate();
        if($request->headers->get('lang')=="ar"){
            $bills = BankTransfer::where('user_id',$user->id)
                ->select('id','money_transfered as money', 'created_at')
                ->selectRaw('(CASE WHEN type = "order" THEN "طلب خدمة" ELSE "طلب ترقية عضوية" END) AS name')
                ->paginate(10);

            return response()->json(
                [
                    'status' => 200,
                    'data' =>$bills
                ]
            );
        }else{
            $bills = BankTransfer::where('user_id',$user->id)
                ->select('id', 'money_transfered as money', 'created_at')
                ->selectRaw('(CASE WHEN type = "order" THEN "Order" ELSE "Membership" END) AS name')
                ->paginate(10);

            return response()->json(
                [
                    'status' => 200,
                    'data' =>$bills
                ]
            );
        }




        return response()->json(
            [
                'status' => 200 ,
                'data' => $orders ,
            ]);

    }


    public function my_reports(){
        $user =   JWTAuth::parseToken()->authenticate();
        if(App::isLocale('ar')){
            $reports = Reports::whereIn('order_id', function ($query) use ($user) {
                $query->select('id')
                    ->from(with(new Orders())->getTable())
                    ->where('user_id', $user->id)
                    ->where('status', 4);
            }) ->where('order_id', "!=", 0)
                ->with(['getService' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['getOrder.getCurrency' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->select('id', 'date_of_report', 'service_id','order_id')
                ->selectRaw('(CONCAT ("'.url('/').'/'.App::getLocale().'/single-report/", id)) as url')
                ->paginate(10);


            return response()->json(
                [
                    'status' => 200,
                    'data' =>$reports
                ]
            );
        }else{
            $reports =Reports::whereIn('order_id', function ($query) use ($user) {
                $query->select('id')
                    ->from(with(new Orders())->getTable())
                    ->where('user_id', $user->id)
                    ->where('status', 4);
            }) ->where('order_id', "!=", 0)
                ->with(['getService' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['getOrder.getCurrency' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->select('id', 'date_of_report', 'service_id','order_id')
                ->selectRaw('(CONCAT ("'.url('/').'/'.App::getLocale().'/single-report/", id)) as url')
                ->paginate(10);


            return response()->json(
                [
                    'status' => 200,
                    'data' =>$reports
                ]
            );
        }



        return response()->json(
            [
                'status' => 200 ,
                'data' => $orders ,
            ]);

    }


    public function get_messages(Request $request){
        $user =   JWTAuth::parseToken()->authenticate();
        $other_user = $request->admin_id;
        $messages = Messages::select('id','message','reciever_id','sender_id','type','status','sender_name','created_at')
            ->selectRaw('(CASE WHEN image = "" THEN image ELSE (CONCAT ("'.url('').'/uploads/", image)) END) AS image')
            ->selectRaw('(CASE WHEN sender_id = '.$user->id.' THEN true ELSE false END) AS sender')
            ->where('sender_id',$user->id)->orWhere('reciever_id',$user->id)
            ->orderBy('id','DESC')
//            ->with('getCreatedAtAttribute')
            ->paginate(10);

        $res=[];
        foreach ($messages as $message){
            $message->{"created_time"}= Carbon::parse($message->created_at)->diffForHumans();
            $res[]=$message;
        }

        return response()->json(
            [
                'status' => 200 ,
                'data' => $messages ,
            ]);

    }

    public function my_cancelled_orders(){
        $user =   JWTAuth::parseToken()->authenticate();
        if(App::isLocale('ar')){
            $orders = Orders::where('user_id',$user->id)
                ->with(['getService'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->with(['getYear'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->with(['getBrand'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->with(['getModel'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->with(['getCurrency'=> function ($query) {
                    $query->select('id' ,'name' );
                }])
                ->where('status',5)->paginate(10);

            return response()->json(
                [
                    'status' => 200,
                    'data' =>$orders
                ]
            );
        }else {
            $orders = Orders::where('user_id', $user->id)
                ->with(['getService' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['getYear' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['getBrand' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['getModel' => function ($query) {
                    $query->select('id', 'name_en as name');
                }])
                ->with(['getCurrency'=> function ($query) {
                    $query->select('id' ,'name_en as name' );
                }])
                ->where('status', 5)->paginate(10);


            return response()->json(
                [
                    'status' => 200,
                    'data' => $orders
                ]
            );
        }

    }

    public function get_price(Request $request){
        $user =   JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'service_id' => 'required',
            'state_id' => 'required',
        ]);
        if ($validator->fails()) {



            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }else{

            $price = Prices::where('state_id',$request->state_id)
                ->where('service_id',$request->service_id)
                ->with('getCurrency')
                ->first();
                if($price) {
                    return response()->json(
                        [
                            'status' => 200,
                            'data' => $price,
                        ]);
                }else{

                    return response()->json(
                        [
                            'status' => 400,
                            'message' => trans('messages.price_doesnot_determined_yet' ),
                        ]);

                }

        }


    }


    public function add_project(Request $request)
    {
        $user =   JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
        ]);

        $this_user=User::find($user->id);

        if($this_user->project_activate == 1){
            return response()->json(
                [
                    'status' => 400 ,
                    'message' => trans('messages.you_have_to_join_project_owner_package') ,
                ]
            );
        }

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }


        $order = new Projects();
        $order->title= $request->title;
        $order->description= $request->description;
        $order->category_id = $request->category_id;
        $order->sub_category_id = $request->sub_category_id;
        $order->user_id = $user->id;
        $order->country_id = $request->country_id;
        $order->state_id = $request->state_id;
        $order -> save();

        return response()->json(
            [
                'status' => 200 ,
                'message' => trans('messages.project_added_successfully') ,
                'data'=> $order
            ]);
    }


    public function send_message(Request $request)
    {

        $user =   JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'message' => $request->type == 0 ? 'required' : '' ,
            'image' => $request->type == 1 ? 'required': '',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }


        $message = new Messages();
        $message ->sender_id= $user->id;
        $message ->reciever_id= 0;
        $message ->message = $request->message ? $request->message : "" ;
        $message ->type = $request->type;
        $message -> save();



		 $file = $request->file('image');
		 if ($request->hasFile('image')) {
		 	 $fileName = 'message-photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
		 	 $destinationPath = 'uploads';
		 	 $request->file('image')->move($destinationPath, $fileName);
             $message->image=$fileName;
		 }
        $message->save();

        $message = Messages::where('id',$message->id)->select('id','message','type','created_at')
            ->selectRaw('(CASE WHEN image = "" THEN image ELSE (CONCAT ("'.url('').'/uploads/", image)) END) AS image')
            ->first();
        $message->{'created_time'}=$message->created_at->diffForHumans();

         return response()->json(
            [
                'status' => 200 ,
                'message' => trans('messages.your_message_sent_successfully') ,
                'data'=> $message
            ]);
    }


    public function cancel_order(Request $request)
    {
        $user =   JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }


        $order = Orders::where('id',$request->order_id)->where('user_id',$user->id)->first();
        $order->status=5;
        $order->save();
        if($order) {

            $order->state_id = $request->state_id;
            $order->save();
            return response()->json(
                [
                    'status' => 200 ,
                    'message' => trans('messages.your_order_cancelled_successfully') ,
                    'data'=> $order
                ]);


        }else{
            return response()->json(
                [
                    'status' => 400 ,
                    'message' => "this is not your order" ,
                ]);

        }






    }



    public function delete_notification(Request $request)
    {
        $user =   JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }


        $order = Notification::where('id',$request->notification_id)->where('reciever_id',$user->id)->first();

        if($order) {

            $order->delete();
            return response()->json(
                [
                    'status' => 200 ,
                    'message' => trans('messages.your_notification_deleted_successfully') ,
                ]);


        }else{
            return response()->json(
                [
                    'status' => 400 ,
                    'message' => "this is not your notification" ,
                ]);
        }
    }


    public function update_profile(Request $request) {
        $user =   JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:60|min:3',
            'last_name' => 'required|max:60|min:3',
            'email' => 'required|email|unique:users,email,'.$user->id.',id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }



        $user = User::find($user->id);
        $user->first_name= $request->first_name;
        $user->last_name= $request->last_name;
        $user->email = $request->email;
        $user -> save();

        $token = JWTAuth::fromUser($user);
        $user->{"token"}=$token;

        return response()->json(
            [
                'status' => 200 ,
                'data' => $user ,
                'message' => trans('messages.your_profile_updated_successfully') ,
            ]);

    }


    public function update_device_token(Request $request) {
        $user =   JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }



        $user = User::find($user->id);
        $user->device_token= $request->device_token;
        $user -> save();

//        $token = JWTAuth::fromUser($user);
//        $user->{"token"}=$token;

        return response()->json(
            [
                'status' => 200 ,
                'data' => $user ,
//                'message' => trans('messages.your_profile_updated_successfully') ,
            ]);

    }



    public function bank_transfer_order(Request $request)
    {
        $user =   JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'  ,
            'image' =>  'required',
            'money_transfered'=>'required',
            'account_name'=>'required',
            'reference_number'=>'required',
        ]);

        if ($validator->fails()) {



            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }


        $transfer = new BankTransfer();
        $transfer->user_id= $user->id;
        $transfer ->order_id= $request->order_id;
        $transfer ->type = "order";
        $transfer ->money_transfered=$request->money_transfered ? $request->money_transfered : "" ;
        $transfer ->account_name=$request->account_name ? $request->account_name : "" ;
        $transfer ->bank_name=$request->bank_name ? $request->bank_name : "" ;
        $transfer ->reference_number=$request->reference_number ? $request->reference_number : "" ;
        $file = $request->file('image');
        if ($request->hasFile('image')) {
            $fileName = 'bank-transfer-photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('image')->move($destinationPath, $fileName);
            $transfer->image=$fileName;
        }

        $transfer -> save();



        return response()->json(
            [
                'status' => 200 ,
                'message' => trans('messages.your_bank_transfer_sent_successfully') ,
                'data'=> $transfer
            ]);

    }


    public function bank_transfer_membership(Request $request)
    {
        $user =   JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'package_id' => 'required'  ,
            'image' =>  'required',
            'money_transfered'=>'required',
            'account_name'=>'required',
            'reference_number'=>'required',
        ]);

        if ($validator->fails()) {


            return response()->json(
                [
                    'status' => 400 ,
                    'errors' => $validator->errors()->all(),
                    'message' => trans('messages.some_error_happened') ,
                ]
            );
        }



        $transfer = new BankTransfer();
        $transfer->user_id= $user->id;
        $transfer ->package_id= $request->package_id;
        $transfer ->type = "membership";
        $transfer ->money_transfered=$request->money_transfered ? $request->money_transfered : "" ;
        $transfer ->account_name=$request->account_name ? $request->account_name : "" ;
        $transfer ->reference_number=$request->reference_number ? $request->reference_number : "" ;
        $transfer ->bank_name=$request->bank_name ? $request->bank_name : "" ;

        $file = $request->file('image');
        if ($request->hasFile('image')) {
            $fileName = 'bank-transfer-photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('image')->move($destinationPath, $fileName);
            $transfer->image=$fileName;
        }

        $transfer -> save();




        return response()->json(
            [
                'status' => 200 ,
                'message' => trans('messages.your_bank_transfer_sent_successfully') ,
                'data'=> $transfer
            ]);

    }










}
