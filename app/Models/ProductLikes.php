<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLikes extends Model
{
    protected $table = 'product_likes';
    public function getProduct() {
  			return $this->belongsTo('App\Models\Products', 'product_id', 'id');
  	}
}
