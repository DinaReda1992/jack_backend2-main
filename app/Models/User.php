<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $guarded = ['id'];
    protected $append = ['balance'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getPackage()
    {
        return $this->belongsTo('App\Models\Packages', 'package_id', 'id')->select('id', 'name', 'allowed_ads');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Currencies', 'currency_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Countries', 'country_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\States', 'state_id', 'id');
    }
    public function region()
    {

        return $this->belongsTo('App\Models\Regions', 'region_id', 'id');
    }

    public function devices()
    {
        return $this->hasMany('App\Models\DeviceTokens', 'user_id', 'id');
    }
    public function bank()
    {
        return $this->belongsTo('App\Models\Banks', 'bank_id', 'id');
    }
    public function getStore()
    {
        return $this->belongsTo('App\Models\Stores', 'user_id', 'id')->where('approved', 1);
    }
    public function privilegesGroup()
    {
        return $this->belongsTo('App\Models\Privileges_groups', 'privilege_id', 'id');
    }
    public function privilegesHallGroup()
    {
        return $this->belongsTo('App\Models\SupervisorGroup', 'privilege_id', 'id');
    }
    public function socialProviders()
    {
        return $this->belongsTo('App\Models\socialProvider', 'user_id', 'id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Products', 'provider_id', 'id')->where('is_archived', 0)->where('stop', 0);
    }
    public function provider()
    {
        if ($this->main_provider == 0.0) {
            return $this->belongsTo('App\Models\User', 'id', 'id');
        } else {
            return $this->belongsTo('App\Models\User', 'main_provider', 'id');
        }
    }
    public function cart()
    {
        return $this->hasMany('App\Models\CartItem', 'user_id', 'id')->where('shipment_id', 0);
    }

    public function calcAllBalance()
    {
        return Balance::where('user_id', $this->id)->sum('price');
    }
    public function notifications()
    {
        return $this->hasMany('App\Models\Notification', 'reciever_id', 'id')->where('status', 0)->orderBy('id', 'DESC');
    }
    public function balances()
    {
        return $this->hasMany('App\Models\Balance', 'user_id', 'id')->where('balance_type_id', '!=', 14);
    }

    public function wishlist()
    {
        return $this->belongsToMany(Products::class, 'favorites', 'user_id', 'item_id')
            // ->whereRaw('quantity - min_quantity >= min_warehouse_quantity')
            ->where('stop', 0)->where('is_archived', 0);
    }

    public function cartCount()
    {
        return $this->cart()->sum('quantity');
    }

    public function getBalanceAttribute()
    {
        return @round(\App\Models\Balance::where('user_id', $this->id)->sum('price'), 2);
    }

    public function check_permission($privilege_id)
    {
        if ($this->user_type_id == 3) {
            return true;
        }
        $pr = \App\Models\SupervisorGroupsPrivileges::where('group_id', $this->privilege_id)->pluck('privilege_id')->toArray();

        if (in_array($privilege_id, $pr)) {
            return true;
        } else {
            return false;
        }
    }

    public function supplier()
    {
        return $this->hasOne('App\Models\SupplierData', 'user_id', 'id');
    }

    public function supplier_categories()
    {
        return $this->hasMany('App\Models\SupplierCategory', 'user_id', 'id');
    }

    public function mainSupplier()
    {
        return $this->belongsTo(MainSupplier::class, 'main_supplier_id');
    }

    public function mainSupplierUsers()
    {
        if ($this->main_supplier_id && MainSupplier::find($this->main_supplier_id)) {
            $users = User::where('main_supplier_id', $this->main_supplier_id)->where('block', 0)->where('is_archived', 0)->pluck('id')->toArray();
        } else {
            $users = [$this->id];
        }
        return $users;
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Orders', 'user_id', 'id');
    }
    public function addresses()
    {
        return $this->hasMany('App\Models\Addresses', 'user_id', 'id');
    }
    public function clientType()
    {

        return $this->belongsTo('App\Models\ClientTypes', 'client_type', 'id');
    }
    public function cats()
    {
        return $this->belongsToMany(
            ServicesCategories::class,
            'suppliers_categories',
            'user_id',
            'category_id'
        );
    }
    public function admin_regions()
    {
        return $this->belongsToMany(
            Regions::class,
            'users_regions',
            'user_id',
            'region_id'
        );
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'user_id', 'id');
    }
}
