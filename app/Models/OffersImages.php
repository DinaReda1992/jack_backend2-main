<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffersImages extends Model
{
    protected $table = 'offers_images';
    public function restaurant() {
  			return $this->belongsTo('App\Models\Restaurant', 'restaurant_id', 'id');
  	}
    
}
