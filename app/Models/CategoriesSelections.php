<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriesSelections extends Model
{
    protected $table = 'categories_selections';

    public function selection(){
        return  $this->belongsTo('\App\Models\Selections','selection_id');
    }

}
