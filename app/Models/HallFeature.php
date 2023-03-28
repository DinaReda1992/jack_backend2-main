<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HallFeature extends Model
{

    protected $table = 'hall_features';


       public function feature(){
            return  $this->belongsTo('\App\Models\Feature','feature_id');
       }
    public function hall(){
        return  $this->belongsTo('\App\Models\Hall','hall_id');
    }
}
