<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'total_price',
        'status',
        'payment_status',
        'notes',
        'cancelled_reason',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'pending';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
            'preparing' => 'bg-blue-100 text-blue-800 border-blue-300',
            'ready' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'cancelled' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
