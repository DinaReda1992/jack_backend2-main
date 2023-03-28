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
class MainCategoriesController extends Controller
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
    public function index()
    {
        return view('admin.main_categories.all',['objects'=>MainCategories::orderBy('sort','asc')->get()]);
    }

    public function create()
    {

        return view('admin.main_categories.add');
    }

 

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

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
		 $object = new MainCategories();
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


        return redirect()->back()->with('success','تم اضافة القسم  بنجاح');
    }

    public function change_sort(Request $request){
        foreach ($request->position as $key=>$value){
            $privilege= MainCategories::find($value);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        return view('admin.main_categories.add',['object'=> MainCategories::find($id)]);

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
        $object= MainCategories::find($id);
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
        $object = MainCategories::find($id);
        $object ->delete();
    }
    public function stop_category($id){
        $object=MainCategories::where('id',$id)->first();
        $object->stop=!$object->stop;
        $object->save();
        return 1;
    }

}
