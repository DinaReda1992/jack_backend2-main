<?php

namespace App\Http\Controllers\Providers;

use App\Models\Categories;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Cars;
use App\Models\Subcategories;
class CarsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('providers.cars.all',['objects'=>Cars::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.cars.add');
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
        	'name' => 'required|max:100|min:3',
            'name_en' => 'required|max:100|min:3',
//            'photo' => 'mimes:jpeg,bmp,png,jpg,JPEG,BMP,PNG,JPG,ANI,FAX,GIF,IMG,JBG,JPE,MAC,PBM,PBM,TIFF,PCT,RAS',
        ]);
		 $object = new Cars;
		 
//		 $file = $request->file('photo');
//		 if ($request->hasFile('photo')) {
//		 	$fileName = 'bikes-category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
//		 	$destinationPath = 'uploads';
//		 	$request->file('photo')->move($destinationPath, $fileName);
//		 	$object->photo=$fileName;
//		 }
		 
		 
		 $object->name = $request->name;
        $object->name_en = $request->name_en ;


        $object->save();
		 return redirect()->back()->with('success','تم اضافة الماركة بنجاح');
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
    	return view('providers.cars.add',['object'=> Cars::find($id) ]);
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
        $object= Cars::find($id);
        $this->validate($request, [
            'name' => 'required|max:100|min:3',
            'name_en' => 'required|max:100|min:3',

//            'photo' => 'mimes:jpeg,bmp,png,jpg,JPEG,BMP,PNG,JPG,ANI,FAX,GIF,IMG,JBG,JPE,MAC,PBM,PBM,TIFF,PCT,RAS',
         ]);

        
//        $file = $request->file('photo');
//        if ($request->hasFile('photo')) {
//        	$old_file = 'uploads/'.$object->icon;
//        	if(is_file($old_file))	unlink($old_file);
//        	$fileName = 'bikes-category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
//        	$destinationPath = 'uploads';
//        	$request->file('photo')->move($destinationPath, $fileName);
//        	$object->photo=$fileName;
//        }
       
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
         $object->save();
		 return redirect()->back()->with('success','تم تعديل الماركة بنجاح');
    }

    public function save_order_car($car_id=0,$order=0)
    {
        $car = Cars::find($car_id);
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
        $object = Cars::find($id);
        $old_file = 'uploads/'.$object->photo;
        if(is_file($old_file))	unlink($old_file);
        $object->delete();
    }
}
