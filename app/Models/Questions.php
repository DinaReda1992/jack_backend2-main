<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
	public function getCategory() {
			return $this->belongsTo('App\Models\Categories', 'category_id', 'id');
	}
}
