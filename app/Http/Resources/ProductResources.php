<?php

namespace App\Http\Resources;

use App\Models\CartItem;
use App\Models\Shop_offer;
use App\Repositories\ProductRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResources extends JsonResource
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
     * @return array
     */
    public function toArray($request)
    {
        ini_set('serialize_precision', -1);
             $using = static::$using;
        if ($using && $using['user_id']) {
            $user_id = $using['user_id']->id;
        } else {
            $user_id = auth('client')->user() ? auth('client')->user()->id : 0;
        }
        // $margin = round($this->client_price, 2) - round($this->price, 2);
        // $profit_margin = $margin / round($this->client_price, 2) * 100;
        $client_price = $this->client_price + $this->client_price * 0.15;
        $price = $this->price + $this->price * 0.15;
        $offer_price = 0;
        $offer = @$this->offer_product->offer;
        $offer = Shop_offer::checkAvailability($offer);
        $offer_type_id = 0;
        $offer_price = null;
        $is_offer = 0;
        if ($offer) {
            if ($offer->number_of_users) {
                $is_offer = $offer->number_of_users > $offer->offer_usage_count ? 1 : 0;
                $offer = $is_offer == 1 ? $offer : null;
            } else {
                $is_offer = 1;
            }
            if ($is_offer) {
                if (@$offer->type_id == 1) {
                    $offer_price = floatval($price) - floatval($offer->price_discount + $offer->price_discount * .15);
                    if ($offer_price < 0) {
                        $offer_price = 0;
                        $offer = null;
                    }
                } elseif (@$offer->type_id == 2) {
                    $offer_price = $price - (floatval($price) * floatval($offer->percentage) / 100);
                }
                $offer_type_id = @$offer->type_id;
            }
        }
        // if ($offer && $offer_price) {
        //     $offer_price = $offer_price + ($offer_price * 15 / 100);
        // }
        return [
            'id' => $this->id,
            'title' => app()->getLocale() == 'en' ? $this->title_en : $this->title,
            'description' => app()->getLocale() == 'en' ? $this->description_en : $this->description,
            //            'usage'=>$this->usage,
            'price' => @round($price, 2),
            'client_price' => @round($client_price, 2),
            'has_cover' => $this->has_cover,
            'measurement_unit' => app()->getLocale() == 'en' ? @$this->measurement->name_en : @$this->measurement->name,
            'min_quantity' => $this->min_quantity,
            'quantity' => $this->quantity,
            'category_id' => $this->category_id,
            'delivery_status' => app()->getLocale() == 'en' ? @$this->deliverStatus->name_en : @$this->deliverStatus->name,
            'category_name' => app()->getLocale() == 'en' ? @$this->category->name_en : @$this->category->name,
            'expiry' => $this->expiry,
            'temperature' => $this->temperature,
            'weight' => $this->weight,
            'photo' =>  is_file('uploads/' . $this->photo) ? asset('uploads/') .  '/' . $this->photo : asset('/images/placeholder.png'),
            'thumb' => is_file('uploads/' . $this->thumb) ? asset('uploads/') .  '/' . $this->thumb : asset('/images/placeholder.png'),
            'deliver_status' => $this->deliver_status,
            'photos' => ProductsPhotosResource::collection($this->photos),
            'is_liked' => $user_id ? $this->favorites()->where('user_id', $user_id)->count() : 0,
            'is_fav' => $this->is_fav,
            'is_carted' => $user_id ? $this->cart_items()->where('type', 1)->where('user_id', $user_id)->where('shipment_id', 0)->count() : 0,
            'purchase_count' => $this->cart_items->where('type', 1)->where('shipment_id', '<>', 0)->count() ?: 0,
            'subcategory_name' => $this->subcategory_name ?: '',
            // 'shop' => $this->user,
            // 'related_projects' => $this->related_projects,
            // 'profit_margin' => (float) round($profit_margin, 2),
            'offer_price' => round((float)$offer_price, 2),
            'offer_type' => $offer ? ProductRepository::getOfferType($offer, ($offer->price_discount * 15 / 100)) : '',
            'offer_type_id' => $offer_type_id ?: 0,
            'offer_id' => @$offer ? $offer->id : 0,
            'offer_end_date' => @$offer ? $offer->end_date->copy()->addHours('23')->addMinutes('59')->addSeconds('59')->format('M d ,Y H:i:s') : 0,
            'user_fav_count' => auth()->user() ? auth()->user()->wishlist->count() : 0,
        ];
    }
}
