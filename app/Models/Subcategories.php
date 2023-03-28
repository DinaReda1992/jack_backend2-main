<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategories extends Model
{
	protected $table = 'sub_categories';
	
	public function getCategory() {
			return $this->belongsTo('App\Models\Categories', 'category_id', 'id');
	}
    public function measurementUnits()
    {

        return $this->belongsToMany('\App\Models\MeasurementUnit', 'measurement_units_categories','id','measurement_id');
    }

}
