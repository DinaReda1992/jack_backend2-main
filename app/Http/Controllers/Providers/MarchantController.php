<?php

namespace App\Http\Controllers\Providers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Marchant;
use App\Models\User;
class MarchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('providers.marchant.all',['objects'=>Marchant::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.marchant.add',['users'=> User::all()]);
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
        	'name' => 'required|unique:marchant|max:100|min:3',
           'description' => 'required|min:3',
           'phone' => 'required|min:3',
           'longitude' => 'required',
           'user_id'=> 'required',
           'latitude' => 'required',
        	 'photo' => 'required|mimes:jpeg,bmp,png,jpg',
        ]);
		 $object = new Marchant;

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
		 return redirect()->back()->with('success','تم اضافة المتجر بنجاح');
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
    	return view('providers.marchant.add',['object'=> Marchant::find($id) , 'users' => User::all() ]);
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
        $object= Marchant::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:marchant,name,'.$object->id.',id',
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
		 return redirect()->back()->with('success','تم تعديل المتجر بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Marchant::find($id);
        $old_file = 'uploads/'.$object->photo;
        if(is_file($old_file))	unlink($old_file);
        $object->delete();
    }
}
