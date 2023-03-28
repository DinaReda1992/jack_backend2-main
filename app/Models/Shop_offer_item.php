<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop_offer_item extends Model
{
    use SoftDeletes;

    protected $table = 'shop_offer_items';
    protected $dates = ['start_date', 'end_date'];


    public function offer()
    {
        // if (@$this->lastOffer && (@$this->lastOffer ? $this->lastOffer->offerAllUser->count() < $this->lastOffer->number_of_users : 0) && (@$this->lastOffer ? $this->lastOffer->offerAllAuthUser->count() < $this->lastOffer->one_user_use : 0)) {
        //     Log::info('test');
        //     return $this->belongsTo(Shop_offer::class, 'offer_id', 'id')
        //         ->where('status', 1)
        //         ->where('deleted_at', null)
        //         ->whereDate('start_date', '<=', Carbon::today())
        //         ->whereDate('end_date', '>=', Carbon::today())
        //         ->where('number_of_users', '>', @$this->lastOffer ? $this->lastOffer->offerAllUser->count() : 0)
        //         ->where('one_user_use', '>', @$this->lastOffer ? $this->lastOffer->offerAllAuthUser->count() : 0);
        // }
        $user_id = 0;
        if (auth('client')->user()) {
            $user_id = auth('client')->user()->id;
        } elseif (auth('api')->user()) {
            $user_id = auth('api')->user()->id;
        }
        if (@$this->lastOffer && @$this->lastOffer->users->count() > 0 && !in_array($user_id, @$this->lastOffer->users()->pluck('user_id')->toArray())) {
            return $this->belongsTo(Shop_offer::class, 'offer_id', 'id')
            ->where('status', 1)
            ->where('deleted_at', null)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today());

        }
        return $this->belongsTo(Shop_offer::class, 'offer_id', 'id')
            ->where('status', 1)
            ->where('deleted_at', null)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today());
        // ->where('number_of_users', '>', @$this->lastOffer ? $this->lastOffer->offerAllUser->count() : 0)
        // ->where('one_user_use', '>', @$this->lastOffer ? $this->lastOffer->offerAllAuthUser->count() : 0);
    }

    public function lastOffer()
    {
        return $this->belongsTo(Shop_offer::class, 'offer_id', 'id')
            ->where('status', 1)
            ->where('deleted_at', null)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today());
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function cart_items()
    {
        return $this->hasMany(CartItem::class, 'item_id', 'shop_product_id');
    }
}
