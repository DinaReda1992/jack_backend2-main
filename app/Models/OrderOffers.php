<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderOffers extends Model
{
    protected $table = 'order_offers';

    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function getOrder()
    {
        return $this->belongsTo('App\Models\Orders', 'order_id', 'id');
    }
}
