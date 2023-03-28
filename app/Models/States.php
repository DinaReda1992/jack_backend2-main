<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    public function getCountry() {
        return $this->belongsTo('App\Models\Countries', 'country_id', 'id');
    }
    public function getRegion() {

        return $this->belongsTo('App\Models\Regions', 'region_id', 'id');
    }

}
