<?php

namespace App\Http\Controllers\Providers;

use App\Models\Categories;
use App\Models\Settings;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Cobons;
use App\Models\Subcategories;
class CobonsController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings(179);
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
        return view('providers.cobons.all',['objects'=>Cobons::orderBy('id','DESC')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.cobons.add');
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
        	'code' => 'required|unique:cobons,code',
            'percent' => 'required|numeric'

        ]);
		 $object = new Cobons;
		 $object->code = $request->code;
         $object->percent = $request->percent;
         $object->days = $request->days;
        $object->usage_quota = $request->usage_quota;

         $object->save();
		 return redirect()->back()->with('success','تم اضافة الكوبون بنجاح');
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
    	return view('providers.cobons.add',['object'=> Cobons::find($id) ]);
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
        $object= Cobons::find($id);
        $this->validate($request, [
            'code' => 'required|unique:cobons,code,'.$object->id.',id',
            'percent' => 'required|numeric'
         ]);

		 $object->code = $request->code;
         $object->percent = $request->percent;
        $object->days = $request->days;
        $object->usage_quota = $request->usage_quota;

        $object->save();
		 return redirect()->back()->with('success','تم تعديل الكوبون بنجاح');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Cobons::find($id);
        $old_file = 'uploads/'.$object->photo;
        if(is_file($old_file))	unlink($old_file);
        $object->delete();
    }
}
