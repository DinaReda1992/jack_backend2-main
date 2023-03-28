<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Main_slider extends Model
{
    public function Sliders(){
        return $this->hasMany('\App\Models\Sliders','main_id','id');
    }

}
