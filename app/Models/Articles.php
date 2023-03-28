<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    protected $table = 'articles';

    public function getSubcategory()
    {
        return $this->belongsTo('App\Models\BlogSubcategories', 'sub_category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function Images($ads = 0)
    {
        return $this->hasMany('\App\Models\AdsPhotos', 'ads_id');
    }

    public function Comments($ads = 0)
    {
        return $this->hasMany('\App\Models\ArticleComments', 'article_id');
    }
    
}
