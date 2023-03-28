<?php

namespace App\Http\Controllers\Providers;

use App\Models\Balance;
use App\Models\BankTransfer;
use App\Models\Countries;
use App\Models\Messages;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\Projects;
use App\Models\UserServices;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AccountsController extends Controller
{

    public function __construct()
    {

        \Carbon\Carbon::setLocale(app()->getLocale());

        $this->middleware(function ($request, $next) {
            $this->check_provider_settings(424);
            return $next($request);
        });
    }


    public function adv_user_package($user_id=0,$package_id){
        $package = Packages::find($package_id);

        $user = User::find($user_id);
        if($user) {
            $user->package_id = $package->id;
            $user->days = $package->days;
            $user->date_of_package = date('Y-m-d');
            $user->save();
        }
        return redirect()->back()->with('success','تم الاشتراك في الباقة بنجاح .');

    }

    public function cancel_package($user_id=0){

        $user = User::find($user_id);
        if($user) {
            $user->package_id = 0;
            $user->days = 0;
            $user->date_of_package = "0000-00-00";
            $user->save();
        }
        return redirect()->back()->with('success','تم الغاء الباقة بنجاح .');

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = \auth()->user()->user_type_id==3?\auth()->id():\auth()->user()->main_provider;
        $orders = Balance::where('user_id',$user_id)->get();
        return view('providers.accounts.get_details',['objects'=>$orders]);
    }

    public function new_accounts(){
        $orders = Orders::where('status',2)->where('payment',0)->groupBy('user_id')->paginate(10);
        return view('providers.accounts.new_accounts',['objects'=>$orders]);
    }

    public function previous_accounts(){
        $orders = Orders::where('status',2)->where('payment',1)->groupBy('user_id')->paginate(10);
        return view('providers.accounts.previous_accounts',['objects'=>$orders]);
    }

    public function edit_profile()
    {
        return view('providers.accounts.edit_profile',['object'=>User::find(Auth::user()->id) ]);
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
        return view('providers.accounts.normal',['objects'=>User::where('user_type_id',3)->orderBy('id','DESC')->get()]);
    }

    public function representative_users()
    {
        return view('providers.accounts.representatives',['objects'=>User::where('user_type_id',4)->orderBy('id','DESC')->get()]);
    }

    public function normal_users()
    {
        return view('providers.accounts.normal',['objects'=>User::where('user_type_id',4)->get()]);
    }

    public function seller_users()
    {
        return view('providers.accounts.adv',['objects'=>User::where('user_type_id',3)->get()]);
    }
    public function both_users()
    {
        return view('providers.accounts.normal',['objects'=>User::where('user_type_id',5)->get()]);
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

    public function approve_payment($id=0)
    {
        $orders = Orders::where('representative_id',$id)->where('status',2)->where('payment',0)->get();
        foreach ($orders as $order){
            $order ->payment = 1 ;
            $order ->save();
        }
        return redirect()->back()->with('success','تم تأكيد تحصيل المبالغ السابقة بنجاح .');
    }

    public function get_details($id=0){
        $orders = Balance::where('user_id',$id)->where('status',0)->orderBy('id','DESC')->get();
        return view('providers.accounts.get_details',['objects'=>$orders,'user'=>User::find($id)]);
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
        return view('providers.accounts.add',['countries'=>Countries::all()]);
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
        	'username' => 'required|unique:users,username',
            'phone' => 'required|regex:/[0-9]/|unique:users,phone|min:9',
            'email' => 'required|email|unique:users,email',
            'phonecode' => 'required',
            'gender' => 'required',
            'user_type_id' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
		 $object = new User;
		 $object->username= $request->username;
         $object->email = $request->email;
         $object->phone = $request->phone;
         $object->gender = $request->gender;
         $object->phonecode = $request->phonecode;
         $object->activate = 1;
         $object->user_type_id = 2 ;
         $object->privilege_id = $request->privilege_id ? $request->privilege_id : 0 ;
         $object->password = bcrypt($request->password);

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

		 $object->save();
		 return redirect()->back()->with('success','تم اضافة المشرف بنجاح .');
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
    	return view('providers.accounts.add',['object'=> User::where('id',$id)->whereIn('user_type_id',[3,4])->first()]);
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
//            'last_name' => 'required|max:60|min:3',
            'phone' => 'required|regex:/[0-9]/|min:9|unique:users,phone,'.$object->id.',id',
            'email' => 'required|email|unique:users,email,'.$object->id.',id' ,
//            'gender' => 'required',
            'user_type_id'=> 'required',
            'phonecode' => 'required',
            'first_name' => $request->user_type_id==4 ?'required'  : "" ,
            'last_name' => $request->user_type_id==4 ?'required'  : "" ,
            'brand' => $request->user_type_id==4 ?'required'  : "" ,
            'model' => $request->user_type_id==4 ?'required'  : "" ,
            'password' => $request->password ? 'same:password_confirmation|min:6' : '',
            'password_confirmation' => $request->password ? 'same:password' : '',
         ]);

        $object->username= $request->username;
        $object->email = $request->email;
        $object->phone = $request->phone;
        $object->phonecode = $request->phonecode;
        $object->first_name = $request->first_name;
        $object->last_name = $request->last_name;
        $object->brand = $request->brand;
        $object->model = $request->model;
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

        $file = $request->file('liscense');
        if ($request->hasFile('liscense')) {
            $old_file = 'uploads/'.$object->liscense;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'profile-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('liscense')->move($destinationPath, $fileName);
            $object->liscense=$fileName;
        }

        $file = $request->file('national_photo');
        if ($request->hasFile('national_photo')) {
            $old_file = 'uploads/'.$object->national_photo;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'national-photo-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('national_photo')->move($destinationPath, $fileName);
            $object->national_photo=$fileName;
        }

        $file = $request->file('front_car');
        if ($request->hasFile('front_car')) {
            $old_file = 'uploads/'.$object->front_car;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'front-car-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('front_car')->move($destinationPath, $fileName);
            $object->front_car=$fileName;
        }

        $file = $request->file('back_car');
        if ($request->hasFile('back_car')) {
            $old_file = 'uploads/'.$object->back_car;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'back-car-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('back_car')->move($destinationPath, $fileName);
            $object->back_car=$fileName;
        }

        UserServices::where('user_id',$object->id)->delete();
        foreach ($request->service as $key=>$value){
            if($value==1){
                $ser = new UserServices();
                $ser ->user_id = $object->id;
                $ser -> service_id = $key;
                $ser ->save();
            }
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
        $object = User::where('id',$id)->where('user_type_id',2)->first();
        $old_file = 'uploads/'.$object->photo;
        if(is_file($old_file))	unlink($old_file);
        $object->delete();
    }
}
