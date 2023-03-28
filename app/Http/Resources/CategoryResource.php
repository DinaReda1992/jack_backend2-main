<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => app()->getLocale() == "ar" ? $this->name : $this->name_en,
            'photo' => $this->photo != "" ? url('/') . "uploads/" . $this->photo : '/images/placeholder.png',
            'sub_categories' => SubCategoryResource::collection($this->subCategories),
        ];
    }
}
