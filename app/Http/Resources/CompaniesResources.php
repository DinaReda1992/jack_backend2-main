<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompaniesResources extends JsonResource
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
            'description'=>$this->description,
            'type'=>$this->type,
            'address'=>$this->address,
            'phone'=>$this->phone,
            'email'=>$this->email,
            'photo'=>$this->photo,
            'city'=>$this->city,
            'created_at'=>$this->created_at->diffForHumans(),
        ];
    }}
