<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleComments extends Model
{
    protected $table = 'article_comments';

    public function getFullName()
    {
      return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }


    public function getArticle()
    {
      return $this->belongsTo('App\Models\Articles', 'article_id', 'id');
    }

}