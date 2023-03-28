<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    protected $table = 'user_rating';

    public function getUser() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function getRatedUser() {
        return $this->belongsTo('App\Models\User', 'rated_user', 'id');
    }


}