<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainCategories extends Model
{

    protected $table = 'main_categories';
    //       public function parent(){
    //          return  $this->belongsTo('\App\Models\Categories','parent_id');
    //       }

    public function subCategories()
    {
        return  $this->hasMany('\App\Models\Categories', 'parent_id', 'id')->where('is_archived', 0);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Products::class, 'category_id', 'id')->where('stop', 0)->where('is_archived', 0)->orderBy('sort', 'asc');
            // ->whereRaw('quantity - min_quantity >= min_warehouse_quantity');
    }
}
