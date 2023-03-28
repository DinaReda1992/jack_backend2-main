<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'details' => $this->details,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'email' => $this->email,
            'phone1' => $this->phone1,
            'phone2' => $this->phone2,
            'is_home' => $this->is_home,
            'region_name' => $this->region->$select_name,
            'region_id' => $this->region_id,
            'state_name' => $this->state->$select_name,
            'state_id' => $this->state_id,
        ];
    }
}
