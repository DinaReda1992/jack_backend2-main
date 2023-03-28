<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticlePhotos extends Model
{
    protected $table = 'article_photos';

    public function getArticle() {
    	return $this->belongsTo('App\Models\Articles', 'article_id', 'id');
    }
}
