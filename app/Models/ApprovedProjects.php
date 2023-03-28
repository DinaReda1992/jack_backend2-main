<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovedProjects extends Model
{
    protected $table = 'approved_projects';

    public function getProject()
    {
      return $this->belongsTo('App\Models\Projects', 'project_id', 'id');
    }


    public function getOffer()
    {
      return $this->belongsTo('App\Models\ProjectOffers', 'offer_id', 'id');
    }

}