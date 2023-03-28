<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prices extends Model
{
    protected $table = 'prices';
    public function getState() {
  			return $this->belongsTo('App\Models\States', 'state_id', 'id');
  	}

    public function getService() {
  			return $this->belongsTo('App\Models\Services', 'service_id', 'id');
  	}

    public function getCurrency() {
        return $this->belongsTo('App\Models\Currencies', 'currency_id', 'id');
    }
}
