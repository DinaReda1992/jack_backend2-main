<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageCategory extends Model
{
    use HasFactory;

    protected $table = 'page_categories';

    protected $fillable = ['name_ar', 'name_en', 'is_offer', 'sub_category_id', 'category_id'];

    public function products()
    {
        return $this->belongsToMany(Products::class, PageCategoryProduct::class, 'page_category_id', 'product_id')
            // ->whereRaw('quantity - min_quantity >= min_warehouse_quantity')
            ->where('stop', 0)->where('is_archived', 0)->orderBy('sort', 'asc');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MainCategories::class, 'category_id', 'id')->whereHas('products');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'sub_category_id', 'id')->whereHas('products');
    }
}
