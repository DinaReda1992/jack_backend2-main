<?php

namespace App\Http\Controllers\Panel;

use App\Models\Addresses;
use App\Models\BankTransfer;
use App\Models\Countries;
use App\Models\DeviceTokens;
use App\Models\Main_menus;
use App\Models\Menus;
use App\Models\Messages;
use App\Models\Notification;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\Projects;
use App\Models\ServicesCategories;
use App\Models\SupplierCategory;
use App\Models\UserServices;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
class DriverController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }





    public function send_fcm_notification($notification_title,$notification_message,$notification55,$object_in_push,$sound="default"){
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification_message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['data' =>[
            'notification_type'=> (int)$notification55->type,
            'notification_title'=> $notification_title ,
            'notification_message'=> $notification_message ,
            'notification_data' => $object_in_push
        ]
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        $token = @$notification55->getReciever->devices->count();
        $tokens = DeviceTokens::where('user_id',$notification55->reciever_id)->pluck('device_token')->toArray();
        $notification_ = @$notification55->getReciever->notification;
        if($token > 0 && $notification_) {
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.drivers.all',['objects'=>User::where('user_type_id',6)->where('is_archived',0)->get()]);
    }



    /**
     * @return string
     */



    public function block_user($id=0)
    {
        $user = User::whereId($id)->where('user_type_id',6)->first();
        if(!$user){
            return redirect()->back()->with('error','لا يوجد سائق بهذا العنوان');
        }
        if($user->block==0){
            $user -> block = 1 ;
            $user -> save();
            return redirect()->back()->with('success','تم حظر السائق بنجاح');
        }else{
            $user -> block = 0 ;
            $user -> save();
            return redirect()->back()->with('success','تم فك الحظر عن السائق بنجاح .');
        }


    }

    public function active_user($id=0)
    {
        $user = User::whereId($id)->where('user_type_id',6)->first();
        if(!$user){
            return redirect()->back()->with('error','لا يوجد سائق بهذا العنوان');
        }
        if($user->activate==0){
            $user -> activate = 1 ;
            $user -> save();
            return redirect()->back()->with('success','تم تفعيل السائق بنجاح');
        }else{
            $user -> activate = 0 ;
            $user -> save();
            return redirect()->back()->with('success','تم الغاء التفعيل عن السائق بنجاح .');
        }


    }







    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.drivers.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'nullable|email|unique:users,email',
            'photo'=> $request->photo?  'required|mimes:jpeg,png,jpg,gif,svg|max:4048':'',
            'phone' => 'required|unique:users,phone|digits:9',
            'username' => 'required|unique:users,username',
            // 'driver_type'=>'required',
            'licence_end_date'=>'required',
            'licence_number'=>'required',
            'licence_photo'=> $request->licence_photo?  'required|mimes:jpeg,png,jpg,gif,svg|max:4048':'',

        ]);
        $object = new User;
        $object->username= $request->username;
        $object->email = $request->email?$request->email:"";
        $object->phone = ltrim($request->phone, '0');
        $object->country_id = $request->country_id?:188;
        $object->state_id = $request->state_id?:'';
        $object->region_id = $request->region_id?:'';

        $object->currency_id = $request->currency_id?:1;
        $object->phonecode = $request->phonecode?:966;
        $object->address = $request->address?:'';
        $object->longitude = $request->longitude?:'';
        $object->latitude = $request->latitude?:'';
        $object->activate = 1;
        $object->user_type_id = 6 ;
        $object->profit_rate =$request->profit_rate?$request->profit_rate:'';
        $object->device_type =$request->device_type?$request->device_type:'';
        $object->accept_pricing =$request->accept_pricing?1:0;
        $object->accept_estimate =$request->accept_estimate?1:0;
        $object->add_product =$request->add_product?1:0;
        // $object->driver_type =$request->driver_type==1?1:0;
        // 0 توصيل
        // 1 استلام

        $object->shop_type =$request->shop_type?:0;
        $object->licence_end_date =$request->licence_end_date;
        $object->licence_number =$request->licence_number;
        $object->client_type =$request->client_type?:0;

        $object->shipment_id =1;
        $object->shipment_days =3;

//        $object->password = bcrypt($request->password);

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }
        $file = $request->file('licence_photo');
        if ($request->hasFile('licence_photo')) {
            $fileName = 'licence_photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('licence_photo')->move($destinationPath, $fileName);
            $object->licence_photo=$fileName;
        }

        $object->save();
        return redirect()->back()->with('success','تم اضافة حساب السائق بنجاح .');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        return view('admin.drivers.add',['object'=> User::where('id',$id)->whereIn('user_type_id',[6])->first()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $object= User::where('id',$id)->whereIn('user_type_id',[6])->first();
        $this->validate($request, [
            'username' => 'required|unique:users,username,'.$object->id.',id',
            'phone' => 'required|regex:/[0-9]/|min:9|unique:users,phone,'.$object->id.',id',
            'email' => 'nullable|email|unique:users,email,'.$object->id.',id' ,
            // 'country_id'=>'required',
            // 'region_id'=>'required',
            // 'state_id' => 'required',
//            'currency_id' => 'required',
            // 'driver_type' => 'required',
//            'address' => 'required',
            'photo'=> $request->photo?  'required|image':'',
            'licence_end_date'=>'required',
            'licence_number'=>'required',
            'licence_photo'=> $request->licence_photo?  'required|mimes:jpeg,png,jpg,gif,svg|max:4048':'',

        ]);

        $object->username= $request->username;
        $object->email = $request->email?$request->email:"";
        $object->phone = $request->phone;
        $object->country_id = $request->country_id?:188;
        $object->state_id = $request->state_id?:'';
        $object->region_id = $request->region_id?:'';
        $object->currency_id = $request->currency_id?:1;
        $object->phonecode = $request->phonecode?:966;
        $object->address = $request->address?:'';
        $object->longitude = $request->longitude?:'';
        $object->latitude = $request->latitude?:'';
        $object->accept_pricing =$request->accept_pricing?1:0;
        $object->accept_estimate =$request->accept_estimate?1:0;
        $object->add_product =$request->add_product?1:0;
        // $object->driver_type =$request->driver_type==1?1:0;

        $object->shop_type =$request->shop_type?:1;
        $object->client_type =$request->client_type?:1;
        $object->profit_rate =$request->profit_rate?$request->profit_rate:'';
        $object->licence_end_date =$request->licence_end_date;
        $object->licence_number =$request->licence_number;
//        $object->gender = $request->gender;
       /* if($request->password) {
            $object->password = bcrypt($request->password);
        }*/
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'profile-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }
        $file = $request->file('licence_photo');
        if ($request->hasFile('licence_photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'licence_photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('licence_photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

        $object->save();

        return redirect()->back()->with('success','تم تعديل حساب السائق بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $object = User::where('id',$id)->where('user_type_id',6)->first();
        $object->is_archived=1;
        $object->save();
//        $old_file = 'uploads/'.$object->photo;
//        if(is_file($old_file))	unlink($old_file);
//        $object->delete();
    }
    public function provider_archived_restore($id)
    {
        $object = User::where('id',$id)->where('user_type_id',6)->first();
        $object->is_archived=0;
        $object->save();
//        $old_file = 'uploads/'.$object->photo;
//        if(is_file($old_file))	unlink($old_file);
//        $object->delete();
    }



    /**/







}
