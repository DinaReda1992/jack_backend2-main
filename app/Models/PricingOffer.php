<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingOffer extends Model
{
    protected $table = 'pricing_offers';

    public function shop(){
        return $this->belongsTo('\App\Models\User','provider_id');
    }
    public function manufactureType(){
        return $this->belongsTo('\App\Models\ManufactureType','manufacture_type');
    }
    public function PricingOrderType(){
        return $this->belongsTo('\App\Models\PricingOrderType','order_type');
    }
    public function part(){
        return $this->belongsTo('\App\Models\PricingOrderPart','part_id');
    }


}