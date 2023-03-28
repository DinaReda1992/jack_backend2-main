<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MandoobPayments extends Model
{

    protected $table = "mandoob_payments";

    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }


    
}
