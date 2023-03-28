<?php

namespace App\Http\Resources;

use danielme85\CConverter\Currency;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductRatingsResource extends JsonResource
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
            'user_id'=>$this->user_id,
            'rate'=>$this->rate,
            'comment'=>$this->comment,
            'username'=>$this->username,
            'photo'=>$this->photo,
            'created_at'=>$this->created_at->diffForHumans(),
        ];
    }
}
