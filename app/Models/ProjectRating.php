<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRating extends Model
{
    protected $table = 'project_rating';
    public function getProduct() {
  			return $this->belongsTo('App\Models\Projects', 'project_id', 'id');
  	}
    public function getUser() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
