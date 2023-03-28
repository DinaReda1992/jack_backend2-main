<?php

namespace App\Http\Resources;

use App\Models\Notification;
use App\Models\Orders;
use App\Models\Purchase_order;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $array = [
            'id' => $this->id,
            'username' => $this->username,
            'phonecode' => $this->phonecode,
            'phone' => $this->phone,
            'email' => $this->email,
            'photo' =>  is_file('uploads/' . $this->photo) ? asset('uploads/') .  '/' . $this->photo : asset('/images/placeholder.png'),
            'lang' => $this->lang,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'region_id' => $this->region_id,
            'region' => app()->getLocale() == 'ar' ? @$this->region->name : @$this->region->name_en,
            'activate' => $this->activate,
            'approved' => $this->approved,
            'cancel_reason' => $this->cancel_reason ?: '',
            'city' => app()->getLocale() == 'ar' ? @$this->state->name : @$this->state->name_en,
            'city_id' => $this->state_id,
            'is_driver' => $this->user_type_id == 6 ? 1 : 0,
            'user_type_id' => $this->user_type_id,
            'notification' => $this->notification,
            'notification_count' => Notification::where('reciever_id', $this->id)->where('status', 0)->orderBy('id', 'DESC')->count(),
            'token' => $this->token,
        ];
        $array2 = [];
        if ($this->user_type_id == 6) {
            $orders_count = Orders::where('driver_id', $this->id)->whereDate('delivery_date', now())->count();
            $array2 = ['orders_count' => $orders_count];
        }
        return $array + $array2;
    }
}
