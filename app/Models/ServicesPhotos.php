<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicesPhotos extends Model
{
    protected $table = 'service_photos';

    public function getService() {
    	return $this->belongsTo('App\Models\Services', 'service_id', 'id');
    }
}
