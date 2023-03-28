<?php

namespace App\Http\Resources;

use danielme85\CConverter\Currency;
use Illuminate\Http\Resources\Json\JsonResource;

class HallResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        $valueNOK = Currency::conv($from = 'EGP', $to = 'SAR', $value = 1000, $decimals = 5);
//return $valueNOK;
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'title'=>$this->title,
            'longitude'=>$this->longitude,
            'latitude'=>$this->latitude,
            'capacity'=>$this->capacity,
            'terms'=>$this->terms,
            'policy'=>$this->policy,
            'description'=>$this->description,
            'price_per_hour'=>$this->price_per_hour,
            'currency'=>$this->getCurrency,
            'chairs'=>$this->chairs,
            'address'=>$this->address,
            'is_liked'=>(int)$this->is_liked,
            'likes_count'=>(int)$this->likes_count,
            'rates_count'=>(int)$this->rates_count,
            'hall_rate'=>(int)$this->hall_rate,
            'photos'=>HallsPhotosResource::collection($this->photos),
            'hall_types'=>HallCategoriesResource::collection($this->hallTypes),
            'hall_features'=>HallFeaturesResource::collection($this->hallsFeatures),

        ];
    }
}
