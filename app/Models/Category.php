<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'icon',
        'image',
        'description',
    ];

    public function foodItems(): HasMany
    {
        return $this->hasMany(FoodItem::class);
    }

    public function beverages(): HasMany
    {
        return $this->hasMany(Beverage::class);
    }

    public function itemsCount(): int
    {
        return $this->foodItems()->count() + $this->beverages()->count();
    }
}
