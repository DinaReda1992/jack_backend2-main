<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestProvider extends Model
{
    protected $table = 'request_provider';
    protected $guarded=['id'];
    public function getUser() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function employee() {
        return $this->belongsTo('App\Models\User', 'employee_id', 'id');
    }

    public function country() {
        return $this->belongsTo('App\Models\Countries', 'country_id', 'id');
    }

    public function state() {
        return $this->belongsTo('App\Models\States', 'state_id', 'id');
    }
    public function region() {

        return $this->belongsTo('App\Models\Regions', 'region_id', 'id');
    }
}
