<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSpecification extends Model
{
    protected $table = 'product_specification';

    public function getProduct() {
    	return $this->belongsTo('App\Models\Products', 'product_id', 'id');
    }
}
