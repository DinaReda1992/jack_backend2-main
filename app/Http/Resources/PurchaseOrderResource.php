<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $select_name = app()->getLocale() == "ar" ? 'name' : 'name_en';

        return [
            'id' => $this->id,
            'status' => $this->status,
            'status_name' => $this->orderStatus->$select_name,
            'color' => $this->orderStatus->color,
            'created_at' => $this->created_at->diffForHumans(),
            'create' => $this->created_at->format('Y-m-d h:i A'),
            'delivery_date' => $this->provider_delivery_date ? $this->provider_delivery_date . '  ' . $this->provider_delivery_time : null,
            'warehouse_date' => $this->warehouse_date,
            'invoice_url' => url('/p') . '/' . $this->code,
            'products_count' => $this->products_count ?: $this->purchase_item->count(),
            'user' => [
                'id' => $this->provider->id,
                'name' => $this->provider->username,
                'photo' => $this->provider->photo ? url('/uploads') . '/' . $this->provider->photo : url('/images/placeholder.png'),
            ],
        ];
    }
}
