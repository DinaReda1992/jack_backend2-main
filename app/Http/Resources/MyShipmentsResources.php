<?php

namespace App\Http\Resources;

use App\Models\CartItem;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class MyShipmentsResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $select_title = request()->header('Accept-Language') == "ar" ? 'title' : 'title_en as title';
        $select_measurement = request()->header('Accept-Language') == "ar" ? 'measurement_units.name as measurement_unit' : 'measurement_units.name_en as measurement_unit';

        $cart_items=[];
        if($this->type==2){
            $select_part_name = request()->header('Accept-Language') == "ar" ? 'part_name as title' : 'part_name as title';

            $cart_items=CartItem::select('cart_items.id','cart_items.status', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id', 'cart_items.price',
                'cart_items.quantity', 'pricing_order_parts.'.$select_part_name, 'cart_items.shop_id',$select_measurement)
                ->where('cart_items.shipment_id',$this->id)
                ->selectRaw('(CASE WHEN pricing_order_parts.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", pricing_order_parts.photo)) END) AS photo')
                ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM product_ratings WHERE product_ratings.item_id=pricing_offers.id and product_ratings.type=2 and product_ratings.user_id=cart_items.user_id ) as product_rate')

                ->join('pricing_offers', 'cart_items.item_id', 'pricing_offers.id')
                ->join('pricing_order_parts', 'pricing_order_parts.id', 'pricing_offers.part_id')
                ->join('measurement_units', 'measurement_units.id', 'pricing_order_parts.measurement_id')
                ->get();
        }
        elseif($this->type==1){
            $cart_items=CartItem::select('cart_items.id','cart_items.status', 'cart_items.item_id', 'cart_items.type', 'cart_items.user_id',
                'cart_items.price', 'cart_items.quantity', 'products.' . $select_title, 'cart_items.shop_id',$select_measurement)
                ->where('cart_items.shipment_id',$this->id)
                ->selectRaw('(CASE WHEN products.photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . URL::to('/') . '/uploads/", products.photo)) END) AS photo')
                ->selectRaw('(SELECT ROUND(AVG(rate) ,0) FROM product_ratings WHERE product_ratings.item_id=products.id and product_ratings.type=1 and product_ratings.user_id=cart_items.user_id ) as product_rate')

                ->join('products', 'cart_items.item_id', 'products.id')
                ->join('measurement_units', 'measurement_units.id', 'products.measurement_id')

                ->get();

        }
        return [
            'id'=>$this->id,
            'delivery_date'=>$this->delivery_date,
            'status'=>$this->status,
            'order_id'=>$this->order_id,
            'type'=>$this->type,
            'created_at'=>$this->created_at->format('M d Y'),
            'cart_items'=>$cart_items,
        ];
    }
}
