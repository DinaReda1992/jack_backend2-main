<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flights extends Model
{
    protected $table = 'flights';

    public function getUser()
    {
      return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function fromCity()
    {
        return $this->belongsTo('App\Models\States', 'from_city', 'id');
    }
    public function toCity()
    {
        return $this->belongsTo('App\Models\States', 'to_city', 'id');
    }

}
