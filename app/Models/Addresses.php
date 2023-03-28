<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Addresses extends Model
{
    protected $table = 'addresses';
    protected $guarded=['id'];
//    protected $with=['region','state','user'];
    protected $appends=['lat','lng'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('address_details', function (Builder $builder) {
            $builder->with(['region','state','user']);
        });
    }

    public function getLatAttribute()
    {
        return $this->latitude;
    }
    //withoutGlobalScope
    public function getLngAttribute()
    {
        return $this->longitude;
    }

    public function region()
    {
        return $this->belongsTo(Regions::class,'region_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function state()
    {
        return $this->belongsTo(States::class,'state_id');
    }

}
