<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beverage extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'size',
        'ingredients',
        'calories',
        'temperature',
        'available_quantity',
        'image',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'calories' => 'integer',
        'available_quantity' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'item_id')->where('item_type', 'beverage');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'item_id')->where('item_type', 'beverage');
    }

    public function averageRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 5.0, 1);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->available_quantity > 0;
    }

    public function getIngredientsListAttribute(): array
    {
        if (empty($this->ingredients)) return [];
        return array_map('trim', explode(',', $this->ingredients));
    }
}
