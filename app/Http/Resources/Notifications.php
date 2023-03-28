<?php

namespace App\Http\Resources;

use App\Models\Settings;
use Illuminate\Http\Resources\Json\JsonResource;

class Notifications extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'getSender'=> [
                'username' => $this->sender_id == 1 ?  'مدير التطبيق' :  @$this->getSender->username,
                'photo' => $this->sender_id == 1 ? url('/')."/images/" .Settings::find(1)->value : url('/uploads')."/" .$this->getSender->photo,
            ],
            'order_id' => $this->order_id,
            'type'=>$this->type,
            'message' => $this->message,
            'created_at'=> $this->created_at->diffForHumans(),
            'order'=>@new MyOrdersResources($this->order)
        ];
    }
}
