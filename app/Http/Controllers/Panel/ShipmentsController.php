<?php

namespace App\Http\Controllers\Panel;

use App\Models\CategoriesSelections;
use App\Models\Selections;
use App\Models\Shipment;
use App\Models\SmsaSetting;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Categories;
class ShipmentsController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(473);
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
        $objects = Shipment::all();
        return view('admin.shipments.all', ['objects' => $objects]);
    }

    public function stop_shipment($id)
    {
        $object = Shipment::find($id);
        $object->status = $object->status == 1 ? 0 : 1;
        $object->save();
        return 1;
    }

    // smsa page
    public function smsa_page(Request $request){
        $object= SmsaSetting::first();

        return view('admin.shipments.smsa',['object'=>$object]);

}




    public function smsa_update(Request $request)
    {
        $object= SmsaSetting::first();
        $object->passkey = $request->passkey;
        $object->name = $request->name;
//        $object->status = $request->status?1:0;

         $object->save();


		 return redirect()->back()->with('success','تم تعديل الاعدادات بنجاح');
    }


}
