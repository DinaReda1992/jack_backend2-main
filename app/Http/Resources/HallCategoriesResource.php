<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HallCategoriesResource extends JsonResource
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
            'name'=> app()->getLocale()=="ar" ? $this->name : $this->name_en
        ];
    }
}
