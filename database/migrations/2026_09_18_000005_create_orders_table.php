<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_price', 8, 2);
            $table->enum('status', ['pending', 'preparing', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'cash_on_delivery'])->default('cash_on_delivery');
            $table->text('notes')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
