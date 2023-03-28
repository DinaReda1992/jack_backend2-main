<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportPhotos extends Model
{
    protected $table = 'report_photos';

    public function getReport() {
    	return $this->belongsTo('App\Models\Reports', 'report_id', 'id');
    }
}
