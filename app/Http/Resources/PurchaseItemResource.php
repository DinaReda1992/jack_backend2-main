<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $select_title = app()->getLocale() == "ar" ? 'title' : 'title_en';
        $select_name = app()->getLocale() == "ar" ? 'name' : 'name_en';
        ini_set('serialize_precision', -1);
        return [
            'id' => $this->id,
            "shop_id" => $this->provider_id,
            "item_id" => $this->product_id,
            "order_id" => $this->order_id,
            "title" => $this->product->$select_title ?: $this->product->title,
            "price" =>  $this->price,
            "photo" => url('uploads') . '/' . $this->product->photo,
            "quantity" => $this->quantity,
            "shop_name" => $this->provider->username,
            "measurement_unit" => $this->product->measurement->$select_name,
            "total" => $this->price * $this->quantity,
        ];
    }
}
