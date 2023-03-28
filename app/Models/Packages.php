<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packages extends Model
{
    public function getCurrency(){
        return $this->belongsTo('\App\Models\Currencies','currency_id','id');
    }
}
