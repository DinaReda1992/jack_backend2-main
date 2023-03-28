<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table = 'payments';

	public function getPaymentMethod() {
			return $this->belongsTo('App\Models\PaymentMethods', 'payment_method_id', 'id');
	}

    public function getUser() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }


}
