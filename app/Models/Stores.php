<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stores extends Model
{
    protected $table = 'stores';
    public function getCategory(){
        return $this->belongsTo('App\Models\Categories','category_id','id');
    }
    public function getState(){
        return $this->belongsTo('App\Models\States','state_id','id');
    }
    public function getUser(){
        return $this->belongsTo('App\Models\User','user_id','id');
    }
    public function city(){
        return $this->belongsTo('\App\Models\States','state_id');
    }

}