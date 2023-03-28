<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sliders extends Model
{
    public function main_slider()
    {
        return $this->belongsTo('\App\Models\MainSlider');
    }
}
