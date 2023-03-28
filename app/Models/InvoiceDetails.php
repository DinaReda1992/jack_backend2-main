<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetails extends Model
{
    protected $table = 'invoice_details';

    public function getInvoice() {
    	return $this->belongsTo('App\Models\Invoices', 'invoice_id', 'id');
    }

    public function getProduct() {
        return $this->belongsTo('App\Models\Products', 'product_id', 'id');
    }
}
