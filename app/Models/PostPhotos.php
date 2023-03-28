<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostPhotos extends Model
{
    protected $table = 'post_photos';

    public function getProject() {
    	return $this->belongsTo('App\Models\Posts', 'post_id', 'id');
    }
}
