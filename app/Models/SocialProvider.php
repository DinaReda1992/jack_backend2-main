<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialProvider extends Model
{

    protected $table = 'social_providers';

    public function getUser() {
  			return $this->belongsTo('App\Models\User', 'user_id', 'id');
  	}



}
