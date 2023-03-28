<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkPhotos extends Model
{
    protected $table = 'work_photos';

    public function getWorks() {
    	return $this->belongsTo('App\Models\Works', 'work_id', 'id');
    }
}
