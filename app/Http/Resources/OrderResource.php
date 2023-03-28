<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        ini_set('serialize_precision', -1);
        $select_status_1 = App::getLocale() == "ar" ? 'قيد المراجعة' : 'Under review';
        $select_status_2 = App::getLocale() == "ar" ? 'مكتمل' : 'Completed';
        $select_status_3 = App::getLocale() == "ar" ? 'تم الشحن' : 'Charged';
        $select_status = $this->status <= 7 ? $select_status_1 : $select_status_2;
        $name = App::getLocale() == "ar" ? 'name' : 'name_en';
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if ($user->user_type_id == 2 && (in_array($this->status, [4, 6, 7]) || $this->status > 7)) {
                $status = $select_status_3;
                $color = '#04d651';
            } else {
                $status = $this->orderStatus->$name ?: $select_status;
                $color = $this->status > 7 ? '#04d651' : $this->orderStatus->color;
            }
        } catch (\Throwable $th) {
            $status =  $this->orderStatus->$name ?: $select_status;
            $color = $this->status > 7 ? '#04d651' : $this->orderStatus->color;
        }
        return [
            'id' => $this->id,
            'status' => $this->status > 7 ? 7 : $this->status,
            'status_name' => $status,
            'color' => $color,
            'created_at' => $this->created_at->diffForHumans(),
            'create' => $this->created_at->format('Y-m-d h:i A'),
            'delivery_date' => $this->delivery_date,
            'warehouse_date' => Carbon::create(@$this->warehouse_date)->format('Y-m-d'),
            'download_url' => $this->download_url == null ? url('/i') . '/' . $this->short_code : $this->download_url,
            'products_count' => $this->products_count ?: $this->cart_items->count(),
            'final_price' => round($this->final_price, 2),
            'discounts' => round($this->discounts, 2),
            'coupon_discount' => round($this->cobon_discount, 2),
            'order_price' => round($this->order_price, 2),
            'vat' => round($this->taxes, 2),
            'payment_method' => $this->payment_method,
            'payment_method_name' => $this->paymentMethod->$name,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->username,
                'photo' => is_file('uploads/' . $this->user->photo) ? asset('uploads/') . '/' . $this->user->photo : asset('/images/placeholder.png'),
            ],
        ];
    }
}
