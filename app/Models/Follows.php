<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follows extends Model
{
    protected $table = 'follows';

    public function getUser() {
  			return $this->belongsTo('App\Models\User', 'user_id', 'id');
  	}
    public function getFollowedUser() {
        return $this->belongsTo('App\Models\User', 'followed_user', 'id');
    }


}
