<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealMenu extends Model
{
    protected $table = 'meal_menus';



    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }



    public function products()
    {
        return $this->belongsToMany('\App\Models\Products', 'products_meal_menus','meal_menus_id','product_id');
    }

}
