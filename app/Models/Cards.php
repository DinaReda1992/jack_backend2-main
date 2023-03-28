<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cards extends Model
{
    protected $table = 'cards';

    public function getCategory()
    {
        return $this->belongsTo('App\Models\CardsCategories', 'card_category_id', 'id');
    }


}
