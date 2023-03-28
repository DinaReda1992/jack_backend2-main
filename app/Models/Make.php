<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Make extends Model
{
protected $table='makes';
public function years(){
    return $this->hasMany('App\Models\MakeYear','make_id');
}
}
