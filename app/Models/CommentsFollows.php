<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentsFollows extends Model
{
    protected $table = 'comments_follows';
    public function getads() {
  			return $this->belongsTo('App\Models\Ads', 'ads_id', 'id');
  	}
    
}
