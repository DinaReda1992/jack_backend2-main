<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    protected $table = 'cars_category';
    
    public function getSubcategory() {
    	return $this->belongsTo('App\Models\Categories', 'sub_category_id', 'id');
    }

    public function getModels() {
        return $this->hasMany('App\Models\CarsModels', 'cars_category_id', 'id');
    }

    public function getCategory() {
        return $this->belongsTo('App\Models\Categories', 'category_id', 'id');
    }
}