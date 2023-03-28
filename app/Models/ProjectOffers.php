<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectOffers extends Model
{
    protected $table = 'project_offers';

    public function getProject() {
    	return $this->belongsTo('App\Models\Projects', 'project_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


}