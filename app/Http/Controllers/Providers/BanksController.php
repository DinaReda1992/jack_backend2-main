<?php

namespace App\Http\Controllers\Providers;

use App\Models\Countries;
use App\Models\Prices;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Banks;
class BanksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_provider_settings(243);
            return $next($request);
        });
    }
    public function index()
    {
        return view('providers.banks.all',['objects'=>Banks::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.banks.add',['countries'=>Countries::all()]);
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
		 $object = new Banks;
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
    	return view('providers.banks.add',['object'=> Banks::find($id),'countries'=>Countries::all()]);
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
        $object= Banks::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3',
        'name_en' => 'required|max:100|min:3',
         ]);
		
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
		 $object->save();



		 return redirect()->back()->with('success','تم تعديل البنك بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Banks::find($id);
        $object->delete();
    }
}
