<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marchant extends Model
{
    protected $table = 'marchant';

    public function getcity()
    {
      return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function getstate()
    {
      return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

}
