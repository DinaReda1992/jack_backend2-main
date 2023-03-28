<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Products;
use App\Models\Settings;
use App\Models\Shop_product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OffersExport implements FromView
{

    public function view(): View
    {
        $provider_id = \auth()->user()->provider->id;

        $offers = Products::select(
            'products.id'
            , 'products.user_id', 'products.title', 'products.title_en'
            , 'products.client_price', 'products.min_quantity'
            , 'products.quantity', 'products.category_id'
            , 'products.has_tax', 'products.video_type', 'products.video')
            ->with(['product_offer' => function ($query) {
                $query->select('shop_offer_items.shop_product_id', 'shop_offers.user_id', 'shop_offer_items.id', 'shop_offer_items.product_id', 'shop_offer_items.offer_id',
                    'shop_offers.type_id', 'shop_offers.price_discount', 'shop_offers.is_free', 'shop_offers.percentage',
                    'shop_offers.quantity', 'shop_offers.get_quantity', 'shop_offers.number_of_users', 'shop_offers.one_user_use', 'shop_offer_types.name_en')
                    ->selectRaw('(SELECT count(*) FROM shop_offer_items WHERE shop_offer_items.product_id=products.id AND shop_offer_items.user_id=products.user_id) as is_offer')
                    ->join('shop_offers', 'shop_offers.id', 'shop_offer_items.offer_id')
                    ->join('shop_offer_types', 'shop_offer_types.id', 'shop_offers.type_id')
                    ->join('products', 'products.id', 'shop_offer_items.product_id')
                    ->where('shop_offer_items.group', 1)->where('shop_offers.status', 1)
                    ->where('shop_offers.deleted_at', null)
                    ->whereDate('shop_offers.start_date', '<=', Carbon::today())
                    ->whereDate('shop_offers.end_date', '>=', Carbon::today());
            }, 'photos' => function ($query) {
                $query->select('id', 'product_id')
                    ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", photo)) as photo')
                    ->selectRaw('(CONCAT ("' . url('/') . '/uploads/thumbs/", thumb)) as thumb');;
            }])
            ->join('products', 'products.id', 'products.product_id')
            ->join('users', 'products.user_id', 'users.id')
            ->join('user_data', 'user_data.user_id', 'users.id')
            ->leftJoin('shop_offer_items', 'shop_offer_items.shop_product_id', 'products.id')
            ->leftJoin('shop_offers', 'shop_offers.id', 'shop_offer_items.offer_id')
            ->selectRaw('(CONCAT ("' . url('/') . '/uploads/", products.photo)) as photo')
            ->selectRaw('(SELECT IFNULL(ROUND(AVG(rate) ,0),0) FROM product_ratings WHERE product_ratings.item_id=products.id  and product_ratings.type=1 ) as product_rate')
            ->where(function ($query) {
                $query->where('shop_offer_items.group', 1)
                    ->where('shop_offers.status', 1)
                    ->whereDate('shop_offers.start_date', '<=', Carbon::today())
                    ->whereDate('shop_offers.end_date', '>=', Carbon::today())
                    ->where('shop_offers.deleted_at', null)
                    ->where('shop_offer_items.deleted_at', null);
            })
            ->where('products.photo', '<>', '')
            ->where('products.is_archived',0)
            // ->where('products.user_id', $provider_id)
            ->where('users.block', 0)
            ->where('is_gift', 0)
            // ->where('user_data.stop', 0)//
            // ->groupBy('products.id')
            ->orderBy('id', 'DESC')
            ->get();

        $settings = Settings::where('option_name', 'tax_fees')->first();

        return view('admin.offers.excel', [
            'products' => $offers,
            'tax' => @$settings->value
        ]);
    }
}
