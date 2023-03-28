<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrders extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
               "id"=>$this->id,
                "provider_id"=>$this->provider_id,
                "final_price"=>$this->final_price,
                "order_price"=>$this->order_price,
                "taxes"=>$this->taxes,
                "delivery_date"=>$this->delivery_date,
                "delivery_time"=>$this->delivery_time,
                "provider_delivery_date"=> $this->provider_delivery_date,
                "provider_delivery_time"=>$this->provider_delivery_time,
                "delivery_method"=>$this->delivery_method,
                "transfer_photo"=>$this->transfer_photo,
                "details"=>$this->details,
                "payment_terms"=>$this->payment_terms,
                "status"=>$this->status,
                "invoice_url"=>$this->invoice_url,
                "purchase_item_count"=>$this->purchase_item_count,
                "purchase_item"=>$this->purchase_item,
                "order_status"=>$this->orderStatusSupplier,
                "payment_method"=>$this->paymentMethod,
                "payment_term"=>$this->paymentTerm,
        ];
    }
}
