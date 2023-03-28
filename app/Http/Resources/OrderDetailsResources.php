<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResources extends JsonResource
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
            'delivery_time'=>@$this->getDeliveryTime,
            'distance'=>$this->distance,
            'status'=>$this->status,
            'from_long'=>$this->from_long,
            'from_lat'=>$this->from_lat,
            'to_long'=>$this->to_long,
            'to_lat'=>$this->to_lat,
            'from_address'=> $this->from_address,
            'to_address'=> $this->to_address,
            'store_name'=>$this->store_name,
            'store_icon'=>$this->store_icon,
            'store_photo'=>$this->store_photo,
            'description'=>$this->description,
            'place_id'=>$this->place_id,
            'get_user'=> new UsersResource(@$this->getUser),
            'get_representative'=> new UsersResource(@$this->getRepresentative),
            'photos'=> @$this->photos,
            'get_offer'=> new OfferResource(@$this->getOffer)
        ];
    }
}
