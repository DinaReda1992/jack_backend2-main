<?php

namespace App\Http\Controllers\Providers;

use App\Models\SelectionOptions;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Selections;
class SelectionsController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(278);
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.r
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('providers.selections.all',['objects'=>Selections::orderBy('sort','asc')->get()]);
    }

    public function selections()
    {
        return view('providers.selections.all',['objects'=>Selections::where('parent_id',0)->orderBy('sort','asc')->get()]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.selections.add');
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
//            'name_en' => 'required',
        ]);
		 $object = new Selections;
		 $object->name = $request->name;
//         $object->name_en = $request->name_en;
         $object->parent_id = $request->parent_id?:0;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

         $object->save();
		 return redirect()->back()->with('success','تم اضافة الخاصية  بنجاح');
    }

    public function change_sort(Request $request){
        foreach ($request->position as $key=>$value){
            $privilege= Selections::find($value);
            $privilege->sort = $key;
            $privilege->save();
        }
    }
    public function change_sort_options(Request $request){
        foreach ($request->position as $key=>$value){
            $privilege= SelectionOptions::find($value);
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
        return view('providers.selections.subs',['objects'=>SelectionOptions::where('selection_id',$id)->orderBy('sort','asc')->get(),'selection'=>Selections::find($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    	return view('providers.selections.add',['object'=> Selections::find($id)]);
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
        $object= Selections::find($id);
        $this->validate($request, [
        'name' => 'required',
//            'name_en' => 'required',
         ]);
		
		$object->name = $request->name;
//        $object->name_en = $request->name_en;
        $object->parent_id = $request->parent_id?:0;
         $object->save();


//        SelectionOptions::where('parent_id',$object->id)->delete();
        if($request->option_name){
            foreach ($request->option_name as $key=>$value){
                if($request->option_name[$key]) {
                    $adv = new SelectionOptions();
                    $adv->selection_id = $object->id;
                    $adv->parent_id = $request->option_parent_id[$key]?:0;
                    $adv->name = $request->option_name[$key];
//                    $adv->name_en = $request->option_name_en[$key];
                    $adv->save();
                }
            }
        }




		 return redirect()->back()->with('success','تم تعديل الخاصية  بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Selections::find($id);
        $object ->delete();
    }
}
