<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCar extends Model
{
    protected $table = 'user_cars';

    public function make()
    {
        return $this->belongsTo('App\Models\Make', 'make_id', 'id');
    }
    public function year()
    {

        return $this->belongsTo('App\Models\MakeYear', 'year_id', 'id');
    }
    public function model()
    {

        return $this->belongsTo('App\Models\Models', 'model_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
