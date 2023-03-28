<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingOrderPart extends Model
{
    protected $table = 'pricing_order_parts';

    public function pricing_order()
    {
        return $this->belongsTo('App\Models\PricingOrder', 'order_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo('App\Models\Categories', 'category_id', 'id');
    }
    public function subcategory()
    {
        return $this->belongsTo('App\Models\Subcategories', 'subcategory_id', 'id');
    }
    public function measurement() {

        return $this->belongsTo('App\Models\MeasurementUnit','measurement_id');
    }

//
//    public function part()
//    {
//
//        return $this->belongsTo('App\Models\AutoPart', 'part_id', 'id');
//    }

    public function offers(){

        return $this->hasMany('App\Models\PricingOffer','part_id');
    }

    public function my_offer()
    {

        return $this->hasMany('App\Models\PricingOffer', 'part_id')
            ->where(function ($query) {

                    $query->where('provider_id', auth()->id())->orWhere('provider_id',auth()->user()->main_provider);

            });
    }

    public function admin_offer()
    {

        return $this->hasMany('App\Models\PricingOffer', 'part_id')
            ->where('provider_id',235);
    }
}
