<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blocks extends Model
{
	public function getCity() {
			return $this->belongsTo('App\Models\Cities', 'city_id', 'id');
	}
}
