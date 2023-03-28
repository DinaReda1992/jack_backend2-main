<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationFeatures extends Model
{

    protected $table = 'reservation_features';

	public function feature() {
			return $this->hasMany('App\Models\Feature', 'feature_id', 'id');
	}

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function reservation() {
        return $this->belongsTo('App\Models\Reservations', 'reservation_id', 'id');
    }
    public function getFeature() {
        return $this->belongsTo('App\Models\Feature', 'feature_id', 'id');
    }
}
