<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MakeYear extends Model
{
protected $table='make_years';
public function models(){
    return $this->hasMany('App\Models\Models','makeyear_id');
}
public function make(){
    return $this->belongsTo('App\Models\Make','make_id');
}
}
