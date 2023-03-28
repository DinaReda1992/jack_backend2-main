<?php

namespace App\Http\Controllers\Panel;

use App\Models\Countries;
use App\Models\Prices;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\CancellationTypes;
class CancellationTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(428);
            return $next($request);
        });
    }
    public function index()
    {
        return view('admin.cancellation_types.all',['objects'=>CancellationTypes::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.cancellation_types.add',['countries'=>Countries::all()]);
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
        ]);
		 $object = new CancellationTypes;
		 $object->name = $request->name;
         $object->name_en = $request->name_en;

        $object->save();





        return redirect()->back()->with('success','تم اضافة البنك بنجاح');
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
    	return view('admin.cancellation_types.add',['object'=> CancellationTypes::find($id),'countries'=>Countries::all()]);
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
        $object= CancellationTypes::find($id);
        $this->validate($request, [
            'description' => 'required',
            'percent' => 'required',
            'days' => 'required',
            'description_en' => 'required|max:100|min:3',
         ]);
		
		 $object->description = $request->description;
         $object->description_en = $request->description_en;
		 $object->percent = $request->percent;
         $object->days = $request->days;
		 $object->save();



		 return redirect()->back()->with('success','تم تعديل سياسات الإلغاء بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = CancellationTypes::find($id);
        $object->delete();
    }
}
