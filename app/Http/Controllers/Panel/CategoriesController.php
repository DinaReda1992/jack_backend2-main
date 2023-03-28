<?php

namespace App\Http\Controllers\Panel;

use App\Models\CategoriesSelections;
use App\Models\MainCategories;
use App\Models\MeasurementUnit;
use App\Models\MeasurementUnitsCategories;
use App\Models\Selections;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Categories;
class CategoriesController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
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
        $objects=Categories::where('is_archived',0)->where(function ($query)use($request){
            if($request->parent_id){
                $query->where('parent_id',$request->parent_id);
            }
        })->orderBy('sort','asc')
            ->get();
        $mainCategory=null;
        if($request->parent_id){
            $mainCategory=MainCategories::find($request->parent_id);
        }
        return view('admin.categories.all',['objects'=>$objects,'main_category'=>$mainCategory]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $measurementUnits=MeasurementUnit::all();

        return view('admin.categories.add',['measurementUnits'=>$measurementUnits]);
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
		 $object = new Categories;
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
         $object->parent_id = $request->parent_id?:0;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
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

        return redirect()->back()->with('success','تم اضافة القسم  بنجاح');
    }

    public function change_sort(Request $request){
        foreach ($request->position as $key=>$value){
            $privilege= Categories::find($value);
            $privilege->sort = $key;
            $privilege->save();
        }
    }
    public function change_sort_categories_selections(Request $request){
            foreach ($request->position as $key=>$value){
                $privilege= CategoriesSelections::find($value);
                $privilege->sort = $key;
                $privilege->save();
            }
        }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('admin.categories.subs',['objects'=>Categories::where('parent_id',$id)->orderBy('sort','asc')->get(),'category'=>Categories::find($id)]);
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

        return view('admin.categories.add',['object'=> Categories::find($id) ,'measurementUnits'=>$measurementUnits,'sub_measurementUnits'=>$sub_measurementUnits]);

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
        $object= Categories::find($id);
        $this->validate($request, [
        'name' => 'required',
            'name_en' => 'required',
         ]);
		
		$object->name = $request->name;
        $object->name_en = $request->name_en;
        $object->parent_id = $request->parent_id?:0;

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

		 return redirect()->back()->with('success','تم تعديل القسم  بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Categories::find($id);
        $object->is_archived=1;
        $object ->save();
    }
    public function stop_category($id){
        $object=Categories::where('id',$id)->first();
        $object->stop=!$object->stop;
        $object->save();
        return 1;
    }

}
