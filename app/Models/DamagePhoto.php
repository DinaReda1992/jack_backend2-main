<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagePhoto extends Model
{
    protected $table = 'damage_photos';

    public function damage()
    {
        return $this->belongsTo('App\Models\DamageEstimate', 'damage_id', 'id');
    }
}
