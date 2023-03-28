<?php

namespace App\Http\Controllers\Panel;

use Auth;
use App\Http\Requests;
use App\Models\Settings;
use Illuminate\Http\Request;
use App\Models\PaymentSettings;
use App\Http\Controllers\Controller;


class PaymentSettingsController extends Controller
{
     public function __construct()
     {

         $this->middleware(function ($request, $next) {
             $this->check_settings((new \ReflectionClass($this))->getShortName());
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
       return view('admin.payment-settings.add');
    }


    public function store(Request $request)
    {

			 if($request->settings) {
                 foreach ($request->settings as $key => $value) {
                     $settings = PaymentSettings::find($key);
                     $settings->value = $value;
                     //echo $settings -> input_type ;
                     if ($settings->input_type == "checkbox") {

                         if ($value == "1") {
                             $settings->value = "1";
                         } else {
                             $settings->value = "0";
                         }
                     }
                     if ($settings->input_type == "switch") {
                         $settings->value=$value?1:0;

                     }

                     $settings->save();
                 }
             }
		 return redirect()->back()->with('success','تم تعديل الاعدادات بنجاح');
	}




}
