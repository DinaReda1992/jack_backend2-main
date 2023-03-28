<?php

namespace App\Models;

use Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Products extends Model
{
    protected $table = 'products';
    protected $appends = ['is_fav'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('user', function (Builder $builder) {
            //            $builder->where('products.is_archived',0);
            //            $builder->where('products.stop',0);
            //            $builder->whereHas('user', function (Builder $query) {
            //                return $query->where('approved', 1)->where('block', 0);
            //            });
            $user_id = 0;
            $region_id = 0;
            $state_id = 0;
            if (request()->route()->getPrefix() == "/admin-panel") {
                $user = auth()->user();
            } else {
                $user = auth('client')->user();
            }
            if ($user) {
                $user_id = $user->user_type_id == 5 ? $user->id : 0;
                $user_address = Addresses::where(['user_id' => $user_id, 'is_home' => 1, 'is_archived' => 0])->first();
                $region_id = @$user_address->id ? $user_address->region_id : 0;
                $state_id = @$user_address->id ? $user_address->state_id : 0;
            } else {
                $user_id = 0;
            }
            $builder->where(function ($q) use ($region_id, $user_id, $state_id) {
                if ($user_id != 0) {
                    $q->where('has_regions1', 0);
                    $q->orWhere(function ($q3) use ($region_id, $state_id) {
                        $q3->whereHas('product_regions', function (Builder $query) use ($region_id) {
                            return $query->where('region_id', $region_id);
                        });
                        if ($state_id != 0) {
                            $q3->whereHas('product_states', function (Builder $query) use ($state_id) {
                                return $query->where('state_id', $state_id);
                            });
                        }
                    });
                }
            });
        });
    }

    public function getIsFavAttribute()
    {
        if (auth('client')->check()) {
            return auth('client')->user()->wishlist()->where('item_id', $this->id)->count();
        }
        return 0;
    }
    public function favorites()
    {
        return $this->hasMany('App\Models\Favorite', 'item_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'provider_id');
    }
    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierData', 'provider_id', 'user_id');
    }
    public function product_regions()
    {
        return $this->belongsToMany(
            Regions::class,
            'product_regions',
            'product_id',
            'region_id'
        );
    }
    public function product_states()
    {
        return $this->belongsToMany(
            States::class,
            'product_regions',
            'product_id',
            'state_id'
        );
    }
    public function photoImage()
    {
        return $this->belongsTo('App\Models\ProductPhotos', 'id', 'product_id');
    }
    public function subcategory()
    {
        return $this->belongsTo('App\Models\Categories', 'subcategory_id');
    }
    public function category()
    {
        return $this->belongsTo('App\Models\MainCategories', 'category_id');
    }
    public function measurement()
    {

        return $this->belongsTo('App\Models\MeasurementUnit', 'measurement_id');
    }

    public function model()
    {

        return $this->belongsTo('App\Models\Models', 'model_id');
    }

    public function getSpecification()
    {
        return $this->hasMany('\App\Models\ProductSpecification', 'product_id');
    }
    public function photos()
    {
        return $this->hasMany('\App\Models\ProductPhotos', 'product_id');
    }

    public function make_years()
    {
        return $this->belongsToMany('\App\Models\MakeYear', 'product_make_year', 'product_id', 'make_year_id');
    }
    public function makeYearsText()
    {
        $txt = '';
        foreach ($this->make_years as $year) {
            $txt .= $year->year . ' - ';
        }
        return $txt;
    }

    public function cart_items()
    {
        return $this->hasMany('App\Models\CartItem', 'item_id', 'id');
    }
    public function purchase_items()
    {
        return $this->hasMany(Purchase_item::class, 'product_id', 'id')->whereNotIn('status', [0, 5, 7]);
    }
    public function deliverStatus()
    {
        return $this->belongsTo('\App\Models\DeliverStatus', 'deliver_status');
    }

    /**
     * The categories that belong to the Products
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Categories::class, 'product_categories', 'product_id', 'category_id');
    }

    public function calculateMinWareHouseQty($mount = 0)
    {
        $afterDeleteCount = $this->quantity - $mount;
        if ($afterDeleteCount > 0) {
            if ($this->min_warehouse_quantity < $afterDeleteCount) {
                if ($this->min_quantity > $mount) {
                    return -2;
                } else {
                    return $mount;
                }
            } else {
                $newMount = $this->quantity - $this->min_warehouse_quantity;
                if ($newMount <= 0) {
                    return -1;
                }
                if ($this->min_quantity > $newMount) {
                    return -1;
                } else {
                    return $newMount;
                }
            }
        } else {
            return -1;
        }
    }

    public function calculateMinWareHouseQtyPanel($mount = 0)
    {
        $afterDeleteCount = $this->quantity - $mount;
        if ($afterDeleteCount > 0) {
            if ($this->min_warehouse_quantity < $afterDeleteCount) {
                if (1 > $mount) {
                    return -2;
                } else {
                    return $mount;
                }
            } else {
                $newMount = $this->quantity - $this->min_warehouse_quantity;
                if ($newMount <= 0) {
                    return -1;
                }
                if (1 > $newMount) {
                    return -2;
                } else {
                    return $newMount;
                }
            }
        } else {
            return -1;
        }
    }

    public function offer_products()
    {
        return $this->hasOne(Shop_offer_item::class, 'product_id', 'id')
            ->where('group', 1)
            ->where('deleted_at', null)
            ->whereHas('offer');
    }

    public function offer_product()
    {
        return $this->hasOne(Shop_offer_item::class, 'product_id', 'id')
            ->where('shop_offer_items.group', 1)
            ->where('shop_offer_items.deleted_at', null)
            ->select(
                'shop_offer_items.shop_product_id',
                'shop_offer_items.id',
                'shop_offer_items.product_id',
                'shop_offer_items.offer_id',
                'shop_offers.type_id',
                'shop_offers.price_discount',
                'shop_offers.is_free',
                'shop_offers.percentage',
                'shop_offers.quantity',
                'shop_offers.id',
                'shop_offers.get_quantity',
                'shop_offers.number_of_users',
                'shop_offers.one_user_use',
                'shop_offer_types.name_en',
                'shop_offers.status',
                'shop_offers.start_date',
                'shop_offers.end_date',
            )
            ->join('shop_offers', 'shop_offers.id', 'shop_offer_items.offer_id')
            ->join('shop_offer_types', 'shop_offer_types.id', 'shop_offers.type_id')
            ->whereHas('offer');
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Shop_offer::class, 'shop_offer_items', 'product_id', 'offer_id')
            ->where('shop_offers.status', 1)
            ->where('shop_offers.deleted_at', null)
            ->whereDate('shop_offers.start_date', '<=', Carbon::today())
            ->whereDate('shop_offers.end_date', '>=', Carbon::today());
        // ->OfferAvailableAuthUser();
        // ->where('shop_offers.number_of_users', '>', 2)
        // ->where('shop_offers.one_user_use', '>', 2);
    }

    public static function getProducts($platform)
    {
        $sortList = [
            'sort' => ['column' => 'sort', 'type' => 'asc'],
            'price_high_to_low' => ['column' => 'price', 'type' => 'desc'],
            'price_low_to_high' => ['column' => 'price', 'type' => 'asc']
        ];
        $sort = 0;
        if (array_key_exists(request()->sort, $sortList)) {
            $sort = $sortList[request()->sort];
        }
        $platform =  $platform ?: 'web';

        if ($platform == 'api') {
            $user = auth('api')->user() ? auth('api')->user()->id : null;
        } else {
            $user = auth('client')->user() ? auth('client')->user()->id : null;
        }
        return Products::where('is_archived', 0)->where('stop', 0)->withCount('cart_items')
            ->when(request('is_offer'), function ($query) {
                $productId = [];
                $offers = Shop_offer::whereHas('offerItems')->whereHas('offerItemsLess')->with('offerItemsLess')
                    ->OfferAvailableAuthUser()
                    ->AvailableAuthUser()->get();
                foreach ($offers as $item) {
                    $offer = Shop_offer::checkAvailability($item);
                    if ($offer) {
                        foreach ($item->offerItemsLess as $value) {
                            if (($item->type_id == 1 && $item->price_discount > $value->product->price)) {
                                # code...
                            } else {
                                $productId[] = $value->product_id;
                            }
                        }
                    }
                }
                $query->whereIn('id', $productId);
            })
            ->with('offer_product')->with('photos')
            // ->whereRaw('quantity - min_quantity >= min_warehouse_quantity')
            ->when(request('category_id')  && !is_array(json_decode(request('category_id'))), function ($query) {
                $query->where('category_id', request('category_id'));
            })
            // ->when(request('category_id') && is_array(json_decode(request('category_id'))), function ($query) {
            //     $query->whereIn('category_id', json_decode(request('category_id')));
            // })
            ->when(request('subcategory_id') && !is_array(json_decode(request('subcategory_id'))), function ($query) {
                $query->where('subcategory_id', request('subcategory_id'));
            })
            ->when(request('subcategory_id') && is_array(json_decode(request('subcategory_id'))), function ($query) {
                $query->whereIn('subcategory_id', json_decode(request('subcategory_id')));
            })
            ->when(request('keyword') != '', function ($query) {
                $query->where(function ($query) {
                    $query
                        // ->where('title', 'REGEXP', self::generate_pattern(request('keyword')))
                        ->orWhere('title_en', 'LIKE', request('keyword') . "%")
                        ->orWhere('title_en', 'LIKE',   request('keyword') . "%")
                        ->orWhere('title', 'LIKE', request('keyword') . "%");
                });
            })->when(request('is_favorite'), function ($query) use ($user) {
                $query->whereIn('id', function ($query) use ($user) {
                    $query->select('item_id')->from(with(new Favorite())->getTable())->where('type', 0)->where('user_id', $user);
                });
            })->when(request('take'), function ($query) {
                $query->take(request('take'));
            })
            ->when($sort, function ($query) use ($sort) {
                $query->orderBy($sort['column'], $sort['type']);
            })
            ->when(request()->sort == 'random', function ($query) {
                $query->inRandomOrder();
            });
        // ->when(request('paginate'), function ($query) {
        //     $query->paginate(request('paginate'));
        // })
        // ->when(!request('paginate'), function ($query) {
        //     $query->get();
        // });
    }


    public static function generate_pattern($search_string)
    {
        $patterns = array("/(ا|إ|أ|آ)/", "/(ه|ة)/", "/(ى|ي|ئ)/");
        $replacements = array("[ا|إ|أ|آ]", "[ه|ة]", "[ى|ي|ئ]");
        return preg_replace($patterns, $replacements, $search_string);
    }
}
