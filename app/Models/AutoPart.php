<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoPart extends Model
{
    protected $table = 'autoparts';
    protected $fillable = [
        'name', 'name_en'
    ];


}
