<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
	public function getState() {
			return $this->belongsTo('App\Models\States', 'state_id', 'id');
	}
}
