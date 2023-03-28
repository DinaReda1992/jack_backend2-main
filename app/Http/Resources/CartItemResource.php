<?php

namespace App\Http\Resources;

use App\Models\Shop_offer;
use App\Models\Shop_offer_item;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    protected static $using = [];

    public static function using($using = [])
    {
        static::$using = $using;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        ini_set('serialize_precision', -1);
        $using = $this->merge(static::$using);
        $tax = @$using->data['tax'] ?: 15;
        $cobon_discount = 0;

        $summary_sub_total = $this->price * $this->quantity;
        $priceWithVat = $this->price + ($this->price * $tax / 100);
        $price_vat = (($this->price * $this->quantity) - ($this->discount_price)) * $tax / 100;

        $offer_price = 0;
        $offer = $this->offer;
        $offer = Shop_offer::checkAvailability($offer);
        if ($offer) {
            $offerItem = Shop_offer_item::withoutTrashed()->where(['offer_id' => $offer->id, 'shop_product_id' => $this->item_id])->first();
            if ($offerItem) {
                if (@$offer->type_id == 1) {
                    $offer_price = floatval($this->price) - floatval($offer->price_discount);
                    if ($offer_price < 0) {
                        $offer_price = 0;
                        $offer = null;
                    }
                } elseif (@$offer->type_id == 2) {
                    $offer_price = $this->price - (floatval($this->price) * floatval($offer->percentage) / 100);
                }
            }
        }
        $offer_price += ($offer_price * $tax / 100);
        if ($offer && $offer->type_id == 3) {
            $item_total = ($this->price * $this->quantity) - $this->discount_price + $price_vat;
        } else {
            $item_total = (($offer_price > 0 ? $offer_price : $priceWithVat) * $this->quantity);
        }
        ProductResources::using(['user_id' => auth('api')->user()]);
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'user_id' => $this->user_id,
            'order_id' => $this->order_id,
            "price" => round($priceWithVat, 2),
            "price_vat" => round($price_vat, 2),
            "offer_price" => round((float)$offer_price, 2),
            "discount_price" => floatval($this->discount_price) + floatval($this->cobon_discount),
            "offer_id" => @$offer->id,
            'summary_sub_total' =>  round((float)$summary_sub_total, 2),
            'cobon_discount' => $this->cobon_discount,
            "shop_id" => 0,
            "item_total" => round((float)$item_total, 2),
            "item_id" => $this->item_id,
            "discount_price" => $this->discount_price,
            'product' => ProductResources::make($this->product),
        ];
    }
}
