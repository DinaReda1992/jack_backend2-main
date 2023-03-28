<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{

    protected $table = 'halls';

    public function photos()
    {
        return $this->hasMany('\App\Models\HallPhoto', 'hall_id');
    }
    public function onePhoto()
    {
        return $this->hasOne('\App\Models\HallPhoto', 'hall_id');
    }
    public function photos3()
    {
        return $this->hasMany('\App\Models\HallPhoto', 'hall_id')->limit(3);
    }
    public function features()
    {
        return $this->belongsToMany('\App\Models\Feature', 'hall_features');
    }
    public function getCurrency()
    {
        return $this->belongsTo('\App\Models\Currencies', 'currency');
    }
    public function hallfeatures()
    {
        return $this->hasMany('\App\Models\HallFeature', 'hall_id');
    }
    public function hallsFeatures()
    {
        return $this->belongsToMany('\App\Models\Feature', 'hall_features','hall_id','feature_id');
    }
    public function hallTypes()
    {
        return $this->belongsToMany('\App\Models\Categories', 'hall_categories','hall_id','category_id');
    }
    public function provider()
    {
        return $this->belongsTo('\App\Models\User', 'user_id');
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


}
