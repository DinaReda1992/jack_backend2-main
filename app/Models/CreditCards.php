<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCards extends Model
{

    protected $table = 'credit_cards';

    public $timestamps =false;


    public function getCurrency()
    {
        return $this->belongsTo('\App\Models\User', 'user_id');
    }


}
