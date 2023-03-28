<?php

namespace App\Http\Controllers\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Settings;
use Auth;


class SettingsController extends Controller
{
     public function __construct()
     {

         $this->middleware(function ($request, $next) {
             $this->check_provider_settings(474);
             return $next($request);
         });
     }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $object=auth()->user()->provider;
       return view('providers.settings.add',['object'=>$object]);
    }

    public function store(Request $request)
    {
        $provider=auth()->user()->provider;
        $object = User::find($provider->id);
        $this->validate($request, [
            'username' => 'required',
//            'last_name' => 'required|max:60|min:3',
            'address'=>'required',
            'longitude'=>'required',
            'latitude'=>'required',
            'phone' => 'required|regex:/[0-9]/|min:9|unique:users,phone,'.$object->id.',id',
            'email' => 'required|email|unique:users,email,'.$object->id.',id' ,
//            'gender' => 'required',
//            'phonecode' => 'required',
            'photo'=> $request->photo?  'required|image':'',
        ]);

        $object->username= $request->username;
        $object->email = $request->email;
        $object->phone = $request->phone;
//         $object->state_id=$request->state_id;
//        $object->phonecode = $request->phonecode;
        $object->address = $request->address;
        $object->longitude = $request->longitude;
        $object->latitude = $request->latitude;
        $object->shipment_id=$request->shipment;
        $object->shipment_price=$request->shipment_price?:0;
        $object->taxes=$request->taxes?:0;

        $object->shipment_days = $request->shipment_days;
//        $object->privilege_id =$request->privilege_id;
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


        return redirect()->back()->with('success','تم تعديل بيانات المتجر بنجاح .');
    }





}
