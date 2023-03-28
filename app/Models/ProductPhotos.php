<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPhotos extends Model
{
    protected $table = 'product_photos';
    public function product() {
    	return $this->belongsTo('App\Models\Products', 'product_id', 'id');
    }
}
