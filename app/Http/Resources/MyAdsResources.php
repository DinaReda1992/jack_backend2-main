<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyAdsResources extends JsonResource
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
            'title'=>$this->title,
            'price'=>$this->price,
            'views'=>$this->views,
            'likes_count'=>$this->likes_count,
            'main_photo'=>$this->mainPhoto,
            'get_user'=>$this->user,
            'created_at'=>$this->created_at->diffForHumans(),
        ];
    }
}
