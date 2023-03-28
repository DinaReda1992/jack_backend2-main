<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestRepresentative extends Model
{
    protected $table = 'request_representative';

	public function getServices() {
			return $this->hasMany('App\Models\RequestUserService', 'user_id', 'id');
	}
    public function getUser() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function bank() {
        return $this->belongsTo('App\Models\Banks', 'bank_id', 'id');
    }


}
