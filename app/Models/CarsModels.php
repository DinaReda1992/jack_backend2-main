<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarsModels extends Model
{
	protected $table = 'cars_sub_category';
	
	public function getCarsCategory() {
			return $this->belongsTo('App\Models\Cars', 'cars_category_id', 'id');
	}
    public function getCategory() {
        return $this->belongsTo('App\Models\Categories', 'category_id', 'id');
    }
}

