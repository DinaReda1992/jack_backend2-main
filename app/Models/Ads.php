<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    protected $table = 'ads';

    public function getSubcategory()
    {
        return $this->belongsTo('App\Models\Categories', 'sub_category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function options()
    {
        return $this->hasMany('App\Models\AdsOptions', 'ads_id');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\States', 'state_id');
    }

    public function Images($ads = 0)
    {
        return $this->hasMany('\App\Models\AdsPhotos', 'ads_id');
    }
    public function photos($ads = 0)
    {
        return $this->hasMany('\App\Models\AdsPhotos', 'ads_id');
    }
    public function mainPhoto()
    {
        return $this->hasOne('\App\Models\AdsPhotos', 'ads_id')->where('type',1);
    }

    public function comments()
    {
        return $this->hasMany('\App\Models\Comments', 'ads_id');
    }
    
}
