<?php

namespace App\Http\Controllers\Panel;

use App\Models\CategoriesSelections;
use App\Models\OffersCategories;
use App\Models\OffersCategoriesRestaurants;
use App\Models\Restaurants;
use App\Models\Selections;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
class OffersCategoriesController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings(461);
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
        return view('admin.offersCategories.all',['objects'=>OffersCategories::where('parent_id',0)->orderBy('sort','asc')->get()]);
    }

    public function selections()
    {
        return view('admin.offersCategories.all',['objects'=>OffersCategories::where('parent_id',0)->orderBy('sort','asc')->get()]);
    }


    public function categories_selections($id=0){
        return view('admin.offersCategories.categories_selections',['objects'=>CategoriesSelections::where('category_id',$id)->orderBy('sort','asc')->get(),'category'=>OffersCategories::find($id)]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $restaurants=Restaurants::where('stop',0)->get();
        return view('admin.offersCategories.add',['restaurants'=>$restaurants]);
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
		 $object = new OffersCategories();
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

        $icon = $request->file('icon');
        if ($request->hasFile('icon')) {
            $fileName = 'offer-category-'.time().'-'.uniqid().'.'.$icon->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('icon')->move($destinationPath, $fileName);
            $object->icon=$fileName;
        }
         $object->save();
//                OffersCategoriesRestaurants::where('category_id',$object->id)->delete();
        if($request->restaurants){
            foreach ($request->restaurants as $key=>$value){
                if($request->restaurants[$key]  ) {
                    $adv = new OffersCategoriesRestaurants();
                    $adv->restaurant_id = $request->restaurants[$key];
                    $adv->category_id = $object->id;
                    $adv->save();
                }
            }
        }

        return redirect()->back()->with('success','تم اضافة القسم  بنجاح');
    }

    public function change_sort(Request $request){
        foreach ($request->position as $key=>$value){
            $privilege= OffersCategories::find($value);
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
        return view('admin.categories.subs',['objects'=>OffersCategories::where('parent_id',$id)->orderBy('sort','asc')->get(),'category'=>Categories::find($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $restaurants=Restaurants::where('stop',0)->get();

        $offer_restaurants = OffersCategoriesRestaurants::where('category_id', $id)->pluck('restaurant_id')->toArray();

        return view('admin.offersCategories.add',['object'=> OffersCategories::find($id),'offer_restaurants'=>$offer_restaurants,'restaurants'=>$restaurants]);
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
        $object= OffersCategories::find($id);
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
        $icon = $request->file('icon');
        if ($request->hasFile('icon')) {
            $fileName = 'offer-category-'.time().'-'.uniqid().'.'.$icon->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('icon')->move($destinationPath, $fileName);
            $object->icon=$fileName;
        }
        $object->save();
        OffersCategoriesRestaurants::where('category_id',$object->id)->delete();
        if($request->restaurants){
            foreach ($request->restaurants as $key=>$value){
                if($request->restaurants[$key]  ) {
                    $adv = new OffersCategoriesRestaurants();
                    $adv->restaurant_id = $request->restaurants[$key];
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
        $object = OffersCategories::find($id);
        $object ->delete();
    }
}
