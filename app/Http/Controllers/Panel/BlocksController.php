<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Blocks;
use App\Models\Cities;
class BlocksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.blocks.all',['objects'=>Blocks::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.blocks.add',[ 'cities'=>Cities::all() ]);
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
        	'name' => 'required|unique:blocks|max:100|min:3',
        	'city_id' => 'required',
        ]);
		 $object = new Blocks;
		 $object->name = $request->name;
		 $object->city_id = $request->city_id;
		 $object->save();
		 return redirect()->back()->with('success','تم اضافة الحي بنجاح');
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
    	return view('admin.blocks.add',['object'=> Blocks::find($id), 'cities'=>Cities::all() ]);
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
        $object = Blocks::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:blocks,name,'.$object->id.',id',
        'city_id' => 'required'
        ]);

		 $object->name = $request->name;
		 $object->city_id = $request->city_id;
		 $object->save();
		 return redirect()->back()->with('success','تم تعديل الحي بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Blocks::find($id);
        $object->delete();
    }
}
