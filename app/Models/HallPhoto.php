<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HallPhoto extends Model
{

    protected $table = 'hall_photos';
    public function hall() {
        return $this->belongsTo('App\Models\Hall', 'hall_id', 'id');
    }

}
