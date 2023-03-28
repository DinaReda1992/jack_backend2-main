<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DamageOfferResources extends JsonResource
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
            'cost_from'=>$this->cost_from,
            'cost_to'=>$this->cost_to,
            'time'=>$this->time,
            'description'=>$this->description,
            'shop'=>$this->shop,
            'status'=>$this->status,
            'created_at'=>$this->created_at->diffForHumans(),
        ];
    }
}
