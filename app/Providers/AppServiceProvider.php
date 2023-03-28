<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Models\Favorite;
use App\Models\Products;
use App\Models\Countries;
use App\Models\ClientTypes;
use App\Models\PageCategory;
use App\Models\MainCategories;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResources;
use App\Services\AuthService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        View::composer(['website.layout'], function ($view) {
            $select_name = app()->getLocale() == "en" ? 'name_en as name' : 'name_ar as name';

            request()->merge(['take' => 6, 'is_offer' => 1]);
            $products = Products::getProducts('web')->get();

            $page_categories = PageCategory::where(function ($query) {
                $query->whereHas('products')->OrWhereHas('category')->OrWhereHas('subcategory');
            })->when($products->count() > 0, function ($query) {
                $query->orWhere('is_offer', 1);
            })->with('products', 'category.products', 'subcategory.products')
                ->select($select_name, 'id', 'is_offer', 'category_id', 'sub_category_id')->get();
            $wishlist_count = 0;
            $balance = 0;
            $cart_count = 0;
            if (auth('client')->user()) {
                $wishlist_count = auth('client')->user()->wishlist->count();
                $balance = auth('client')->user()->balance;
                $cart_count = CartItem::where('type', 1)->where(['status' => 0, 'shipment_id' => 0])->whereHas('product')
                    ->where('user_id', auth('client')->id())->count();
            }
            $countries = Countries::select('id', 'name')->with('getRegions.getStates:id,name,country_id,region_id')
                ->with('getRegions:id,name,country_id')->get();
            $clientTypes = ClientTypes::select('id', 'name')->get();

            $categories = MainCategories::whereHas('subcategories')->with('subcategories')->take(5)->get();
            $statistic_data = compact('wishlist_count', 'balance', 'cart_count');
            $items = CartItem::where('type', 1)->where(['status' => 0, 'shipment_id' => 0])->whereHas('product')->with('product')->where('user_id', auth('client')->id())->get();
            request()->merge(['is_offer' => 1]);
            $product = Products::getProducts('web')->first();
            $random_product  = $product ? json_encode(ProductResources::make($product)) : $product;

            $view->with([
                'statistic_data' => $statistic_data,
                'page_categories' => $page_categories,
                'countries' => $countries,
                'clientTypes' => $clientTypes,
                'categories' => json_encode(CategoryResource::collection($categories)),
                'items' => json_encode(CartItemResource::collection($items)),
                'random_product' => $random_product
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('auth-service', function ($app) {
            return new AuthService();
        });
    }
}
