<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionsResources extends JsonResource
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
            'created_at'=>$this->created_at->format('Y/m/d'),
            'store_photo'=>$this->store_photo,
            'get_user_photo'=>@$this->getUser->photo,
            'price'=>@$this->getOffer->price,
        ];
    }
}
