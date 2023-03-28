<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPhotos extends Model
{
    protected $table = 'project_photos';

    public function getProject() {
    	return $this->belongsTo('App\Models\Projects', 'project_id', 'id');
    }
}
