<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    protected $table = 'companies';

    public function city(){
        return $this->belongsTo('\App\Models\States','state_id');
    }



}
