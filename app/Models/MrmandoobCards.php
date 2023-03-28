<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MrmandoobCards extends Model
{
    protected $table = 'mrmandoob_cards';

    public function getCardsDetails()
    {
        return $this->hasMany('App\Models\MrmandoobCardsDetails', 'mrmandoob_card_id', 'id');
    }

    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
