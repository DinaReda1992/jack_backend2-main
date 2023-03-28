<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{

    protected $table = 'messages';

	public function getRecieverUser() {
		return $this -> belongsTo('App\Models\User', 'reciever_id', 'id');
	}

    public function reservation() {
        return $this -> belongsTo('App\Models\Reservations', 'reservation_id', 'id');
    }

	public function getSenderUser() {
		return $this -> belongsTo('App\Models\User', 'sender_id', 'id');
	}

    public function getTicket() {
        return $this -> belongsTo('App\Models\Tickets', 'ticket_id' , 'id');
    }
    public function ticket() {
        return $this -> belongsTo('App\Models\Tickets', 'ticket_id' , 'id');
    }

	public function hall() {
		return $this -> belongsTo('App\Models\Hall', 'hall_id' , 'id');
	}

    public function getScheduleAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

	public function getAllMessages(Messages $message){
		$sender_id=$message->sender_id;
	    $reciever_id=$message->reciever_id;

		$all_messages=$this->getUserMessages($sender_id,$reciever_id)->merge($this->getUserMessages($reciever_id,$sender_id));
		return $all_messages->sortBy('created_at');
	}

    public function getAllMessagesForProject($sender_id ,$reciever_id,$project_id){

        $all_messages=$this->getUserMessages($sender_id,$reciever_id,$project_id)->merge($this->getUserMessages($reciever_id,$sender_id,$project_id));
        return $all_messages->sortBy('created_at');
    }

	public function getUserMessages($sender_id,$reciever_id,$project_id){
		return $this->where("sender_id",$sender_id)->where("reciever_id",$reciever_id)->where('project_id',$project_id)->get();
	}

    public function getUserMessagesForProject($sender_id,$reciever_id,$project_id){
        return $this->where("sender_id",$sender_id)->where("reciever_id",$reciever_id)->where('project_id',$project_id)->get();
    }

	}
