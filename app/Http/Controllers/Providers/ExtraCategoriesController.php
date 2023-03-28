<?php

namespace App\Http\Controllers\Providers;

use App\Models\CategoriesSelections;
use App\Models\ExtraCategories;
use App\Models\ExtraItems;
use App\Models\Selections;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExtraCategoriesController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
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
        $this->check_provider_settings(436);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        return view('providers.extra_categories.all',['objects'=>ExtraCategories::where('parent_id',0)->where('user_id',$provider_id)->orderBy('sort','asc')->get()]);
    }

    public function selections()
    {
        return view('providers.extra_categories.all',['objects'=>ExtraCategories::where('parent_id',0)->orderBy('sort','asc')->get()]);
    }


    public function categories_selections($id=0){
        return view('providers.categories.categories_selections',['objects'=>CategoriesSelections::where('category_id',$id)->orderBy('sort','asc')->get(),'category'=>Categories::find($id)]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->check_provider_settings(439);

        return view('providers.extra_categories.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->check_provider_settings(439);
        $this->validate($request, [
        	'name' => 'required',
            'name_en' => 'required',
        ]);

		 $object = new ExtraCategories();
		 $object->name = $request->name;
         $object->name_en = $request->name_en;
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        $object->user_id=$provider_id;
         $object->parent_id = $request->parent_id?:0;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'category-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
        }

         $object->save();
        if($request->extra_name){

            foreach ($request->extra_name as $key=>$value) {
                if($value) {
                    $extraItem = new ExtraItems();
                    $extraItem->extra_category_id = $object->id;
                    $extraItem->name = @$value;
                    $extraItem->name_en = @$request->extra_name_en[$key]?:'';
                    $extraItem->limit = @$request->limit[$key]?:'';
                    $extraItem->price = @$request->price[$key]?:'';
                    $extraItem->user_id=$provider_id;
                    $extraItem->save();
                }
            }
        }

        return redirect()->back()->with('success','تم اضافة القسم  بنجاح');
    }

    public function change_sort(Request $request){
        foreach ($request->position as $key=>$value){
            $privilege= ExtraCategories::find($value);
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
        return view('providers.extra_categories.subs',['objects'=>ExtraCategories::where('parent_id',$id)->orderBy('sort','asc')->get(),'category'=>ExtraCategories::find($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->check_provider_settings(440);

        $category=ExtraCategories::find($id);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        if($category->user_id!=$provider_id){
            return abort(404);
        }
    	return view('providers.extra_categories.add',['object'=> $category]);
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
        $this->check_provider_settings(440);

        $object= ExtraCategories::find($id);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        if($object->user_id!=$provider_id){
            return abort(404);
        }

        $this->validate($request, [
        'name' => 'required',
            'name_en' => 'required',
         ]);
		
		$object->name = $request->name;
        $object->name_en = $request->name_en;
        $object->parent_id = $request->parent_id?:0;
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        $object->user_id=$provider_id;

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
        ExtraItems::where('extra_category_id',$object->id)->delete();

        if($request->extra_name){
            foreach ($request->extra_name as $key=>$value) {
                if($value) {
                    $extraItem = new ExtraItems();
                    $extraItem->extra_category_id = $object->id;
                    $extraItem->name = @$value;
                    $extraItem->name_en = @$request->extra_name_en[$key]?:'';

                    $extraItem->user_id=$provider_id;
                    $extraItem->limit = @$request->limit[$key]?:'';
                    $extraItem->price = @$request->price[$key]?:'';
                    $extraItem->save();
                }
            }
        }

//        CategoriesSelections::where('category_id',$object->id)->delete();
//        if($request->selection){
//            foreach ($request->selection as $key=>$value){
//                if($request->selection[$key]  ) {
//                    $adv = new CategoriesSelections();
//                    $adv->selection_id = $request->selection[$key];
//                    $adv->category_id = $object->id;
//                    $adv->save();
//                }
//            }
//        }
//

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
        $this->check_provider_settings(441);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        $object = ExtraCategories::find($id);
        if($object->user_id==$provider_id){
            $object ->delete();
        }

    }
    public function extraItems(){

        return view('providers.items.extra');
    }

}
