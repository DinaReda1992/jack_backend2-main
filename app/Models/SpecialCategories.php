<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialCategories extends Model
{
    protected $table = 'special_categories';
    public function products()
    {
        return $this->hasMany('App\Models\Products','special_category_id');
    }

}
