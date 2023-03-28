<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    protected $table = 'likes';
    public function getProject() {
  			return $this->belongsTo('App\Models\Projects', 'project_id', 'id');
  	}
}
