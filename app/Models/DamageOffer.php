<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageOffer extends Model
{
    protected $table = 'damage_offers';

    public function shop(){
        return $this->belongsTo('\App\Models\User','provider_id');
    }
    public function order(){
        return $this->belongsTo('\App\Models\DamageEstimate','order_id');
    }



}
