<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsOptions extends Model
{
    protected $table = 'ads_options';

    public function getAds() {
    	return $this->belongsTo('App\Models\Ads', 'ads_id', 'id');
    }
}
