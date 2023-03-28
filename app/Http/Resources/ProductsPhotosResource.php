<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductsPhotosResource extends JsonResource
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
            'photo' => is_file('uploads/' . $this->photo) ? asset('uploads/') . '/' . $this->photo : '/asset/images/product-03.jpg',
            'thumb' => $this->thumb,
        ];
    }
}
