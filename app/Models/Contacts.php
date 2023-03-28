<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    protected $table = 'contacts';

    protected $guarded = ['id'];

    public function getContactType()
    {
        return $this->belongsTo(ContactTypes::class, 'message_type_id', 'id');
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
