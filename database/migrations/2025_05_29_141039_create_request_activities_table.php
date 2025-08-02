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
        Schema::create('request_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->nullable()->references('id')->on('reward_requests')->onDelete('cascade');
            $table->foreignId('service_activity_id')->nullable()->references('id')->on('service_entertainment_activities')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_activities');
    }
};
