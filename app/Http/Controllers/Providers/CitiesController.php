<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Cities;
use App\Models\States;
class CitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('providers.cities.all',['objects'=>Cities::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.cities.add',[ 'states'=>States::all() ]);
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
        	'name' => 'required|unique:cities|max:100|min:3',
        	'state_id' => 'required',
        ]);
		 $object = new Cities;
		 $object->name = $request->name;
		 $object->state_id = $request->state_id;
		 $object->save();
		 return redirect()->back()->with('success','تم اضافة المدينة بنجاح');
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
    	return view('providers.cities.add',['object'=> Cities::find($id), 'states'=>States::all() ]);
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
        $object = Cities::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:cities,name,'.$object->id.',id',
        'state_id' => 'required' 
        ]);
		
		 $object->name = $request->name;
		 $object->state_id = $request->state_id;
		 $object->save();
		 return redirect()->back()->with('success','تم تعديل المدينة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Cities::find($id);
        $object->delete();
    }
}
