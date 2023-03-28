<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportPoints extends Model
{
    protected $table = 'report_points';

    public function getType() {
        return $this->belongsTo('App\Models\ReportTypes', 'type', 'id');

    }

}
