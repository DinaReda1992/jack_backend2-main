<?php

namespace App\Http\Controllers\Panel;

use App\Models\MeasurementUnit;
use App\Models\MeasurementUnitsCategories;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Subcategories;
class SubcategoriesController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(20);
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $objects=Subcategories::where(function ($query) use ($request){
            if($request->category_id){
                $query->where('category_id',$request->category_id);
            }
        })->get();
        $category=null;
        if($request->category_id){
            $category=Categories::find($request->category_id);
        }
        return view('admin.subcategories.all',['objects'=>$objects,'category'=>$category ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $measurementUnits=MeasurementUnit::all();

        return view('admin.subcategories.add',[ 'categories'=>Categories::all(),'measurementUnits'=>$measurementUnits ]);
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
        	'name' => 'required|unique:sub_categories,name|max:100|min:3',
            'name_en' => 'required|unique:sub_categories,name_en|max:100|min:3',
        	'category_id' => 'required',
//            'price' => 'required',
        ]);
		 $object = new Subcategories;
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
		 $object->category_id = $request->category_id;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'sub-category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }
        $object->save();

        if($request->measurementUnits){
            foreach ($request->measurementUnits as $key=>$value){
                if($request->measurementUnits[$key]  ) {
                    $adv = new MeasurementUnitsCategories();
                    $adv->measurement_id = $request->measurementUnits[$key];
                    $adv->category_id = $object->id;
                    $adv->save();
                }
            }
        }
		 return redirect()->back()->with('success','تم اضافة القسم الفرعي بنجاح');
    }

    public function getSubCategories($id=0){
        $option = "";
        foreach (Categories::where('parent_id',$id)->get() as $sub){
            $option.="<option value='".$sub->id."'>".$sub->name."</option>";
        }
        echo $option;
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
        $measurementUnits=MeasurementUnit::all();

        $sub_measurementUnits = MeasurementUnitsCategories::where('category_id', $id)->pluck('measurement_id')->toArray();

        return view('admin.subcategories.add',['object'=> Subcategories::find($id), 'categories'=>Categories::all() ,'measurementUnits'=>$measurementUnits,'sub_measurementUnits'=>$sub_measurementUnits]);
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
        $object = Subcategories::find($id);
        $this->validate($request, [
        'name' => 'required|max:100|min:3|unique:sub_categories,name,'.$object->id.',id',
        'name_en' => 'required|max:100|min:3|unique:sub_categories,name_en,'.$object->id.',id',
        'category_id' => 'required' ,
//            'price' => 'required',

        ]);

		 $object->name = $request->name;
         $object->name_en = $request->name_en;
		 $object->category_id = $request->category_id;
//         $object->price = $request->price;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->photo;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'sub-category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }
         $object->save();
        MeasurementUnitsCategories::where('category_id',$object->id)->delete();
        if($request->measurementUnits){
            foreach ($request->measurementUnits as $key=>$value){
                if($request->measurementUnits[$key]  ) {
                    $adv = new MeasurementUnitsCategories();
                    $adv->measurement_id = $request->measurementUnits[$key];
                    $adv->category_id = $object->id;
                    $adv->save();
                }
            }
        }

        return redirect()->back()->with('success','تم تعديل القسم الفرعي بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Subcategories::find($id);
        $object->delete();
    }
}
