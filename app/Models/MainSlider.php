<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainSlider extends Model
{
    use HasFactory;
    protected $table = 'main_sliders';
    protected $fillable = ['id' , 'name'];

    public function sliders() {
        return $this->hasMany('\App\Models\Slider','main_slider_id','id');
    }
}
