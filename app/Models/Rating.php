<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'ratings';

    public function getUser(){
        return $this -> belongsTo('App\Models\User','user_id');
    }
    public function user(){
        return $this -> belongsTo('App\Models\User','user_id');
    }

}