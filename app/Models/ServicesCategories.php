<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicesCategories extends Model
{
    protected $table = 'services_categories';
public function suppliers(){

    return $this->belongsToMany('\App\Models\User', 'suppliers_categories','user_id','category_id');
}
}
