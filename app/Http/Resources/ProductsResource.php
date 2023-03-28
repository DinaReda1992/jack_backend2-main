<?php

namespace App\Http\Resources;

use danielme85\CConverter\Currency;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
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
        //        $valueNOK = Currency::conv($from = 'EGP', $to = 'SAR', $value = 1000, $decimals = 5);
        //return $valueNOK;
        //        $discount_rate=0;
        //        if($this->price &&$this->price_after_discount){
        //            $discount_rate=100-(($this->price_after_discount/$this->price)*100);
        //        }
        //        'products.expiry',
        //                'products.temperature','products.weight','products.client_price','products.has_cover',$select_deliver_status
        ini_set('serialize_precision', -1);
        $using = $this->merge(static::$using);
        $user_id = $using->data['user_id'] ?: 0;
        $margin = round($this->client_price, 2) - round($this->price, 2);
        $profit_margin = $margin / round($this->price, 2) * 100;

        return [

            'id' => $this->id,
            'title' => app()->getLocale()=='en'?$this->title_en:$this->title,
            'description' => app()->getLocale()=='en'?$this->description_en:$this->description,
            //            'usage'=>$this->usage,
            'price' => @round($this->price, 2),
            'client_price' => @round($this->client_price, 2),
            'has_cover' => $this->has_cover,
            'measurement_unit' => $this->measurement_unit,
            'min_quantity' => $this->min_quantity,
            'quantity' => $this->quantity,

            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'expiry' => $this->expiry,
            'temperature' => $this->temperature,
            'weight' => $this->weight,
            'photo' =>  is_file('uploads/' . $this->photo) ? asset('uploads/') .  '/' . $this->photo : asset('/images/placeholder.png'),
            'thumb' => is_file('uploads/' . $this->thumb) ? asset('uploads/') .  '/' . $this->thumb : asset('/images/placeholder.png'),
            'deliver_status' => $this->deliver_status,
            //            'shipment_price'=>$this->shipment_price?:0,
            'photos' => ProductsPhotosResource::collection($this->photos),
            'is_liked' => $user_id ? $this->favorites()->where('user_id', $user_id)->count() : 0,
            'is_carted' => $user_id ? $this->cart_items()->where('type', 1)->where('user_id', $user_id)->where('shipment_id', 0)->count() : 0,
            'purchase_count' => $this->cart_items()->where('type', 1)->where('shipment_id', '<>', 0)->count() ?: 0,
            'subcategory_name' => $this->subcategory_name ?: '',
            'shop_name' => $this->shop_name ?: '',
            'profit_margin' => (float) round($profit_margin, 2),

        ];
    }
}
