<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Menus extends Model
{
    protected $table = 'menus';

    public function getMenu()
    {
      return $this->belongsTo('App\Models\Menus', 'parent_id', 'id');
    }

    public function get_sub_menus()
    {
      return $this->hasMany('App\Models\Menus', 'parent_id', 'id')->orderBy('m_order');
    }
    public function getParentMenu(){
        return $this->get_sub_menus();
    }

}
