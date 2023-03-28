<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    protected $table = 'bank_transfer';
    /**
     * @var \Carbon\Carbon|mixed
     */
    private $created_at;

    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function getOrder()
    {
        return $this->belongsTo('App\Models\Orders', 'order_id', 'id');
    }
    public function getPackage()
    {
        return $this->belongsTo('App\Models\Packages', 'package_id', 'id');
    }
    public function to_bank()
    {
        return $this->belongsTo('App\Models\BankAccounts', 'bank_id', 'id');
    }

}
