<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdsDetailsResources extends JsonResource
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
            'description'=>$this->description,
            'price'=>$this->price,
            'views'=>$this->views,
            'likes_count'=>$this->likes_count,
            'comments_count'=>$this->comments_count,
            'photos'=>$this->photos,
            'options'=>$this->options,
            'comments'=>CommentsResources::collection($this->comments),
            'get_user'=>$this->user,
            'city'=>$this->city,
            'is_liked'=>$this->is_liked,
            'created_at'=>$this->created_at->diffForHumans(),
        ];
    }}
