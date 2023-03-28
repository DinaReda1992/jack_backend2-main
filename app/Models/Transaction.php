<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function getOrder()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }
    public function getBankTransfer()
    {
        return $this->belongsTo(BankTransfer::class, 'transfer_id');
    }

    
}
