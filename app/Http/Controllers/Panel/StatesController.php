<?php

namespace App\Http\Controllers\Panel;

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
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }
    public function index()
    {
        return view('admin.states.all',['objects'=>States::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.states.add',['countries'=>Countries::all()]);
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
            'country_id' => 'required',
            'region_id' => 'required',

        ]);
		 $object = new States;
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
         $object->country_id = $request->country_id;
        $object->region_id = $request->region_id;

        $object->routeCode = $request->routeCode?:"";

        $photo = $request->file('photo');

        if ($request->hasFile('photo')) {
            $fileNameFirst = 'photo-'.time().'-'.uniqid().'.'.$photo->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileNameFirst);
            $object->photo=$fileNameFirst;
        }

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
    	return view('admin.states.add',['object'=> States::find($id),'countries'=>Countries::all()]);
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
            'region_id' => 'required',

        ]);
		
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
         $object->country_id = $request->country_id;
        $object->region_id = $request->region_id;

        $object->routeCode = $request->routeCode?:"";

        $photo = $request->file('photo');

        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);
            $fileNameFirst = 'photo-'.time().'-'.uniqid().'.'.$photo->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileNameFirst);
            $object->photo=$fileNameFirst;
        }

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
