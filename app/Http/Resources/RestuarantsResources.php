<?php

namespace App\Http\Resources;

use App\Models\RestaurantCategories;
use Illuminate\Http\Resources\Json\JsonResource;

class RestuarantsResources extends JsonResource
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
    }else{
        foreach (\App\Models\Categories::whereIn('id',function ($query) { $query->select('category_id')
            ->from(with(new RestaurantCategories())->getTable())
            ->where('restaurant_id',$this->id);
        })->get() as $cat){
            $description.=$cat->name_en." ";
        }
    }

        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'description'=>$description,
            'delivery_price'=>$this->delivery_price,
            'distance'=>$this->distance,
            'logo'=>$this->logo,
            'restaurant_rate'=>(double)$this->restaurant_rate,
        ];
    }
}
