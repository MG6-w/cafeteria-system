<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->text('ingredients')->nullable(); // Comma-separated or JSON list of ingredients
            $table->integer('calories')->nullable();
            $table->integer('spicy_level')->default(0); // 0 to 3
            $table->integer('available_quantity')->default(0);
            $table->integer('preparation_time')->default(15); // in minutes
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};
