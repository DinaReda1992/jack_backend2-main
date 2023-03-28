<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardsCategories extends Model
{
    protected $table = 'cards_categories';

    public function getCards()
    {
        return $this->hasMany('App\Models\Cards', 'card_category_id', 'id');
    }
}
