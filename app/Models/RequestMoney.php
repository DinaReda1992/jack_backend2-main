<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestMoney extends Model
{
    protected $table = 'request_money';

    public function getUser() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
public function withdraw(){
        return $this->hasOne('App\Models\Withdraw', 'order_id', 'id');

}
}
