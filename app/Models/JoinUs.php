<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JoinUs extends Model
{
    protected $table = 'join_us';

    public function getState()
    {
        return $this->belongsTo('App\Models\States', 'city_id');
    }

}
