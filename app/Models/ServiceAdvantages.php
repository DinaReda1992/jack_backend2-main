<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAdvantages extends Model
{
    protected $table = 'service_advantages';


    public function getService()
    {
        return $this->belongsTo('App\Models\Services', 'service_id', 'id');
    }

}
