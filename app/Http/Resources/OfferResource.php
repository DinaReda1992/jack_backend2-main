<?php

namespace App\Http\Resources;

use App\Models\Orders;
use App\Models\Settings;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'order_id'=>$this->order_id,
            'cost_from'=>$this->cost_from,
            'cost_to'=>$this->cost_to,
                    'time'=>$this->time,
                    'description'=>$this->description,
                    'created_at'=>$this->created_at->diffForHumans(),
                    'provider_id'=>$this->provider_id,
                    'status'=>$this->status,
                    'shop'=>$this->shop,

                ];
    }
}
