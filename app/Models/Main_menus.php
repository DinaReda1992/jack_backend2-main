<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Main_menus extends Model
{
    protected $table = 'main_menus';

    public function getItems()
    {
      return $this->hasMany('App\Models\Menus', 'menu_id', 'id')->orderBy('m_order');
    }

}
