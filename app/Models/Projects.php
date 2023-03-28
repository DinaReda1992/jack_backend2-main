<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    protected $table = 'projects';

    public function getSubcategory()
    {
        return $this->belongsTo('App\Models\Subcategories', 'sub_category_id', 'id');
    }

    public function getStyle()
    {
        return $this->belongsTo('App\Models\Styles', 'style_id', 'id');
    }

    public function getCategory()
    {
        return $this->belongsTo('App\Models\Categories', 'category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\Cities', 'city_id');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\States', 'state_id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Countries', 'country_id');
    }




    public function getPhotos()
    {

        return $this->hasMany('\App\Models\ProjectPhotos', 'project_id');
    }

    public function getProducts()
    {
        return $this->hasMany('\App\Models\Products', 'project_id');
    }

    public function Comments($ads = 0)
    {
        return $this->hasMany('\App\Models\Comments', 'ads_id');
    }

    public function getRatings()
    {
        return $this->hasMany('\App\Models\ProjectRating', 'project_id');
    }

    public function offers($ads = 0)
    {
        return $this->hasMany('\App\Models\ProjectOffers', 'project_id');
    }

    
}
