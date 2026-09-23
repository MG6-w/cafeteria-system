<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'age',
        'favorite_categories',
        'favorite_food_types',
        'favorite_beverages',
        'preferred_taste',
        'dietary_preferences',
        'price_preference',
        'spicy_level',
        'favorite_ingredients',
        'disliked_ingredients',
    ];

    protected $casts = [
        'favorite_categories' => 'array',
        'favorite_food_types' => 'array',
        'favorite_beverages' => 'array',
        'dietary_preferences' => 'array',
        'favorite_ingredients' => 'array',
        'disliked_ingredients' => 'array',
        'price_preference' => 'decimal:2',
        'spicy_level' => 'integer',
        'age' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
