<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservations extends Model
{
    protected $table = 'reservations';


    public function hall()
    {
        return $this->belongsTo('App\Models\Hall', 'hall_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function bankTransfer()
    {
        return $this->hasOne('App\Models\BankTransfer', 'reservation_id', 'id');
    }
    public function reservationFeatures()
    {
        return $this->hasMany('App\Models\ReservationFeatures', 'reservation_id', 'id');
    }
}
