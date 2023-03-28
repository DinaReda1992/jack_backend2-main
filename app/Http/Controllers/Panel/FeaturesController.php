<?php

namespace App\Http\Controllers\Panel;

use App\Models\Feature;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Requests;

use App\Models\features;

class FeaturesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(313);
            return $next($request);
        });
    }

    public function index()
    {
        return view('admin.features.all',['objects'=>Feature::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.features.add');
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
        	'name' => 'required|unique:features,name|max:100|min:3',
            'name_en'=> 'required|unique:features,name_en|max:100|min:3',
            'min_price'=> 'required',
            'max_price'=> 'required',

        ]);
		 $object = new Feature;
		 $object->name_en = $request->name_en;
         $object->name= $request->name;
         $object->min_price = $request->min_price;
        $object->max_price = $request->max_price;
        $object->is_one = $request->is_one?1:0;

		 $object->save();


		 return redirect()->back()->with('success','تم اضافة الخاصية بنجاح');
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
    	return view('admin.features.add',['object'=> Feature::find($id)]);
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
        $object = Feature::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:features,name,'.$object->id.',id',
        'name_en' => 'required|max:100|min:3|unique:features,name_en,'.$object->id.',id',
        'phonecode'=> 'required',
        ]);

        $object->name = $request->name;
        $object->name_en = $request->name_en;
        $object->phonecode = $request->phonecode;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'flags/'.$object->icon;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'flag-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

		 $object->save();
		 return redirect()->back()->with('success','تم تعديل الدولة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Feature::find($id);
        $object->delete();
    }
}
