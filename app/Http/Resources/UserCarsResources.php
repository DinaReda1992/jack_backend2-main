<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCarsResources extends JsonResource
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
            'structure_photo' => $this->structure_photo?  url('/').'/uploads/'.$this->structure_photo :'',
            'structure_no'=>$this->structure_no,
            'created_at'=>$this->created_at->diffForHumans(),
            'make'=>$this->make,
            'year'=>$this->year,
            'model'=>$this->model,
            'is_default'=>$this->is_default?:0


        ];
    }
}
