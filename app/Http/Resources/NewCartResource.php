<?php

namespace App\Http\Resources;

use App\Models\CartItem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class NewCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $select_title = app()->getLocale() == "ar" ? 'title' : 'title_en as title';
        $select_measurement = app()->getLocale() == "ar" ? 'measurement_units.name as measurement_unit' : 'measurement_units.name_en as measurement_unit';

        $cart_items = [];

        $shop_items = CartItem::select('cart_items.id', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price',
            'cart_items.quantity_difference as quantity', 'products.title', 'cart_items.shop_id', 'users.shipment_days', $select_measurement)
            ->where('cart_items.type', 1)
            ->where('cart_items.order_id', $this->order_id)
            ->where('shop_id', $this->shop_id)
            ->where('cart_items.quantity_difference', '!=', 0)
            ->where('cart_items.user_id', $this->user_id)
            ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
            ->selectRaw('(CASE WHEN users.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", users.photo)) END) AS shop_photo')
            ->join('users', 'cart_items.shop_id', 'users.id')
            ->join('products', 'cart_items.item_id', 'products.id')
            ->join('measurement_units', 'measurement_units.id', 'products.measurement_id')

            ->get();

        $cart_items = $shop_items;

        return [
            'shop_id' => $this->shop_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            // 'shipment_price' => $this->shipment_price,
            'shop_name' => $this->username,
            'cart_items' => $cart_items,
        ];
    }
}
