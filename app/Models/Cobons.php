<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cobons extends Model
{
    protected $table = 'cobons';

    public function getOrder()
    {
      return $this->belongsTo('App\Models\Orders', 'order_id', 'id');
    }

    public function cobonCategory()
    {
      return $this->hasMany(CobonsCategories::class, 'cobon_id', 'id');
    }

    public function cobonProvider()
    {
      return $this->hasMany(CobonsProviders::class, 'cobon_id', 'id');
    }

    public function checkCobon()
    {
      return  Orders::where('cobon', $this->code)->where('payment_method', '<>', 0)->where('status', '<>', 5)->count(); 
    }

}