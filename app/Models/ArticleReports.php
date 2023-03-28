<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleReports extends Model
{
    protected $table = 'article_reports';


    public function getarticle() {
    	return $this->belongsTo('App\Models\Articles', 'article_id', 'id');
    }

    public function getFullName()
    {
      return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
