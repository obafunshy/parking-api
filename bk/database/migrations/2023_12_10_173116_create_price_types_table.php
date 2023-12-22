<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_types', function (Blueprint $table) {
            $table->id();
            $table->decimal('weekday_price', 10, 2);
            $table->decimal('weekend_price', 10, 2);
            $table->decimal('summer_price', 10, 2);
            $table->decimal('winter_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_types');
    }
};
