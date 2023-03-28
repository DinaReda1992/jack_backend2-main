<?php

namespace App\Http\Controllers\Panel;

use App\Models\BankTransfer;
use App\Models\Countries;
use App\Models\Messages;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\Projects;
use App\Models\SupplierCategory;
use App\Models\UsersRegions;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
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
//        foreach (User::where('user_type_id',3)->get() as $user ){
//            $greater_date =  date("Y-m-d",strtotime(date("Y-m-d", strtotime($user->date_of_package)) . " +".$user->days." days"));
//            if(date('Y-m-d')>=$greater_date){
//                $user->project_activate=0;
//                $user->package_id=0;
//                $user->save();
//            }
//        }

        return view('admin.supervisors.all',['objects'=>User::where('user_type_id',2)->where('is_archived',0)->get()]);
    }

    public function edit_profile()
    {
        return view('admin.supervisors.edit_profile',['object'=>User::find(Auth::user()->id) ]);
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



    public function adv_users()
    {
        return view('admin.supervisors.adv',['objects'=>User::where('user_type_id',2)->where('is_archived',0)->get()]);
    }



    public function normal_users()
    {
        return view('admin.supervisors.normal',['objects'=>User::where('user_type_id',4)->where('is_archived',0)->get()]);
    }

    public function seller_users()
    {
        return view('admin.supervisors.adv',['objects'=>User::where('user_type_id',3)->where('is_archived',0)->get()]);
    }
    public function both_users()
    {
        return view('admin.supervisors.normal',['objects'=>User::where('user_type_id',5)->where('is_archived',0)->get()]);
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
        return view('admin.supervisors.add',['countries'=>Countries::all()]);
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
//            'country_id' => 'required',
//            'state_id' => 'required',
            'phonecode' => 'required',
            'privilege_id'=> 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);
		 $object = new User;
		 $object->username= $request->username;
         $object->email = $request->email;
         $object->phone = $request->phone;
         $object->phonecode = $request->phonecode;
        $object->country_id = $request->country_id?:188;
        $object->state_id = $request->state_id?:'';
        $object->region_id = $request->region_id?:'';
        $object->has_regions = $request->has_regions?1:0;

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
        if ($request->categories) {
            foreach ($request->categories as $key => $value) {
                $supplierCategory = new SupplierCategory();
                $supplierCategory->user_id = $object->id;
                $supplierCategory->category_id = $value;
                $supplierCategory->save();
            }
        }

        if($request->regions &&$request->has_regions){
            foreach ($request->regions as $key=>$value){
                if($request->regions[$key]  ) {
                    $adv = new UsersRegions();
                    $adv->region_id = $request->regions[$key];
                    $adv->user_id = $object->id;
                    $adv->save();
                }
            }
        }

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
        $current_regions = UsersRegions::where('user_id', $id)->pluck('region_id')->toArray();

        return view('admin.supervisors.add',['object'=> User::where('id',$id)->where('user_type_id',2)->first(),'countries'=>Countries::all(),'current_regions'=>$current_regions]);
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
        $object= User::where('id',$id)->where('user_type_id',2)->first();
        $this->validate($request, [
            'username' => 'required|unique:users,username,'.$object->id.',id',
//            'last_name' => 'required|max:60|min:3',
            'phone' => 'required|regex:/[0-9]/|min:9|unique:users,phone,'.$object->id.',id',
            'email' => 'required|email|unique:users,email,'.$object->id.',id' ,
            'phonecode' => 'required',
            'password' => $request->password ? 'same:password_confirmation|min:6' : '',
            'password_confirmation' => $request->password ? 'same:password' : '',
         ]);

        $object->username= $request->username;
        $object->email = $request->email;
        $object->phone = $request->phone;
        $object->phonecode = $request->phonecode;
        $object->country_id = $request->country_id?:188;
        $object->state_id = $request->state_id?:'';
        $object->region_id = $request->region_id?:'';
        $object->has_regions = $request->has_regions?1:0;

        $object->privilege_id =$request->privilege_id;
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
        SupplierCategory::where('user_id',$object->id)->delete();
        if ($request->categories) {
            foreach ($request->categories as $key => $value) {
                $supplierCategory = new SupplierCategory();
                $supplierCategory->user_id = $object->id;
                $supplierCategory->category_id = $value;
                $supplierCategory->save();
            }
        }
        UsersRegions::where('user_id',$object->id)->delete();

        if($request->regions &&$request->has_regions){
            foreach ($request->regions as $key=>$value){
                if($request->regions[$key]  ) {
                    $adv = new UsersRegions();
                    $adv->region_id = $request->regions[$key];
                    $adv->user_id = $object->id;
                    $adv->save();
                }
            }
        }
        return redirect()->back()->with('success','تم تعديل المشرف بنجاح .');
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
        $object->is_archived=1;
        $object->save();

//        $old_file = 'uploads/'.$object->photo;
//        if(is_file($old_file))	unlink($old_file);
//        $object->delete();
    }
}
