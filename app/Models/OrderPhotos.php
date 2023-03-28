<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPhotos extends Model
{
    protected $table = 'order_photos';

    public function getOrder() {
    	return $this->belongsTo('App\Models\Orders', 'order_id', 'id');
    }
}
