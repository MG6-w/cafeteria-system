<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beverages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->enum('size', ['small', 'medium', 'large'])->default('medium');
            $table->text('ingredients')->nullable();
            $table->integer('calories')->nullable();
            $table->enum('temperature', ['hot', 'cold'])->default('cold');
            $table->integer('available_quantity')->default(0);
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beverages');
    }
};
