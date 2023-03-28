<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Reservations extends JsonResource
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
            'reservation_id'=>$this->id,
            'from_time'=>$this->from_time,
            'to_time'=>$this->to_time,
            'reservation_price'=>(float)$this->reservation_price ,
            'features_price'=>(float)$this->features_price ,
            'price'=>(float)$this->final_price,
            'reserved_date'=> $this->date ,

        ];
    }
}
