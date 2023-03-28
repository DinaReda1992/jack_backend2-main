<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $table = 'comments';

    public function getUser()
    {
      return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }


    public function getAds()
    {
      return $this->belongsTo('App\Models\Ads', 'ads_id', 'id');
    }

}
