<?php

namespace App\Models;

use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $table = 'notifications';

    protected $guarded = ['id'];


    public function getSender()
    {
        return $this->belongsTo('App\Models\User', 'sender_id', 'id');
    }

    public function getOrder()
    {
        return $this->belongsTo('App\Models\Orders', 'order_id', 'id');
    }

    public function getReciever()
    {
        return $this->belongsTo('App\Models\User', 'reciever_id', 'id');
    }

    public function getProject()
    {
        return $this->belongsTo('App\Models\Projects', 'project_id', 'id');
    }

    public function getAds()
    {
        return $this->belongsTo('App\Models\Ads', 'ads_id', 'id');
    }
    public function order()
    {
        return $this->belongsTo('App\Models\Orders', 'order_id', 'id');
    }
    public function getArticle()
    {
        return $this->belongsTo('App\Models\Articles', 'ads_id', 'id');
    }

    public function getType()
    {
        return $this->belongsTo('App\Models\NotificationTypes', 'type');
    }

    public function PurchaseOrder()
    {
        return $this->belongsTo(Purchase_order::class, 'order_id', 'id')
            ->withCount('purchase_item as products_count')->with('provider', 'orderStatus');
    }
}
