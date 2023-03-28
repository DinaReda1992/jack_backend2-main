<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MainSupplier extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function suppliers(): HasMany
    {
        return $this->hasMany(User::class, 'main_supplier_id', 'id');
    }
}
