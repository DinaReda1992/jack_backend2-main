<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $table = 'withdraws';



    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\RequestMoney', 'order_id');
    }




    public function bank()
    {

        return $this->belongsTo('\App\Models\BankAccounts', 'bank_id');
    }


    
}
