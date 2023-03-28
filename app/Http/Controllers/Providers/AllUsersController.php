<?php

namespace App\Http\Controllers\Providers;

use App\Models\BankTransfer;
use App\Models\Countries;
use App\Models\DeviceTokens;
use App\Models\Messages;
use App\Models\Notification;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\Projects;
use App\Models\UserServices;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
class AllUsersController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(316);
            return $next($request);
        });
    }


    public function adv_user_package($user_id=0,$package_id){
        $package = Packages::find($package_id);
        $user = User::find($user_id);
        if($user) {
            $user->package_id = $package->id;
            $user->user_type_id = 4;
            $user->days = $package->days;
            $user->date_of_package = date('Y-m-d');
            $user->save();
        }

        $notification55 = new Notification();
        $notification55->sender_id = 1;
        $notification55->reciever_id = $user->id;
        $notification55->type = 1;
        $notification55->message = "قامت الادارة بتمييز عضويتك " ;
        $notification55->save();

        $notification_title="تمييز عضويتك";
        $notification_message = $notification55->message;


        if(@$notification55->getReciever->notification==1){
            $this->send_fcm_notification($notification_title,$notification_message,$notification55,null,'default');
        }

        return redirect()->back()->with('success','تم الاشتراك في الباقة بنجاح .');

    }

    public function cancel_package($user_id=0){

        $user = User::find($user_id);
        if($user) {
            $user->package_id = 0;
            $user->days = 0;
            $user->date_of_package = "0000-00-00";
            $user->user_type_id =3;
            $user->save();
        }
        return redirect()->back()->with('success','تم الغاء الباقة بنجاح .');

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
//        foreach (User::where('user_type_id',3)->get() as $user ){
//            $greater_date =  date("Y-m-d",strtotime(date("Y-m-d", strtotime($user->date_of_package)) . " +".$user->days." days"));
//            if(date('Y-m-d')>=$greater_date){
//                $user->project_activate=0;
//                $user->package_id=0;
//                $user->save();
//            }
//        }

        return view('providers.users.all',['objects'=>User::whereNotIn('user_type_id',[1,2])->get()]);
    }

    public function edit_profile()
    {
        return view('providers.users.edit_profile',['object'=>User::find(Auth::user()->id) ]);
    }

    /**
     * @return string
     */
    public function edit_profile_post(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $this -> validate($request, [
            'state_id' => 'required',
            'username' => 'required|unique:users,username,'.$user->id.',id',
            'email' => 'required|email|unique:users,email,'.$user->id.',id',
            'country_id' => 'required',
            'state_id' => 'required',
            'phone' => 'required',
            'password' => 'same:password_confirmation|min:6',
            'password_confirmation' => 'same:password'
        ]);
        $user -> username = $request -> input('username');
        $user -> email = $request -> input('email');
        $user -> phone = $request -> input('phone');
        $user -> country_id = $request -> input('country_id');
        $user->gender = $request->gender ?  $request->gender : 0;
        $user -> state_id = $request -> input('state_id');
        if($request->input('password')){
            $user -> password = bcrypt($request -> input('password'));
        }

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$user->photo;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'profile-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $user->photo=$fileName;
        }

        $user -> save();

        return redirect()->back()->with('success','تم تعديل بياناتك بنجاح .');
    }



    public function clients_users()
    {
        return view('providers.users.normal',['objects'=>User::where('user_type_id',5)->orderBy('id','DESC')->get()]);
    }

    public function hall_users()
    {
        return view('providers.users.adv',['objects'=>User::where('user_type_id',3)->orderBy('id','DESC')->get()]);
    }

    public function supervisor_users()
    {
        return view('providers.users.supervisor_users',['objects'=>User::where('user_type_id',4)->orderBy('id','DESC')->get()]);
    }

    public function representative_users()
    {
        return view('providers.users.representatives',['objects'=>User::where('user_type_id',4)->orderBy('id','DESC')->get()]);
    }

    public function normal_users()
    {
        return view('providers.users.normal',['objects'=>User::where('user_type_id',4)->get()]);
    }

    public function seller_users()
    {
        return view('providers.users.adv',['objects'=>User::where('user_type_id',3)->get()]);
    }
    public function both_users()
    {
        return view('providers.users.normal',['objects'=>User::where('user_type_id',5)->get()]);
    }

    public function block_user($id=0)
    {
        $user = User::find($id);
        if(!$user){
          return redirect()->back()->with('error','لا يوجد عضو بهذا العنوان');
        }
        if($user->block==0){
          $user -> block = 1 ;
          $user -> save();
          return redirect()->back()->with('success','تم حظر المستخدم بنجاح');
        }else{
          $user -> block = 0 ;
          $user -> save();
          return redirect()->back()->with('success','تم فك الحظر عن المستخدم بنجاح .');
        }


    }

    public function active_user($id=0)
    {
        $user = User::find($id);
        if(!$user){
            return redirect()->back()->with('error','لا يوجد عضو بهذا العنوان');
        }
        if($user->activate==0){
            $user -> activate = 1 ;
            $user -> save();
            return redirect()->back()->with('success','تم تفعيل المستخدم بنجاح');
        }else{
            $user -> activate = 0 ;
            $user -> save();
            return redirect()->back()->with('success','تم الغاء التفعيل عن المستخدم بنجاح .');
        }


    }


    public function active_payment($id=0,$package_id=0)
    {
        $package = Packages::find($package_id);
        $user = User::find($id);
        if(!$user){
            return redirect()->back()->with('error','لا يوجد عضو بهذا العنوان');
        }
        if($user->project_activate==0){
            $user -> project_activate = 1 ;
            $user -> package_id = $package->id;
            $user -> days = $package->days;
            $user -> date_of_package = date('Y-m-d');
            $user -> save();
            return redirect()->back()->with('success','تم تفعيل باقة المستخدم بنجاح');
        }else{
            $user -> project_activate = 0 ;
            $user -> save();
            return redirect()->back()->with('success','تم الغاء تفعيل باقة المستخدم بنجاح .');
        }
    }
    public function adv_user($id=0)
    {
      $user = User::find($id);
      if(!$user){
        return redirect()->back()->with('error','لا يوجد عضو بهذا العنوان');
      }
      if($user->adv==0){
        $user -> adv = 1 ;
        $user -> save();
        return redirect()->back()->with('success','تم تميز المستخدمية بنجاح .');
      }else{
        $user -> adv = 0 ;
        $user -> save();
        return redirect()->back()->with('success','تم ازالة تمييز المستخدمية بنجاح .');
      }

    }

    public function change_drag_name($user_id=0,$vals="")
    {
        $user = User::find($user_id);
        if(!$user){
            return redirect()->back()->with('error','لا يوجد عضو بهذا العنوان');
        }
        $user -> drag_name = $vals ;
        $user -> save();
        return redirect()->back()->with('success','تم تغيير حالة الدراج بنجاح .');

    }

    public function supervisor($id=0){
        $user = User::find($id);
        if(!$user){
            return redirect()->back()->with('error','لا يوجد عضو بهذا العنوان');
        }
        if($user->supervisor==0){
            $user -> supervisor = 1 ;
            $user -> save();
            return redirect()->back()->with('success','تم تعيين المستخدم كمشرف المستخدم بنجاح');
        }else{
            $user -> supervisor = 0 ;
            $user -> save();
            return redirect()->back()->with('success','تم ازالة الاشراف عن المستخدم بنجاح .');
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.users.add',['countries'=>Countries::all()]);
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
            'email' => 'email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'country_id' =>'required',
            'currency_id' =>'required',
            'user_type_id'=>'required',
            'address'=>'required',
            'longitude'=>'required',
            'latitude'=>'required',
            'phonecode'=>'required',
            'photo'=> $request->photo?  'required|image':'',

        ]);
		 $object = new User;
		 $object->username= $request->username;
         $object->email = $request->email;
         $object->phone = $request->phone;
         $object->country_id = $request->country_id;
         $object->currency_id = $request->currency_id;
         $object->phonecode = $request->phonecode;
         $object->address = $request->address;
         $object->longitude = $request->longitude;
         $object->latitude = $request->latitude;
         $object->activate = 1;
         $object->user_type_id = $request->user_type_id==3?3:5 ;
         $object->password = bcrypt($request->password);

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

		 $object->save();
		 return redirect()->back()->with('success','تم اضافة المستخدم بنجاح .');
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
    	return view('providers.users.add',['object'=> User::where('id',$id)->whereIn('user_type_id',[3,4])->first()]);
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
        $object= User::where('id',$id)->whereIn('user_type_id',[3,4])->first();
        $this->validate($request, [
            'username' => 'required|unique:users,username,'.$object->id.',id',
            'phone' => 'required|regex:/[0-9]/|min:9|unique:users,phone,'.$object->id.',id',
            'email' => 'required|email|unique:users,email,'.$object->id.',id' ,
            'country_id' => 'required',
            'currency_id' => 'required',
            'phonecode' => 'required',
            'address' => 'required',
            'photo'=> $request->photo?  'required|image':'',
            'password' => $request->password ? 'same:password_confirmation|min:6' : '',
            'password_confirmation' => $request->password ? 'same:password' : '',
         ]);

        $object->username= $request->username;
        $object->email = $request->email;
        $object->phone = $request->phone;
        $object->country_id = $request->country_id;
        $object->currency_id = $request->currency_id;
        $object->phonecode = $request->phonecode;
        $object->address = $request->address;
        $object->longitude = $request->longitude;
        $object->latitude = $request->latitude;
        $object->user_type_id = $request->user_type_id==3 || $request->user_type_id==4  ?$request->user_type_id:5 ;
//        $object->gender = $request->gender;
        if($request->password) {
            $object->password = bcrypt($request->password);
        }
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'profile-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }



        $object->save();
		 return redirect()->back()->with('success','تم تعديل المستخدم بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = User::where('id',$id)->whereNotIn('user_type_id',[1,2])->first();
        $object->is_archived=1;
        $object->save();
        return 1;
//        $old_file = 'uploads/'.$object->photo;
//        if(is_file($old_file))	unlink($old_file);
//        $object->delete();
    }
    public function user_archived_restore($id)
    {
        $object = User::where('id',$id)->whereNotIn('user_type_id',[1,2])->first();
        $object->is_archived=0;
        $object->save();
        return 1;
//        $old_file = 'uploads/'.$object->photo;
//        if(is_file($old_file))	unlink($old_file);
//        $object->delete();
    }

}
