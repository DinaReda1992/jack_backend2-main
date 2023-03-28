<?php

namespace App\Http\Controllers\Panel;

use App\Models\Categories;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Years;
use App\Models\Subcategories;
class YearsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.years.all',['objects'=>Years::orderBy('id','DESC')->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.years.add');
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
        ]);
		 $object = new Years;
		 

		 
		 
		 $object->name = $request->name;


        $object->save();
		 return redirect()->back()->with('success','تم اضافة سنة الصنع بنجاح');
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
    	return view('admin.years.add',['object'=> Years::find($id) ]);
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
        $object= Years::find($id);
        $this->validate($request, [
            'name' => 'required|max:100|min:3',

//            'photo' => 'mimes:jpeg,bmp,png,jpg,JPEG,BMP,PNG,JPG,ANI,FAX,GIF,IMG,JBG,JPE,MAC,PBM,PBM,TIFF,PCT,RAS',
         ]);

        

		 $object->name = $request->name;
         $object->save();
		 return redirect()->back()->with('success','تم تعديل سنة الصنع بنجاح');
    }

    public function save_order_car($car_id=0,$order=0)
    {
        $car = Years::find($car_id);
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
        $object = Years::find($id);
        $old_file = 'uploads/'.$object->photo;
        if(is_file($old_file))	unlink($old_file);
        $object->delete();
    }
}
