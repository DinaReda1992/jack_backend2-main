<?php

namespace App\Http\Resources;

use App\Models\Shop_offer;
use App\Repositories\ProductRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchProductResource extends JsonResource
{
    protected static $using = [];

    public static function using($using = [])
    {
        static::$using = $using;
    }

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

        return [
            'id' => $this->id,
            'title' => app()->getLocale() == 'en' ? $this->title_en : $this->title,
            'description' => app()->getLocale() == 'en' ? $this->description_en : $this->description,
            'price' => @round($price, 2),
            'offer_price' => round((float)$offer_price, 2),
            'offer_type' => $offer ? ProductRepository::getOfferType($offer, ($offer->price_discount * 15 / 100)) : '',
            'offer_type_id' => $offer_type_id ?: 0,
            'offer_id' => @$offer ? $offer->id : 0,
            'photo' =>  is_file('uploads/' . $this->photo) ? asset('uploads/') .  '/' . $this->photo : asset('/images/placeholder.png'),
            'thumb' => is_file('uploads/' . $this->thumb) ? asset('uploads/') .  '/' . $this->thumb : asset('/images/placeholder.png'),
        ];
    }
}
