<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase_item extends Model
{
    protected $table = 'purchase_items';
    protected $guarded=['id'];
    public function provider()
    {
      return $this->belongsTo('App\Models\User', 'provider_id', 'id');
    }

   /* public function order()
    {
        return $this->belongsTo(Purchase_order::class, 'order_id', 'id');
    }*/
    public function product() {
        return $this->belongsTo('App\Models\Products', 'product_id', 'id');
    }

    public function orderStatus(){
        return $this->belongsTo('App\Models\PurchaseOrderStatus', 'status', 'id');
    }




}
