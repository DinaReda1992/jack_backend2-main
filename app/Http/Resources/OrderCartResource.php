<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            // "shop_id" => $this->shop_id,
            "user_id" => $this->user_id,
            'item_id' => $this->item_id,
            "order_id" => $this->order_id,
            "photo" => url('uploads') . '/' . $this->photo,
            "type" => $this->type,
            "title" => $this->title,
            "price" =>  number_format((float)$this->price, 2, '.', ''),
            "quantity" => $this->quantity,
            // "shop_name" => $this->shop_name,
            // "shipment_price" => $this->shipment_price,
            "measurement_unit" => $this->measurement_unit,
            "total" => number_format((float)$this->total, 2, '.', ''),
        ];
    }
}
