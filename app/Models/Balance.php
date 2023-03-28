<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $table = 'balance';

    protected $guarded = ['id'];

    protected static function boot()
    {

        parent::boot();
        static::addGlobalScope('status', function (Builder $builder) {
//            $builder->where('products.is_archived',0);
//            $builder->where('products.stop',0);
            $builder->where('status',1);

        });
    }

    public function getType()
    {
        return $this->belongsTo('App\Models\BalanceTypes', 'balance_type_id', 'id');
    }
    public function reservation()
    {
        return $this->belongsTo('App\Models\Reservations', 'reservation_id', 'id');
    }
    public function getUser()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
