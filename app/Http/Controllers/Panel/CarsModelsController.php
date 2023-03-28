<?php

namespace App\Http\Controllers\Panel;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\Cars;
use App\Models\Categories;
use App\Models\CarsModels;
class CarsModelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.carsmodels.all',['objects'=>CarsModels::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.carsmodels.add',[ 'cars'=>Cars::all()]);
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
        	'car_category_id' => 'required',
            'name' => 'required',
            'name_en' => 'required',
        ]);

                $object = new CarsModels;
                $object->name = $request->name;
                $object->name_en = $request->name_en;
                $object->cars_category_id = $request->car_category_id;
                $object->save();



		 return redirect()->back()->with('success','تم اضافة الموديل بنجاح');
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
    	return view('admin.carsmodels.add',['object'=> CarsModels::find($id), 'cars'=>Cars::all()]);
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
        $object = CarsModels::find($id);
        $this->validate($request, [
            'car_category_id' => 'required',
            'name' => 'required',
            'name_en' => 'required',
        ]);

        $object->name = $request->name;
        $object->name_en = $request->name_en;
        $object->cars_category_id = $request->car_category_id;

		 $object->save();
		 return redirect()->back()->with('success','تم تعديل الموديل بنجاح');
    }

    public function save_order_type($type_id=0,$order=0)
    {
        $car = CarsModels::find($type_id);
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
        $object = CarsModels::find($id);
        $object->delete();
    }
}
