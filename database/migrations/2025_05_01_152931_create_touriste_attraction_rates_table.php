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
        Schema::create('touriste_attraction_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tourist_attraction_id')->references('id')->on('tourist_attractions')->onDelete('cascade');
            $table->float('rate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('touriste_attraction_rates');
    }
};
