<?php

namespace App\Http\Controllers\Providers;

use App\Http\Resources\Categories;
use App\Models\BankTransfer;
use App\Models\Countries;
use App\Models\DeviceTokens;
use App\Models\Feature;
use App\Models\Hall;
use App\Models\SupplierCategory;
use App\Models\HallFeature;
use App\Models\HallPhoto;
use App\Models\MealMenu;
use App\Models\Messages;
use App\Models\Notification;
use App\Models\Orders;
use App\Models\Packages;
use App\Models\Projects;
use App\Models\RestaurantCategories;
use App\Models\Restaurants;
use App\Models\States;
use App\Models\UserServices;
use App\Models\WorkingDays;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class RestaurantsController extends Controller
{

    public function __construct()
    {


    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->check_provider_settings(438);
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;

        return view('providers.restaurants.all', ['objects' => Restaurants::where('user_id', $provider_id)->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->check_provider_settings(354);
//        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        $states = States::where('country_id', 188)->get();
        $meal_menus = MealMenu::where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhere('user_id', auth()->user()->main_provider);
        })->get();

        return view('providers.restaurants.add', ['states' => $states, 'meal_menus' => $meal_menus]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->check_provider_settings(354);

        $this->validate($request, [
            'title' => 'required',
            'title_en' => 'required',
//            'meal_menu_id' => 'required',
            'min_order_price' => 'required',
            'delivery_price' => 'required',
            'delivery_time' => 'required',
            'state_id' => 'required',
            'longitude' => 'required',
            'address_en' => 'required',
            'address' => 'required',
            'latitude' => 'required',
//            'description' => 'max:650',
//            'description_en' => 'max:650',
            'photo' => 'required',

        ]);
        $object = new Restaurants();
        $object->title = $request->title;
        $object->title_en = $request->title_en;
        $object->min_order_price = $request->min_order_price;
        $object->delivery_price = $request->delivery_price;
        $object->delivery_time = $request->delivery_time;
        $object->state_id = $request->state_id;
        $object->longitude = $request->longitude;
        $object->latitude = $request->latitude;
        $object->address = $request->address;
        $object->address_en = $request->address_en;
        $object->description = $request->description ?: '';
        $object->description_en = $request->description_en ?: '';
        $object->meal_menu_id = $request->meal_menu_id ?: 0;
        $object->stop = $request->publish ? 0 : 1;
        $object->free_delivery = $request->free_delivery ? 1 : 0;
        $object->delivery_limit = $request->free_delivery && $request->delivery_limit ? $request->delivery_limit : 0;

        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;
        $object->user_id = $provider_id;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');

            $fileName = 'restaurant-' . time() . '-' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $photo->move($destinationPath, $fileName);
            $object->logo = $fileName;

        }
        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            $fileName = 'restaurant-cover-' . time() . '-' . uniqid() . '.' . $cover->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $cover->move($destinationPath, $fileName);
            $object->cover = $fileName;

        }
        $object->save();
        if ($request->categories) {
            foreach ($request->categories as $key => $value) {
                $extraCategory = new RestaurantCategories();
                $extraCategory->restaurant_id = $object->id;
                $extraCategory->category_id = $value;
                $extraCategory->save();
            }
        }

        return redirect()->back()->with('success', 'تم اضافة المطعم بنجاح .');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->check_provider_settings(455);
        $states = States::where('country_id', 188)->get();
        $meal_menus = MealMenu::where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhere('user_id', auth()->user()->main_provider);
        })->get();
        $object = Restaurants::where('id', $id)->where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhere('user_id', auth()->user()->main_provider);
        })->first();;
        if (!$object) {
            return abort(404);
        }
        $work_times = WorkingDays::where('restaurant_id', $object->id)->where('is_worked',1)->pluck('day_id')->toArray();
//dd($work_times);
        $restaurant_categories = RestaurantCategories::where('restaurant_id', $object->id)->pluck('category_id')->toArray();
        return view('providers.restaurants.add', ['object' => $object, 'states' => $states, 'meal_menus' => $meal_menus, 'restaurant_categories' => $restaurant_categories,'work_times'=>$work_times]);
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
//        $arr=[];
//        for ($counter=0;$counter<7;$counter++){
//            $arr[]=['check'=>@$request->work_day[$counter]];
//        }
//        dd($arr);
//        $jsonFlavours = json_encode($request->work_day);
//
//        // Turn the JSON string back into an array
//        $arrayFlavours = json_decode($jsonFlavours, true);
//        dd($arrayFlavours);

        $this->check_provider_settings(455);

        $object = Restaurants::where('id', $id)->where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhere('user_id', auth()->user()->main_provider);
        })->first();
        if (!$object) {
            return redirect()->back()->with('error', 'لا يوجد مطعم للتعديل');
        }
        $this->validate($request, [
            'title' => 'required',
            'title_en' => 'required',
//            'meal_menu_id' => 'required',
            'min_order_price' => 'required',
            'delivery_price' => 'required',
            'delivery_time' => 'required',
            'state_id' => 'required',
            'longitude' => 'required',
            'address_en' => 'required',
            'address' => 'required',
            'latitude' => 'required',
//            'description' => 'max:650',
//            'description_en' => 'max:650',
//            'photo'=>'required',

        ]);
        $object->title = $request->title;
        $object->title_en = $request->title_en;
        $object->min_order_price = $request->min_order_price;
        $object->delivery_price = $request->delivery_price;
        $object->delivery_time = $request->delivery_time;
        $object->state_id = $request->state_id;
        $object->longitude = $request->longitude;
        $object->latitude = $request->latitude;
        $object->address = $request->address;
        $object->address_en = $request->address_en;
        $object->description = $request->description ?: '';
        $object->description_en = $request->description_en ?: '';
        $object->meal_menu_id = $request->meal_menu_id ?: 0;
        $object->stop = $request->publish ? 0 : 1;
        $object->free_delivery = $request->free_delivery ? 1 : 0;
        $object->delivery_limit = $request->free_delivery && $request->delivery_limit ? $request->delivery_limit : 0;


        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;
        $object->user_id = $provider_id;
        $path = 'uploads/';

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $old_photo = $path . $object->photo;
            if (is_file($old_photo)) unlink($old_photo);

            $fileName = 'restaurant-' . time() . '-' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $photo->move($destinationPath, $fileName);
            $object->logo = $fileName;

        }
        if ($request->hasFile('cover')) {
            $old_cover = $path . $object->photo;
            if (is_file($old_cover)) unlink($old_cover);

            $cover = $request->file('cover');
            $fileName = 'restaurant-cover-' . time() . '-' . uniqid() . '.' . $cover->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $cover->move($destinationPath, $fileName);
            $object->cover = $fileName;

        }

        $object->approved = 1;
        $object->save();
        RestaurantCategories::where('restaurant_id', $object->id)->delete();

        if ($request->categories) {
            foreach ($request->categories as $key => $value) {
                $extraCategory = new RestaurantCategories();
                $extraCategory->restaurant_id = $object->id;
                $extraCategory->category_id = $value;
                $extraCategory->save();
            }
        }
        foreach ($request->day_id as $key => $value) {
            $work=WorkingDays::where('restaurant_id',$object->id)->where('day_id',$value)->first();
            if(!$work){
                $work = new WorkingDays();
            }
            $work->restaurant_id=$object->id;
            $work->day_id=$value;
            $time_from = $request->time_from[$key];
            $x = preg_replace('/\s*:\s*/', ':', $time_from);
            $work->time_from=  date("H:i", strtotime($x));

            $time_to = $request->time_to[$key];
            $x = preg_replace('/\s*:\s*/', ':', $time_to);
            $work->time_to=  date("H:i", strtotime($x));

//            $work->time_from=$request->time_from[$key];
//            $work->time_to=$request->time_to[$key];
            $work->is_worked=@$request->is_worked[$value]?1:0;
$work->save();
        }


        return redirect()->back()->with('success', 'تم تعديل المطعم بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $restaurant = Restaurants::where('id', $id)->where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhere('user_id', auth()->user()->main_provider);
        })->first();
        if ($restaurant) {
            $restaurant->stop = $restaurant->stop ? 0 : 1;
            $restaurant->save();

            return 1;
        }
        return -1;

    }

    public function stopOpenRestaurant($id)
    {
        $this->check_provider_settings(456);

//status = 0 جديده
//status = 1 مقبولة
//status = 2 معطله
//status = 3 مرفوضة
        $restaurant = Restaurants::where('id', $id)
            ->where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhere('user_id', auth()->user()->main_provider);
            })->first();

        if ($restaurant) {
            $restaurant->stop = $restaurant->stop == 1 ? 0 : 1;
            $restaurant->save();
            return redirect()->back()->with('success', 'تم تغيير حالة المطعم بنجاح .');
        }
        return redirect()->back()->with('error', 'لا يوجد مطعم للتعديل .');

    }
}
