<?php

namespace App\Http\Resources;

use App\Models\Days;
use App\Models\ProductMealMenu;
use App\Models\Products;
use App\Models\RestaurantCategories;
use App\Models\Restaurants;
use App\Models\SpecialCategories;
use App\Models\WorkingDays;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class RestuarantResources extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $description = "";
        if(request()->header('Accept-language')=="ar"){
            foreach (\App\Models\Categories::whereIn('id',function ($query) { $query->select('category_id')
                ->from(with(new RestaurantCategories())->getTable())
                ->where('restaurant_id',$this->id);
            })->get() as $cat){
                $description.=$cat->name." ";
            }

            $restaurant_categories = SpecialCategories::select('id','name')
                ->whereIn('id',function ($query) { $query->select('special_category_id')
                    ->from(with(new Products())->getTable())
                    ->where('user_id',$this->user_id);
                })
                ->where('user_id',$this->user_id)->get();
            foreach ($restaurant_categories as $cat){
                    $cat->{'meals'} = Products::select('id','title','description','price')
                        ->whereIn('id',function ($query) { $query->select('product_id')
                            ->from(with(new ProductMealMenu())->getTable())
                            ->where('meal_menus_id',$this->meal_menu_id);
                        })
                        ->selectRaw('(CASE WHEN photo = "" THEN "'.url('/')."/images/placeholder.png".'" ELSE (CONCAT ("'.URL::to('/').'/uploads/", photo)) END) AS photo')
                        ->where('special_category_id',$cat->id)->where('user_id',$this->user_id)->get();
            }

            $working_days= Days::select('id','name')->get();
            foreach ($working_days as $working){
                $restaurant_day = WorkingDays::where('day_id',$working->id)->where('restaurant_id',$this->id)->first();
                if($restaurant_day){
                    $working->{'day'}=[
                        'is_working'=>$restaurant_day->is_worked,
                        'time_from'=>$restaurant_day->time_from,
                        'time_to'=>$restaurant_day->time_to,
                    ];
                }else{
                    $working->{'day'}=[
                        'is_working'=>1,
                        'time_from'=>"00:00:00",
                        'time_to'=>"00:00:00",
                    ];
                }

            }

        }else{
            foreach (\App\Models\Categories::whereIn('id',function ($query) { $query->select('category_id')
                ->from(with(new RestaurantCategories())->getTable())
                ->where('restaurant_id',$this->id);
            })->get() as $cat){
                $description.=$cat->name_en." ";
            }

            $restaurant_categories = SpecialCategories::select('id','name')
                ->whereIn('id',function ($query) { $query->select('special_category_id')
                    ->from(with(new Products())->getTable())
                    ->where('user_id',$this->user_id);
                })
                ->where('user_id',$this->user_id)->get();
            foreach ($restaurant_categories as $cat){
                $cat->{'meals'} = Products::select('id','title_en as title','description_en as description','price')
                    ->whereIn('id',function ($query) { $query->select('product_id')
                        ->from(with(new ProductMealMenu())->getTable())
                        ->where('meal_menus_id',$this->meal_menu_id);
                    })
                    ->selectRaw('(CASE WHEN photo = "" THEN "'.url('/')."/images/placeholder.png".'" ELSE (CONCAT ("'.URL::to('/').'/uploads/", photo)) END) AS photo')
                    ->where('special_category_id',$cat->id)->where('user_id',$this->user_id)->get();
            }

            $working_days= Days::select('id','name_en as name')->get();
            foreach ($working_days as $working){
                $restaurant_day = WorkingDays::where('day_id',$working->id)->where('restaurant_id',$this->id)->first();
                if($restaurant_day){
                    $working->{'day'}=[
                        'is_working'=>$restaurant_day->is_worked,
                        'time_from'=>$restaurant_day->time_from,
                        'time_to'=>$restaurant_day->time_to,
                    ];
                }else{
                    $working->{'day'}=[
                        'is_working'=>1,
                        'time_from'=>"00:00:00",
                        'time_to'=>"00:00:00",
                    ];
                }

            }


        }



        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'description'=>$description,
            'delivery_price'=>$this->delivery_price,
            'distance'=>$this->distance,
            'min_order_price'=>$this->min_order_price,
            'free_delivery'=>$this->free_delivery,
            'delivery_limit'=>$this->delivery_limit,
            'address'=>$this->address,
            'longitude'=>$this->longitude,
            'latitude'=>$this->latitude,
            'logo'=>$this->logo,
            'cover'=>$this->cover,
            'restaurant_rate'=>(double)$this->restaurant_rate,
            'products'=>$restaurant_categories,
            'working_days'=>$working_days,
        ];
    }
}
