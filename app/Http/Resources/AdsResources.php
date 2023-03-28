<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdsResources extends JsonResource
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
            'comments_count'=>$this->comments_count,
            'photo'=>$this->mainPhoto? $this->mainPhoto : ['id'=>0, 'ads_id'=>$this->id ,'photo'=>url('/').'/images/placeholder.png'],
            'get_user'=>$this->user,
            'city'=>$this->city,
            'is_adv'=> @$this->user->user_type_id == 4 ? true : false ,
            'created_at'=>$this->created_at->diffForHumans(),
        ];
    }}
