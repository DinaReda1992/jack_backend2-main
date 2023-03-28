<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HallsPhotosResource extends JsonResource
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
            'photo'=>$this->photo,
        ];
    }
}
