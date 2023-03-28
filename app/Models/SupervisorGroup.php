<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorGroup extends Model
{
  protected $table = 'supervisor_groups';

    public function getPrivileges(){
        return $this->belongsToMany('\App\Models\Privileges','supervisor_groups_privileges','group_id','privilege_id');
    }

}
