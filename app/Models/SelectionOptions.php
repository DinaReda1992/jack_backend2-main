<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelectionOptions extends Model
{
    protected $table = 'selection_options';

    public function selection(){
        return  $this->belongsTo('\App\Models\SelectionOptions','parent_id');
    }
}
