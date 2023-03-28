<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Selections extends Model
{
       public function options(){
          return  $this->hasMany('\App\Models\SelectionOptions','selection_id')->orderBy('sort','asc');
       }
        public function parent(){
            return  $this->belongsTo('\App\Models\Selections','parent_id');
        }

}
