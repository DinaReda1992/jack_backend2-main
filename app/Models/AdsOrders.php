<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsOrders extends Model
{
    protected $table = 'ads_orders';

    public function getads() {
  			return $this->belongsTo('App\Models\Ads', 'ads_id', 'id');
  	}



}
