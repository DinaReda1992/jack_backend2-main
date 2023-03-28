<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MrmandoobCardsDetails extends Model
{
    protected $table = 'mrmandoob_cards_details';

    public function getMainCard()
    {
        return $this->belongsTo('App\Models\MrmandoobCards', 'mrmandoob_card_id', 'id');
    }
    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
