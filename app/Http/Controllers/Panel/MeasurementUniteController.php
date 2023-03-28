<?php

namespace App\Http\Controllers\Panel;

use App\Models\CategoriesSelections;
use App\Models\MeasurementUnit;
use App\Models\MeasurementUnitRestaurants;
use App\Models\Restaurants;
use App\Models\Selections;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
class MeasurementUnitController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(485);
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
        return view('admin.measurement-unites.all',['objects'=>MeasurementUnit::all()]);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.measurement-unites.add');
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
        	'name' => 'required',
            'name_en' => 'required',
        ]);
		 $object = new MeasurementUnit();
		 $object->name = $request->name;
         $object->name_en = $request->name_en;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

         $object->save();
//                MeasurementUnitRestaurants::where('category_id',$object->id)->delete();

        return redirect()->back()->with('success','تم اضافة وحدة القياس بنجاح');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('admin.measurement-unites.subs',['objects'=>MeasurementUnit::where('parent_id',$id)->orderBy('sort','asc')->get(),'category'=>Categories::find($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $object=MeasurementUnit::find($id);
        if(!$object)return abort(404);

        return view('admin.measurement-unites.add',['object'=> $object]);
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
        $object= MeasurementUnit::find($id);
        if(!$object)return abort(404);
        $this->validate($request, [
        'name' => 'required',
            'name_en' => 'required',
         ]);
		
		$object->name = $request->name;
        $object->name_en = $request->name_en;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

        $object->save();

		 return redirect()->back()->with('success','تم تعديل الوحدة  بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        $object = MeasurementUnit::find($id);
//        $object ->delete();
    }
}
