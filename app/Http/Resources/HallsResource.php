<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HallsResource extends JsonResource
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
            'id'=>$this->id,
            'title'=>$this->title,
            'price_per_hour'=>$this->price_per_hour,
            'currency'=>$this->getCurrency,
            'chairs'=>$this->chairs,
            'address'=>$this->address,
            'is_liked'=>(int)$this->is_liked,
            'likes_count'=>(int)$this->likes_count,
            'hall_rate'=>(int)$this->hall_rate,
            'latitude'=>$this->latitude,
            'longitude'=>$this->longitude,
            'photos'=>HallsPhotosResource::collection($this->photos),
        ];
    }
}
