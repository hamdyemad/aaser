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
        Schema::create('request_touristes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->nullable()->references('id')->on('reward_requests')->onDelete('cascade');
            $table->foreignId('service_tourist_attraction_id')->nullable()->references('id')->on('service_tourist_attractions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_touristes');
    }
};
