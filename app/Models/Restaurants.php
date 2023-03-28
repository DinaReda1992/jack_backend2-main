<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurants extends Model
{

    protected $table = 'restaurants';

    public function provider()
    {
        return $this->belongsTo('\App\Models\User', 'user_id');
    }

    public function category()
    {
        return $this->belongsTo('\App\Models\Categories', 'category_id');
    }
    public function getRates(){
        return $this -> hasMany('App\Models\Rating', 'hall_id', 'id');
    }
    public function ratings()
    {
        return $this->belongsToMany('\App\Models\Rating', 'ratings','hall_id','user_id');
    }

    public function getAvgRates(){
        $count=$this->getRates->count();
        $sum=$this->getRates()->sum('rate');
        if($sum==0)
            return 0;
        return  ($this->getRates()->sum('rate'))/$count;
    }
    public function mealMenu()
    {
        return $this->belongsTo('\App\Models\MealMenu', 'meal_menu_id');
    }

    public function categories()
    {
        return $this->belongsToMany('\App\Models\RestaurantCategories', 'restaurant_categories','restaurant_id');
    }
    public function  state()
    {

        return $this->belongsTo('\App\Models\States', 'state_id');
    }


}
