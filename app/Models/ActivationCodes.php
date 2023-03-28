<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivationCodes extends Model
{
    protected $table = 'activation_codes';

    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    
}
