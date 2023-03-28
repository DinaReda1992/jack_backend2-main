<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';
    protected $guarded = ['id'];




    public function order()
    {
        return $this->belongsTo('App\Models\Orders', 'order_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    
    public function product()
    {
        return $this->belongsTo('App\Models\Products', 'item_id', 'id');
    }

    public function itemProduct()
    {
        if ($this->type == 1) {
            return $this->belongsTo('App\Models\Products', 'item_id', 'id');
        } else {

            return $this->belongsTo('App\Models\PricingOffer', 'item_id', 'id');
        }
    }

    public function cart_items()
    {
        return $this->hasMany('App\Models\CartItem', 'shop_id', 'shop_id');
    }

    public function cart()
    {
        return $this->hasMany('App\Models\CartItem', 'user_id', 'user_id')->where('order_id', 0);
    }

    public function offer()
    {
        return $this->belongsTo(Shop_offer::class, 'offer_id', 'id')->withoutTrashed();
    }

}
