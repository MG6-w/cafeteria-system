<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('item_type', ['food', 'beverage']);
            $table->unsignedBigInteger('item_id');
            $table->timestamps();

            $table->unique(['user_id', 'item_type', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
