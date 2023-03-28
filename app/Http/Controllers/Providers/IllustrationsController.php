<?php

namespace App\Http\Controllers\Providers;

use App\Models\Categories;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Models\Illustrations;
use App\Models\Subcategories;
use App\Models\Privileges;
use App\Models\Groups;
class IllustrationsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->check_settings(5);
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('providers.illustrations.all',['objects'=>Illustrations::orderBy('sort','asc')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.illustrations.add');
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
            'title_en' => 'required',
            'description' => 'required',
            'description_en' => 'required',
            'photo' => 'required|image',
            ]);

		 $object = new Illustrations;
		 
		 $file = $request->file('photo');
		 if ($request->hasFile('photo')) {
		 	$fileName = 'illustrations-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
		 	$destinationPath = 'uploads';
		 	$request->file('photo')->move($destinationPath, $fileName);
		 	$object->photo=$fileName;
		 }
		 $object->title = $request->title;
		 $object->title_en = $request->title_en;
		 $object->description_en = $request->description_en;
		 $object->description = $request->description;

         $object->save();

		 return redirect()->back()->with('success','تم اضافة الشاشة بنجاح');
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
    	return view('providers.illustrations.add',['object'=> Illustrations::find($id)]);
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
        $object= Illustrations::find($id);
        $this->validate($request, [
            'title' => 'required',
            'title_en' => 'required',
            'description' => 'required',
            'description_en' => 'required',
            'photo' => $request->photo ? 'image':'',
         ]);

        
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
        	$old_file = 'uploads/'.$object->photo;
        	if(is_file($old_file))	unlink($old_file);
        	$fileName = 'illustrations-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
        	$destinationPath = 'uploads';
        	$request->file('photo')->move($destinationPath, $fileName);
        	$object->photo=$fileName;
        }
        $object->title = $request->title;
        $object->title_en = $request->title_en;
        $object->description_en = $request->description_en;
        $object->description = $request->description;

        $object->save();

		  return redirect()->back()->with('success','تم تعديل الشاشة بنجاح');
    }

    public function save_order_illustrations($illustrations_id=0,$order=0)
    {
        $car = Illustrations::find($illustrations_id);
        $car ->orders = $order;
        $car->save();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Illustrations::find($id);
        $old_file = 'uploads/'.$object->photo;
        if(is_file($old_file))	unlink($old_file);
        $object->delete();
    }

    public function change_sort(Request $request){
        foreach ($request->position as $key=>$value){
            $privilege= Illustrations::find($value);
            $privilege->sort = $key;
            $privilege->save();
        }
    }
}
