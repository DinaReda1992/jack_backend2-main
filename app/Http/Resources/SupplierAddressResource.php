<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $select_name = app()->getLocale() == "ar" ? 'name' : 'name_en';

        return [
            'address' => $this->address,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'phone' => $this->phone,
            'region_name' => $this->region->$select_name,
            'region_id' => $this->region_id,
            'state_name' => $this->state->$select_name,
            'state_id' => $this->state_id,
        ];
    }
}
