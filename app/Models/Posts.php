<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    protected $table = 'posts';


    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


    public function getComments()
    {
        return $this->hasMany('\App\Models\Comments', 'post_id');
    }


    
}
