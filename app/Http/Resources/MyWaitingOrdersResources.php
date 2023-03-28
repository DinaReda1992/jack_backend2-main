<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyWaitingOrdersResources extends JsonResource
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
            'delivery_time'=>@$this->getDeliveryTime->name,
            'store_name'=>@$this->store_name,
            'distance'=>$this->distance,
            'get_user'=> new UsersResource($this->getUser)
        ];
    }
}
