<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer_feature extends Model
{
    protected $table='offer_features';


    public function offer()
    {
        return $this->belongsTo(Clinic_offer::class, 'offer_id');
    }

}
