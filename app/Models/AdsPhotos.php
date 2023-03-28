<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsPhotos extends Model
{
    protected $table = 'ads_photos';

    public function getAds() {
    	return $this->belongsTo('App\Models\Ads', 'ads_id', 'id');
    }
}
