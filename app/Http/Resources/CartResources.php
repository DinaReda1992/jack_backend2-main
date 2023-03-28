<?php

namespace App\Http\Resources;

use App\Models\CartItem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class CartResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $select_title = App::getLocale() == "ar" ? 'title' : 'title_en as title';
        $select_measurement = App::getLocale() == "ar" ? 'measurement_units.name as measurement_unit' : 'measurement_units.name_en as measurement_unit';

        $cart_items=[];
//            $pricing_items=CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price', 'cart_items.quantity',
//                'pricing_order_parts.part_name as title', 'cart_items.shop_id','users.shipment_days',$select_measurement)
//                ->where('cart_items.type', 2)
//                ->where('cart_items.order_id', 0)
//                ->where('shop_id',$this->shop_id)
//                ->where('cart_items.user_id', $this->user_id)
//                ->selectRaw('(CASE WHEN pricing_order_parts.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", pricing_order_parts.photo)) END) AS photo')
//                ->selectRaw('(CASE WHEN users.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", users.photo)) END) AS shop_photo')
//                ->join('users', 'cart_items.shop_id', 'users.id')
//
//                ->join('pricing_offers', 'cart_items.item_id', 'pricing_offers.id')
//
//                ->join('pricing_order_parts', 'pricing_order_parts.id', 'pricing_offers.part_id')
//                ->join('measurement_units', 'measurement_units.id', 'pricing_order_parts.measurement_id')
//
//                ->get();
//
            $shop_items=CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price',
                'cart_items.quantity', 'products.title', 'cart_items.shop_id','users.shipment_days',$select_measurement)
                ->where('cart_items.type', 1)
                ->where('cart_items.order_id', 0)
                ->where('shop_id',$this->shop_id)
                ->where('cart_items.user_id', $this->user_id)
                ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                ->selectRaw('(CASE WHEN users.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", users.photo)) END) AS shop_photo')
                ->join('users', 'cart_items.shop_id', 'users.id')
                ->join('products', 'cart_items.item_id', 'products.id')
                ->join('measurement_units', 'measurement_units.id', 'products.measurement_id')

                ->get();

        $cart_items=$shop_items;

        return [
            'shop_id'=>$this->shop_id,
            'user_id'=>$this->user_id,
            'type'=>$this->type,
            'shop_name'=>$this->shop_name,
//            'shipment_price'=>$this->shipment_price,
            'cart_items'=>$cart_items,
        ];
    }
}
