<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $table = 'invoices';

    public function getDetails()
    {
        return $this->hasMany('\App\Models\InvoiceDetails', 'invoice_id');
    }

    public function getUser()
    {
        return $this->belongsTo('\App\Models\User', 'representative_id');
    }

    public function getOrder()
    {
        return $this->belongsTo('\App\Models\Orders', 'order_id');
    }

    public function getSeller()
    {
        return $this->belongsTo('\App\Models\User', 'seller_id');
    }



    public function product_count($id=0)
    {
        return \App\Models\InvoiceDetails::where("invoice_id",$id)->count();
    }

    
}
