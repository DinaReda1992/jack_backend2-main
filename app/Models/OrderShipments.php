<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderShipments extends Model
{
    protected $table = 'order_shipments';
    protected $guarded=['id'];
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function shop()
    {
        return $this->belongsTo('App\Models\User', 'shop_id', 'id');
    }


    public function getOrder()
    {
        return $this->belongsTo('App\Models\Orders', 'order_id', 'id');
    }
    public function cart_items(){

        return $this->hasMany('App\Models\CartItem','shipment_id','id');
    }
    public function orderStatus(){
        return $this->belongsTo('App\Models\OrdersStatus', 'status', 'id');
    }

}
