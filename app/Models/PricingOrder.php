<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingOrder extends Model
{
    protected $table = 'pricing_orders';



    public function parts()
    {

        return $this->hasMany('\App\Models\PricingOrderPart', 'order_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function car()
    {
        return $this->belongsTo('App\Models\UserCar', 'car_id');
    }
    public function state()
    {
        return $this->belongsTo('App\Models\States', 'state_id');
    }

}
