<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $table = 'favorites';
    protected $guarded=['id'];
    public function user() {
  			return $this->belongsTo('App\Models\User', 'user_id', 'id');
  	}
    public function products() {
        return $this->hasMany('App\Models\Products', 'item_id', 'id')->where('type',0);
    }

    public function shop() {
        return $this->hasMany('App\Models\Products', 'item_id', 'id')->where('type',1);
    }

}
