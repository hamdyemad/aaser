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
        Schema::create('ad_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->references('id')->on('ads')->onDelete('cascade');
            $table->enum('location',['home','tourist_attraction','episode','guide','entertainment_activity','exhibition_conference','sponsor','particiants','asser_history']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_locations');
    }
};
