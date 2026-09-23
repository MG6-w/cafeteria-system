<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('age')->nullable();
            $table->json('favorite_categories')->nullable();
            $table->json('favorite_food_types')->nullable();
            $table->json('favorite_beverages')->nullable();
            $table->string('preferred_taste')->nullable(); // sweet, savory, spicy, sour, balanced
            $table->json('dietary_preferences')->nullable(); // vegan, vegetarian, keto, gluten_free, low_carb, etc.
            $table->decimal('price_preference', 8, 2)->nullable(); // target or max budget
            $table->integer('spicy_level')->default(0); // 0: None, 1: Mild, 2: Medium, 3: Hot/Extra Spicy
            $table->json('favorite_ingredients')->nullable();
            $table->json('disliked_ingredients')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_preferences');
    }
};
