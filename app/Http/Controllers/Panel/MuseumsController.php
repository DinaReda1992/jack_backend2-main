<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Museums;
use App\Models\User;
class MuseumsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.museums.all',['objects'=>Museums::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.museums.add',['users'=> User::all()]);
    }

    public function save_order_museum($meseum_id=0,$order=0)
    {
        $car = Museums::find($meseum_id);
        $car ->orders = $order;
        $car->save();
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
        	'name' => 'required|unique:museums|max:100|min:3',
           'description' => 'required|min:3',
           'phone' => 'required|min:3',
           'longitude' => 'required',
           'user_id'=> 'required',
           'latitude' => 'required',
        	 'photo' => 'required|mimes:jpeg,bmp,png,jpg',
        ]);
		 $object = new Museums;

		 $file = $request->file('photo');
		 if ($request->hasFile('photo')) {
		 	$fileName = 'cars-category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
		 	$destinationPath = 'uploads';
		 	$request->file('photo')->move($destinationPath, $fileName);
		 	$object->photo=$fileName;
		 }

	 $object->name = $request->name;
	 $object->description = $request->description;
     $object->phone = $request->phone;
     $object->longitude = $request->longitude;
     $object->latitude = $request->latitude;
     $object->user_id = $request->user_id;
		 $object->save();
		 return redirect()->back()->with('success','تم اضافة المعرض بنجاح');
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
    	return view('admin.museums.add',['object'=> Museums::find($id) , 'users' => User::all() ]);
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
        $object= Museums::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:museums,name,'.$object->id.',id',
        'description' => 'required|min:3',
        'phone' => 'required|min:3',
        'longitude' => 'required',
        'user_id'=> 'required',
        'latitude' => 'required',
        'photo' => 'mimes:jpeg,bmp,png,jpg',
         ]);

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
        	$old_file = 'uploads/'.$object->icon;
        	if(is_file($old_file))	unlink($old_file);
        	$fileName = 'cars-category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
        	$destinationPath = 'uploads';
        	$request->file('photo')->move($destinationPath, $fileName);
        	$object->photo=$fileName;
        }
        $object->name = $request->name;
        $object->description = $request->description;
        $object->phone = $request->phone;
        $object->longitude = $request->longitude;
        $object->latitude = $request->latitude;
        $object->user_id = $request->user_id;
		 $object->save();
		 return redirect()->back()->with('success','تم تعديل المعرض بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Museums::find($id);
        $old_file = 'uploads/'.$object->photo;
        if(is_file($old_file))	unlink($old_file);
        $object->delete();
    }
}
