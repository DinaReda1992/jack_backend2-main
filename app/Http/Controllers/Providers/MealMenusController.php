<?php

namespace App\Http\Controllers\Providers;

use App\Models\ExtraCategories;
use App\Models\MealMenu;
use App\Models\ProductExtraCategories;
use App\Models\ProductMealMenu;
use App\Models\ProductPhotos;
use App\Models\Projects;
use App\Models\SpecialCategories;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\ProductSpecification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class MealMenusController extends Controller
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
        $this->check_provider_settings(434);
        $objects = MealMenu::where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhere('user_id', auth()->user()->main_provider);
        })->get();
        return view('providers.meal_menus.all', ['objects' => $objects]);
    }

//    public function all_products($project_id = 0)
//    {
//        $this->check_provider_settings(433);
//
//        return view('providers.products.all', ['objects' => Products::where('project_id', $project_id)->get(), 'project' => Projects::find($project_id)]);
//    }


//    public function adv_ads($id = 0)
//    {
//        $ads = Products::find($id);
//        if (!$ads) {
//            return redirect()->back()->with('error', 'لا يوجد اعلان بهذا العنوان');
//        }
//        if ($ads->adv == 0) {
//            $ads->adv = 1;
//            $ads->save();
//            return redirect()->back()->with('success', 'تم تثبيت الاعلان في الرئيسية بنجاح .');
//        } else {
//            $ads->adv = 0;
//            $ads->save();
//            return redirect()->back()->with('success', 'تم ازالة التثبيت من الرئيسية بنجاح .');
//        }
//
//    }
//
//    public function adv_slider($id = 0)
//    {
//        $ads = Products::find($id);
//        if (!$ads) {
//            return redirect()->back()->with('error', 'لا يوجد اعلان بهذا العنوان');
//        }
//        if ($ads->adv_slider == 0) {
//            $ads->adv_slider = 1;
//            $ads->save();
//            return redirect()->back()->with('success', 'تم تثبيت الاعلان في القسم بنجاح .');
//        } else {
//            $ads->adv_slider = 0;
//            $ads->save();
//            return redirect()->back()->with('success', 'تم ازالة التثبيت من القسم  بنجاح .');
//        }
//
//    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->check_provider_settings(451);
        $products = Products::where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhere('user_id', auth()->user()->main_provider);
        })->get();
        return view('providers.meal_menus.add', ['products' => $products]);
    }

//    public function add_product($project_id = 0)
//    {
//        $this->check_provider_settings(448);
//
//        return view('providers.products.add', ['project' => Projects::find($project_id)]);
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->check_provider_settings(451);

        $this->validate($request, [
            'name' => 'required',
        ]);
        $object = new MealMenu();
        $object->name = $request->name;
        $provider_id = Auth::user()->user_type_id === 3 ? Auth::id() : Auth::user()->main_provider;
        $object->user_id = $provider_id;

        $object->save();

        if ($request->products) {
            foreach ($request->products as $key => $value) {
                $extraCategory = new ProductMealMenu();
                $extraCategory->meal_menus_id = $object->id;
                $extraCategory->product_id = $value;
                $extraCategory->save();
            }
        }


        return redirect()->back()->with('success', 'تم اضافة القائمة بنجاح .');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->check_provider_settings(452);

        $menu = MealMenu::find($id);
        $provider_id = Auth::user()->user_type_id === 3 ? Auth::id() : Auth::user()->main_provider;

        if ($menu->user_id != $provider_id) {
            return abort(404);
        }
        $products = Products::where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhere('user_id', auth()->user()->main_provider);
        })->get();
        $meal_products = ProductMealMenu::where('meal_menus_id', $menu->id)->pluck('product_id')->toArray();
        return view('providers.meal_menus.add', ['object' => $menu, 'products' => $products, 'meal_products' => $meal_products]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->check_provider_settings(452);

        $object = MealMenu::find($id);
        $provider_id = Auth::user()->user_type_id === 3 ? Auth::id() : Auth::user()->main_provider;

        if ($object->user_id != $provider_id) {
            return abort(404);
        }

        $this->validate($request, [
            'name' => 'required',
        ]);
        $object = new MealMenu();
        $object->name = $request->name;
        $provider_id = Auth::user()->user_type_id === 3 ? Auth::id() : Auth::user()->main_provider;
        $object->user_id = $provider_id;

        $object->save();
        ProductMealMenu::where('meal_menus_id', $object->id)->delete();

        if ($request->products) {
            foreach ($request->products as $key => $value) {
                $extraCategory = new ProductMealMenu();
                $extraCategory->meal_menus_id = $object->id;
                $extraCategory->product_id = $value;
                $extraCategory->save();
            }
        }

        return redirect()->back()->with('success', 'تم تعديل القائمة بنجاح .');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->check_provider_settings(453);

        $ads = MealMenu::find($id);

        if ($ads != false) {
            $extras = ProductExtraCategories::where('product_id', $ads->id)->delete();

            $ads->delete();
        }
    }
}
