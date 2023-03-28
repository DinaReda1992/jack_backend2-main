<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogSubcategories extends Model
{
	protected $table = 'blog_subcategories';
	
	public function getCategory() {
			return $this->belongsTo('App\Models\BlogCategories', 'category_id', 'id');
	}

    public function getArticles()
    {
        return $this->hasMany('\App\Models\Articles', 'sub_category_id');
    }
}