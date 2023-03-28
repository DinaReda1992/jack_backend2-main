<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    protected $table = 'delivery_addresses';
    public function state() {
        return $this->belongsTo('App\Models\States', 'state_id', 'id');
    }


}
