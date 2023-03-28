<?php

namespace App\Http\Controllers;

use App\Models\Groups;
use App\Models\Privileges;
use App\Models\PrivilegesGroupsDetails;
use App\Models\SupervisorGroup;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function check_settings($name=0)
    {
        if(Auth::user()->user_type_id==1||Auth::user()->user_type_id==3){
            return 1 ;
        }elseif(!empty(Auth::user()->privilege_id)){
            $id = Privileges::where('controller',$name)->first();
           if($id){
               $id=$id->id;
               $privileges = PrivilegesGroupsDetails::where('privilege_group_id',Auth::user()->privilege_id)->pluck('privilege_id')->toArray();
               if(in_array($id,$privileges)){
                   return 1 ;
               }
            }

        }

        die('<h1 align="center">عفوا غير مسموح لك بالدخول لهذه الصلاحية  </h1>');
    }
    protected function check_settings_id($id=0)
    {
        if(Auth::user()->user_type_id==1||Auth::user()->user_type_id==3){
            return 1 ;
        }elseif(!empty(Auth::user()->privilege_id)){
            $privileges = PrivilegesGroupsDetails::where('privilege_group_id',Auth::user()->privilege_id)->pluck('privilege_id')->toArray();
            if(in_array($id,$privileges)){
                return 1 ;
            }
        }

        die('<h1 align="center">عفوا غير مسموح لك بالدخول لهذه الصلاحية  </h1>');
    }
    protected function check_provider_settings_id($id=0)
    {
        if(Auth::user()->user_type_id==3){
            return 1 ;
        }
        elseif(!empty(Auth::user()->privilege_id)){
            $privileges_group = SupervisorGroup::find(Auth::user()->privilege_id)->privileges;
            if(!empty($privileges_group)){
                $privileges = unserialize($privileges_group);
                foreach ($privileges as $privilege) {
                    if($privilege == $id){
                        return 1 ;
                    }
                }
            }
        }

        die('<h1 align="center">عفوا غير مسموح لك بالدخول لهذه الصلاحية  </h1>');
    }
    protected function check_provider_settings($id=0)
    {
        if(Auth::user()->user_type_id==3){
            return 1 ;
        }
        elseif(!empty(Auth::user()->privilege_id)){
            $privileges_group = SupervisorGroup::find(Auth::user()->privilege_id)->privileges;
            if(!empty($privileges_group)){
                $privileges = unserialize($privileges_group);
                foreach ($privileges as $privilege) {
                    if($privilege == $id){
                        return 1 ;
                    }
                }
            }
        }

        die('<h1 align="center">عفوا غير مسموح لك بالدخول لهذه الصلاحية  </h1>');
    }

}
