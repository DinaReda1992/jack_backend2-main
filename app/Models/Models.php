<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
protected $table='models';
public function year(){
    return $this->belongsTo('App\Models\MakeYear','makeyear_id');
}
}
