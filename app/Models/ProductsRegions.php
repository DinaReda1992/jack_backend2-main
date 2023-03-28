<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsRegions extends Model
{
    protected $table = 'product_regions';
    public function getRegion() {
        return $this->belongsTo(Regions::class, 'region_id', 'region_id')->where('is_stop',0);
    }
}
