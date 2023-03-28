<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
  protected $table = 'privileges_groups';

  public function getMenu()
  {
    return $this->belongsTo('App\Models\Menus', 'parent_id', 'id');
  }
public function privileges(){
      return $this->belongsToMany('App\Models\Privileges','privileges_groups_details','privilege_group_id','privilege_id');
}
}
