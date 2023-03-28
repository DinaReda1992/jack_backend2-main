<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    protected $table = 'reports';

    public function getSubcategory() {
        return $this->belongsTo('App\Models\Categories', 'sub_category_id', 'id');
    }

    public function getAds() {
        return $this->belongsTo('App\Models\Ads', 'ads_id', 'id');
    }

    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
