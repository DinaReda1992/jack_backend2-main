<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraCategories extends Model
{
    protected $table = 'extra_categories';
    public function extra_items()
    {
        return $this->hasMany('App\Models\ExtraItems','extra_category_id');
    }
}
