<?php

namespace App\Http\Controllers\Providers;

use App\Models\Countries;
use App\Models\Prices;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\States;
class StatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(121);
            return $next($request);
        });
    }
    public function index()
    {
        return view('providers.states.all',['objects'=>States::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.states.add',['countries'=>Countries::all()]);
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
        	'name' => 'required|unique:states,name|max:100|min:3',
            'name_en' => 'required|unique:states,name_en|max:100|min:3',
            'country_id' => 'required'
        ]);
		 $object = new States;
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
         $object->country_id = $request->country_id;

        $object->save();





        return redirect()->back()->with('success','تم اضافة المنطقة بنجاح');
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
    	return view('providers.states.add',['object'=> States::find($id),'countries'=>Countries::all()]);
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
        $object= States::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3',
        'name_en' => 'required|max:100|min:3',
        'country_id' => 'required',
         ]);
		
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
         $object->country_id = $request->country_id;
		 $object->save();



		 return redirect()->back()->with('success','تم تعديل المنطقة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = States::find($id);
        $object->delete();
    }
}
