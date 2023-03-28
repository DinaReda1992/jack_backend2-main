<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faqs extends Model
{
    protected $table = 'faqs';
    public function getcar() {
  			return $this->belongsTo('App\Models\Cars', 'car_id', 'id');
  	}
  	
}
