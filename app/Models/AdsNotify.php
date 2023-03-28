<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsNotify extends Model
{
    protected $table = 'ads_notify';
    public function getads() {
  			return $this->belongsTo('App\Models\Ads', 'ads_id', 'id');
  	}



}
