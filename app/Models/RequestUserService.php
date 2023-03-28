<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestUserService extends Model
{
    protected $table = 'request_user_services';

    public function getService() {
        return $this->belongsTo('App\Models\Services', 'service_id', 'id');
    }


}
