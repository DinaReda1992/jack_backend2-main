<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Works extends Model
{
    protected $table = 'works';



    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }




    public function Images($ads = 0)
    {

        return $this->hasMany('\App\Models\WorkPhotos', 'work_id');
    }

    public function Comments($ads = 0)
    {
        return $this->hasMany('\App\Models\Comments', 'ads_id');
    }

    public function offers($ads = 0)
    {
        return $this->hasMany('\App\Models\ProjectOffers', 'project_id');
    }

    
}
