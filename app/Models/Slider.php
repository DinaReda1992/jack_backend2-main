<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $table = 'sliders';

    public function main_slider()
    {
        return $this->belongsTo('\App\Models\MainSlider');
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'item_id');
    }

    public function shop()
    {
        return $this->belongsTo(User::class, 'item_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'item_id');
    }

    public function scopeSelectPhoto($query)
    {
        return  $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", photo)) END) AS photo');
    }
}
