<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regions extends Model
{
    protected $table = 'regions';

    public function getStates() {
        return $this->hasMany('App\Models\States', 'region_id', 'id');
    }
    public function getCountry() {
        return $this->belongsTo('App\Models\Countries', 'country_id', 'id');
    }
}
