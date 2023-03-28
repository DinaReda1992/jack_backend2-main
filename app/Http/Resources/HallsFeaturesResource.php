<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HallsFeaturesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->feature_id,
            'price'=>$this->price,
            'name'=> @$this->feature->name,
            'is_one'=> @$this->feature->is_one,
            'description'=>$this->description,
            'currency'=>"SAR",
        ];
    }
}
