<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersDetails extends Model
{
    protected $table = 'order_details';

    public function getOrder()
    {
        return $this->belongsTo('App\Models\Orders', 'order_id', 'id');
    }
    public function getCard()
    {
        return $this->belongsTo('App\Models\Cards', 'card_id', 'id');
    }
}
