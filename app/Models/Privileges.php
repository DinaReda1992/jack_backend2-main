<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Privileges extends Model
{
    protected $table = 'privileges';
    public function subProgrames()
    {
        return $this->hasMany('App\Models\Privileges', 'parent_id', 'id')->orderBy('orders','asc');
    }
    public function subShowProgrames(){
        if(Auth::User()->privilege_id){
            $pr=\App\Models\SupervisorGroupsPrivileges::where('group_id',Auth::user()->privilege_id)->pluck('privilege_id')->toArray();
            return $this->hasMany('App\Models\Privileges', 'parent_id', 'id')->where('hidden',0)->whereIn("id",$pr);
        }
        return $this->hasMany('App\Models\Privileges', 'parent_id', 'id')->where('hidden',0);
    }

//    public function getConditions(){
//        return $this->hasMany('App\Models\PrivilegesCountConditions', 'privilege_id', 'id');
//    }

//    public function subShowProgrames(){
//        return $this->hasMany('App\Models\Privileges', 'parent_id', 'id')->where('hidden',0);
//    }
}
