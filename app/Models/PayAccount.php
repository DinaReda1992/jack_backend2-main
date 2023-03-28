<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayAccount extends Model
{
    protected $table = 'pay_account';

    public function getUser() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function getPackage() {
        return $this->belongsTo('App\Models\Packages', 'package_id', 'id');
    }

}
