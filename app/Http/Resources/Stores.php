<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Stores extends JsonResource
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
            'name'=>$this->name,
            'address'=>$this->address,
            'commercial_registration_no'=>$this->commercial_registration_no,
            'distance'=>$this->distance,
            'photo'=>$this->photo !="" ? url('/')."/uploads/" .$this->photo : @$this->getCategory->photo ,
            'city'=> @$this->getState
        ];
    }
}
