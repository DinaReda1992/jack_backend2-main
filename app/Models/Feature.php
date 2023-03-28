<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'features';

    public function halls(){
        return $this->hasMany('App\Models\HallFeatures','feature_id','id');

    }



}
