<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageEstimate extends Model
{
    protected $table = 'damage_estimates';


    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function photos(){
        return $this->hasMany('App\Models\DamagePhoto','damage_id');
    }
    public function car()
    {
        return $this->belongsTo('App\Models\UserCar', 'car_id');
    }
    public function offers()
    {
        return $this->hasMany('App\Models\DamageOffer', 'order_id');
    }
    public function service()
    {

        return $this->belongsTo('App\Models\ServicesCategories', 'service_id');
    }

}
