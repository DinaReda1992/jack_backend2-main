<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealSize extends Model
{
    protected $table = 'meal_sizes';



    public function meal()
    {
        return $this->belongsTo('App\Models\Products', 'meal_id');
    }



    public function products()
    {
        return $this->belongsToMany('\App\Models\Products', 'products_meal_menus','meal_menus_id','product_id');
    }

}
