<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shop_offer extends Model
{
    use SoftDeletes;
    protected $appends = ['offer_type'];
    protected $guarded = ['id'];
    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];
    public function items()
    {
        return $this->hasMany(Shop_offer_item::class, 'offer_id')->withoutTrashed();
    }

    public function offerItems()
    {
        return $this->hasMany(Shop_offer_item::class, 'offer_id')
            ->where('group', 1)
            ->where('deleted_at', null);
    }

    public function offerItemsLess()
    {
        return $this->hasMany(Shop_offer_item::class, 'offer_id')
            ->where('group', 1)
            ->where('deleted_at', null)
            ->whereHas('product', function ($query) {
                if ($this->type_id == 1) {
                    $query->where('price', '>=', $this->price_discount);
                }
            });
    }

    public function get_items()
    {
        return $this->items()->where('group', 2);
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function company()
    {
        return $this->belongsTo(UserData::class, 'company_id', 'user_id');
    }
    public function shop()
    {
        return $this->belongsTo(UserData::class, 'user_id', 'user_id');
    }
    public function invitations()
    {
        return $this->hasMany(Offer_invitation::class, 'offer_id');
    }
    public function getOfferTypeAttribute()
    {
        $lang = App::getLocale();
        $message = '';
        if ($this->type_id == 1) {
            $taxs = Settings::find(38)->value;
            $price = round($this->price_discount + ($this->price_discount * $taxs / 100), 2);
            //            $price=$this->price_discount;
            $message = ' خصم ';
            $message .= $price;
            $message .= ' ريال ';
            if ($lang == 'en') {
                $message = 'save ' . $price . ' SAR';
            }
        } elseif ($this->type_id == 2) {
            $message = ' %' . ' وفر' . ' ' . $this->percentage;
            if ($lang == 'en') {
                $message = 'save ' . $this->percentage . ' %';
            }
        } elseif ($this->type_id == 3) {

            if ($this->is_free == 1) {
                $message = ' احصل علي ';
                $message .= ($this->quantity + $this->get_quantity) . ' ';
                $message .= ' بقيمة ';
                $message .= $this->quantity;
                if ($lang == 'en') {
                    $message = 'Get ' . ($this->quantity + $this->get_quantity) . ' SAR';
                }
            } else {
                if ($this->percentage == 100) {
                    $message = ' احصل علي ';
                    $message .= $this->quantity + $this->get_quantity . ' ';
                    $message .= ' بقيمة ';
                    $message .= $this->quantity;
                    if ($lang == 'en') {
                        $message = 'Get ' . ($this->quantity + $this->get_quantity) . ' for ' . $this->quantity;
                    }
                } else {
                    if ($this->quantity == 1) {
                        $message = $this->percentage . ' %';
                        $message .= ' علي الحبة الثانية ';
                        if ($lang == 'en') {
                            $message = $this->percentage . ' % on 2nd';
                        }
                    } else {
                        $message = ' عرض خاص ';
                        if ($lang == 'en') {
                            $message = 'Special offer';
                        }
                    }
                }
            }
        } elseif ($this->type_id == 4) {
            $message = 'هدية بعد الحبة ';
            $message .= $this->quantity;
            if ($lang == 'en') {
                $message = 'Buy ' . $this->quantity . ' + Gift';
            }
        } elseif ($this->type_id == 5) {
            $message = 'شحن مجاني ';
            if ($lang == 'en') {
                $message = 'Free shipping';
            }
        }
        return $message;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'offer_users', 'offer_id', 'user_id');
    }

    public function cartItem(): HasMany
    {
        return $this->hasMany(CartItem::class, 'offer_id');
    }

    public function offerAllUser(): HasMany
    {
        $count = $this->hasMany(CartItem::class, 'offer_id')->where('shipment_id', '<>', 0)->count();
        if ($this->number_of_users && $this->number_of_users > $count) {
            return $this->hasMany(CartItem::class, 'offer_id')->where('shipment_id', '<>', 0);
        }
        return $this->hasMany(CartItem::class, 'offer_id')->where('shipment_id', '<>', 0);
    }

    public function offerAllAuthUser(): HasMany
    {
        $user_id = 0;
        if (auth('client')->user()) {
            $user_id = auth('client')->user()->id;
        } elseif (auth('api')->user()) {
            $user_id = auth('api')->user()->id;
        }
        return $this->hasMany(CartItem::class, 'offer_id')->where('shipment_id', '<>', 0)->where('user_id', $user_id);
    }

    public function ScopeOfferAvailableAuthUser($query)
    {
        $allOfferUses = $this->offerAllUser->count();
        $userUses = $this->offerAllAuthUser->count();
        $query->when($this->number_of_users, function ($query) use ($allOfferUses) {
            $query->where('shop_offers.number_of_users', '>', $allOfferUses);
        })
            ->when($this->one_user_use, function ($query) use ($userUses) {
                $query->where('shop_offers.one_user_use', '>', $userUses);
            })
            ->where('shop_offers.status', 1)
            ->where('shop_offers.deleted_at', null)
            ->whereDate('shop_offers.start_date', '<=', Carbon::today())
            ->whereDate('shop_offers.end_date', '>=', Carbon::today());
    }


    public function ScopeAvailableAuthUser($query)
    {
        $user_id = 0;
        if (auth('client')->user()) {
            $user_id = auth('client')->user()->id;
        } elseif (auth('api')->user()) {
            $user_id = auth('api')->user()->id;
        }
        if ($this->users->count() > 0 && !in_array($user_id, $this->users()->pluck('user_id')->toArray())) {
            return null;
        }
        $query->where(function ($query) use ($user_id) {
            $query->doesnthave('users')->OrwhereHas('users', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
        });
    }


    public static function checkAvailability($query)
    {
        $user_id = 0;
        if (auth('client')->user()) {
            $user_id = auth('client')->user()->id;
        } elseif (auth('api')->user()) {
            $user_id = auth('api')->user()->id;
        }
        if (!$query) {
            return null;
        }
        if ($query->users->count() > 0 && !in_array($user_id, $query->users()->pluck('user_id')->toArray())) {
            return null;
        }
        $allOfferUses = CartItem::where('offer_id', $query->id)->where('shipment_id', '<>', 0)->count();
        $userUses = CartItem::where('offer_id', $query->id)->where('shipment_id', '<>', 0)->where('user_id', $user_id)->count();
        $result = $query->where('status', 1)
            ->where('deleted_at', null)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->when($query->number_of_users, function ($query) use ($allOfferUses) {
                $query->where('number_of_users', '>', $allOfferUses);
            })
            ->when($query->one_user_use, function ($query) use ($userUses) {
                $query->where('one_user_use', '>', $userUses);
            })
            ->where('id', $query->id)
            ->first();
        return $result;
    }
}
