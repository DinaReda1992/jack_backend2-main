<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    public function products()
    {
        return  $this->hasMany(Products::class, 'subcategory_id', 'id')->where('stop', 0)->where('is_archived', 0)->orderBy('sort', 'asc');
        // ->whereRaw('quantity - min_quantity >= min_warehouse_quantity');
    }

    public function getProducts()
    {
        return  $this->hasMany(Products::class, 'category_id')->limit(5);
    }

    public function subs()
    {
        return  $this->hasMany('\App\Models\Categories', 'parent_id')->select('id', 'name');
    }
    public function selections()
    {
        return  $this->hasMany('\App\Models\CategoriesSelections', 'category_id');
    }
    public function subCategories()
    {
        return  $this->hasMany('\App\Models\Subcategories', 'category_id');
    }

    public function category()
    {
        return $this->belongsTo(MainCategories::class, 'parent_id');
    }
}
