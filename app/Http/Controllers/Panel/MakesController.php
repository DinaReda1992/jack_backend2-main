<?php

namespace App\Http\Controllers\Panel;

use App\Models\CategoriesSelections;
use App\Models\Make;
use App\Models\Selections;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Categories;
class MakesController extends Controller
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
        $type=$request->type;
        $objects=Make::where(function ($query)use ($type){
            if($type=='deleted'){
                $query->where('is_archived',1);
            }
            else{
                $query->where('is_archived',0);
            }
        })->orderBy('sort','asc')->get();
        return view('admin.car_makes.all',['objects'=>$objects]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.car_makes.add');
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
		 $object = new Make();
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
        $object->stop = $request->stop?0:1;
        $object->is_special_order = $request->is_special_order?1:0;


        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->image=$fileName;
        }

         $object->save();
		 return redirect()->back()->with('success','تم اضافة القسم  بنجاح');
    }

    public function change_sort(Request $request){
        foreach ($request->position as $key=>$value){
            $privilege= Make::find($value);
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
    	return view('admin.car_makes.add',['object'=> Make::find($id)]);
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
        $object= Make::find($id);
        $this->validate($request, [
        'name' => 'required',
            'name_en' => 'required',
         ]);
		
		$object->name = $request->name;
        $object->name_en = $request->name_en;
        $object->stop = $request->stop?0:1;
        $object->is_special_order = $request->is_special_order?1:0;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/'.$object->image;
            if(is_file($old_file))	unlink($old_file);
            $fileName = 'car-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->image=$fileName;
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
        $object = Make::find($id);
        $object->is_archived=1;
        $object->save();

//        $old_file = 'uploads/'.$object->image;
//        if(is_file($old_file))	unlink($old_file);
//
//        $object ->delete();
    }

    public function stop_car_make($id){
        $object = Make::find($id);
$object->stop=$object->stop==1?0:1;
$object->save();
return 1;
    }
    public function makes_archived_restore($id)
    {
        $object = Make::find($id);
        $object->is_archived=0;
        $object->save();

//        $old_file = 'uploads/'.$object->image;
//        if(is_file($old_file))	unlink($old_file);
//
//        $object ->delete();
    }

}
