<?php

namespace App\Http\Controllers\Panel;

use App\Models\Advantages;
use Illuminate\Http\Request;
use App\Http\Requests;

class AdvantagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.advantages.all',['objects'=>Advantages::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.advantages.add');
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
        	'title' => 'required',
            'description' => 'required',
        ]);
		 $object = new Advantages();
		 $object->title = $request->title;
         $object->description = $request->description;
		 $file = $request->file('photo');
		 if ($request->hasFile('photo')) {
		 	$fileName = 'advantage-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
		 	$destinationPath = 'uploads';
		 	$request->file('photo')->move($destinationPath, $fileName);
		 	$object->photo=$fileName;
		 }
		 $object->save();
		 return redirect()->back()->with('success','تم اضافة الميزة بنجاح');
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
    	return view('admin.advantages.add',['object'=> Advantages::find($id)]);
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
        $object= Advantages::find($id);
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required|min:3',
        ]);

        $object->title = $request->title;
        $object->description = $request->description;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
        	$old_file = 'uploads/'.$object->icon;
        	if(is_file($old_file))	unlink($old_file);
        	$fileName = 'advantage-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
        	$destinationPath = 'uploads';
        	$request->file('photo')->move($destinationPath, $fileName);
        	$object->photo=$fileName;
        }

        $object->save();
		 return redirect()->back()->with('success','تم تعديل الميزة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Advantages::find($id);
        $object->delete();
    }
}
