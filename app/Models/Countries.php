<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    public function getStates()
    {
        return $this->hasMany('\App\Models\States', 'country_id');
    }
    public function getRegions()
    {
        return $this->hasMany('\App\Models\Regions', 'country_id')->where('is_archived',0);
    }
}
