<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResources extends JsonResource
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
            'id'=>$this->id,
            'message'=>$this->message,
            'sender_id'=>$this->sender_id,
            'reciever_id'=>$this->reciever_id,
            'hall_id'=>$this->hall_id,
            'created_at'=>$this->created_at->diffForHumans(),
            'get_sender'=> $this->getSenderUser,
            'get_receiver'=> $this->getRecieverUser,
            'hall'=> [
                'id'=>@$this->hall_id,
                'title'=>@$this->hall->title,
                'photo'=>$this->hall->onePhoto ? url('/')."/uploads/".@$this->hall->onePhoto->photo : url('/')."/images/placeholder.png",
            ]
        ];
    }
}
