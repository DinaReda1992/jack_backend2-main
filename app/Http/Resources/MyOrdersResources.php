<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class MyOrdersResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        ini_set('serialize_precision', -1);
        $select_status_1 = App::getLocale() == "ar" ? 'قيد المراجعة' : 'Under review';
        $select_status_2 = App::getLocale() == "ar" ? 'مكتمل' : 'Completed';
        $select_status = $this->status <= 7 ? $select_status_1 : $select_status_2;
        $name = App::getLocale() == "ar" ? 'name' : 'name_en';
        $status = $this->orderStatus->$name ?: $select_status;
        $color = $this->status > 7 ? '#04d651' : $this->orderStatus->color;
        $final_price = $this->final_price;
        $remaining_money = 0;
        $payed_money = 0;
        if ($this->balance != null) {
            $remaining_money = round(($final_price + $this->balance->price), 2);
            $payed_money = ($this->balance->price * -1);
            if ($this->transaction != null) {
                $payed_money = $payed_money - $this->price;
            }
        } else {
            $remaining_money = $this->final_price;
        }
        return [
            'id' => $this->id,
            'marketed_date' => Carbon::create(@$this->marketed_date)->format('Y-m-d h:i A'),
            'created_at' => Carbon::create(@$this->marketed_date)->diffForHumans(),
            'status' => $this->status > 7 ? 7 : $this->status,
            'status_name' => $status,
            'color' => $color,
            'products_count' => $this->products_count,
            'payed_money' => $payed_money,
            'remaining_money' =>  $this->payment_method == 5 ?  $remaining_money : 0,
            'bank_account' => @$this->transfer_photo ?: @$this->transferParentPhoto,
            'download_url' => $this->download_url == null ? url('/i') . '/' . $this->short_code : $this->download_url,
            'products_count' => $this->products_count ?: $this->cart_items->count(),
            'final_price' => round($this->final_price, 2),
            'discounts' => round($this->discounts, 2),
            'coupon_discount' => round($this->cobon_discount, 2),
            'order_price' => round($this->order_price, 2),
            'vat' => round($this->taxes, 2),
            'payment_method' => $this->payment_method,
            'payment_method_name' => @$this->paymentMethod->$name,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->username,
                'photo' => is_file('uploads/' . $this->user->photo) ? asset('uploads/') . '/' . $this->user->photo : asset('/images/placeholder.png'),
            ],
            // 'has_second_order' => $this->is_edit,
            // 'has_parent_order' => $this->has_parent_order ? 1 : 0,
        ];
    }
}
