<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screens extends Model
{
    protected $table = 'screens';
    
    public function getDetails() {
    	return $this->hasMany('App\Models\ScreenDetails', 'screen_id', 'id');
    }

}