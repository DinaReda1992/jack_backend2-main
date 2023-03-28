<?php

namespace App\Http\Resources;

use App\Models\Days;
use App\Models\ExtraCategories;
use App\Models\ExtraItems;
use App\Models\ProductExtraCategories;
use App\Models\ProductMakeYear;
use App\Models\Products;
use App\Models\Restaurants;
use App\Models\WorkingDays;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class PricingRequestResources extends JsonResource
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
            'status'=>$this->status,
            'description'=>$this->description,
            'ticket_id'=>$this->ticket_id?:0,
            'created_at'=> $this->created_at->diffForHumans(),
            'parts'=>$this->parts,

        ];
    }
}
