<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentsNotify extends Model
{
    protected $table = 'comments_notify';
    public function getads() {
  			return $this->belongsTo('App\Models\Ads', 'ads_id', 'id');
  	}

    public function getcomment() {
  			return $this->belongsTo('App\Models\Comments', 'comment_id', 'id');
  	}

}
