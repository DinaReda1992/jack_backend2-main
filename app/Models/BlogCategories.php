<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategories extends Model
{
    public function getSubcategories()
    {
        return $this->hasMany('\App\Models\BlogSubcategories', 'category_id');
    }
}