<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowCar extends Model
{
    protected $table = 'follow_car';
    public function getcar() {
  			return $this->belongsTo('App\Models\Cars', 'car_id', 'id');
  	}

    public function getmodel() {
  			return $this->belongsTo('App\Models\CarsModels', 'model_id', 'id');
  	}

    public function getFullName() {
  			return $this->belongsTo('App\Models\User', 'user_id', 'id');
  	}

}
