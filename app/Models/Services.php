<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{

    public function getSubcategory()
    {
        return $this->belongsTo('App\Models\Subcategories', 'sub_category_id', 'id');
    }

    public function advantages()
    {
        return $this->hasMany('App\Models\ServiceAdvantages', 'service_id');
    }

    public function getMessages()
    {
        return $this->hasMany('App\Models\MessagesNotifications', 'service_id');
    }


    public function getCategory()
    {
        return $this->belongsTo('App\Models\Categories', 'category_id', 'id');
    }

    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function getCountry()
    {
        return $this->belongsTo('App\Models\Countries', 'country_id');
    }

    public function getState()
    {
        return $this->belongsTo('App\Models\States', 'state_id');
    }

    public function getMainPhoto()
    {
        return $this->belongsTo('App\Models\ServicesPhotos', 'service_id');
    }

    public function getphotos()
    {
        return $this->hasMany('App\Models\ServicesPhotos', 'service_id');
    }


    public function Images($ads = 0)
    {

        return $this->hasMany('\App\Models\AdsPhotos', 'ads_id');
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
