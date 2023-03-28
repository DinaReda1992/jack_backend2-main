<?php

namespace App\Http\Controllers\Panel;

use App\Models\CategoriesSelections;
use App\Models\Make;
use App\Models\MakeYear;
use App\Models\Models;
use App\Models\Selections;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Categories;
class MakeModelsController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(464);
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
        $year_id=$request->year;
        $type=$request->type;
        $year=MakeYear::where('id',$year_id)->first();
        $models=Models::where(function ($query) use($year_id,$type){
            if($year_id){
                $query->where('makeyear_id',$year_id);
            }
            if($type=='deleted'){
                $query->where('is_archived',1);
            }
            else{
                $query->where('is_archived',0);
            }
        })->paginate(50);
        return view('admin.car_models.all',['objects'=>$models,'year'=>$year]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $year=MakeYear::where('id',$request->year)->first();
        $make=$year?$year->make:null;
        return view('admin.car_models.add',['my_make'=>$make,'my_year'=>$year]);
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
            'year_id' => 'required',
            'name' => 'required',
            'name_en' => 'required',

        ]);
		$model=Models::where('makeyear_id',$request->year_id)->where('name',$request->name)->first();
		if($model){
		    return redirect()->back()->with('error','هذا الموديل موجود بالفعل مع هذا النوع ');
        }
		 $object = new Models();
		 $object->makeyear_id = $request->year_id;
         $object->name = $request->name;
        $object->name_en = $request->name_en;

         $object->save();
		 return redirect()->back()->with('success','تم اضافة الموديل بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $object=Models::find($id);
        $year=MakeYear::where('id',$object->makeyear_id)->first();
        $make=$year?$year->make:null;
        return view('admin.car_models.add',['object'=>$object,'my_make'=>$make,'my_year'=>$year]);

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
        $object= Models::find($id);
        $this->validate($request, [
            'year_id' => 'required',
            'name' => 'required',
            'name_en' => 'required',

        ]);
        $model=Models::where('makeyear_id',$request->year_id)->where('name',$request->name)->where('id','!=',$object->id)->first();
        if($model){
            return redirect()->back()->with('error','هذا الموديل موجود بالفعل مع هذا النوع ');
        }
        $object->makeyear_id = $request->year_id;
        $object->name = $request->name;
        $object->name_en = $request->name_en;
        $object->save();


		 return redirect()->back()->with('success','تم تعديل الموديل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Models::find($id);
        $object->is_archived=1;
        $object->save();

//        $object ->delete();
    }
    public function model_archived_restore($id)
    {
        $object = Models::find($id);
        $object->is_archived=0;
        $object->save();

//        $object ->delete();
    }

    public function getMakeYears($id = 0)
    {
        echo "<option value=''>اختر سنة الصنع</option>";
        foreach (MakeYear::where('make_id', $id)->where('is_archived',0)->get() as $year) {
            echo "<option value='" . $year->id . "'>" .  $year->year . "</option>";
        }
    }
    public function filter_all(Request $request){
        $keyword = $request->keyword;
        $orders = Models::where('name','like',"%$keyword%")->orWhere('name_en','LIKE',"%$keyword%")->where('is_archived',0)->orderBy('id', 'DESC')->paginate(50);
        return view('admin.car_models.all',['objects'=>$orders,'order_id'=>$keyword]);

    }

}
