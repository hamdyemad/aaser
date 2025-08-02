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
        Schema::create('reward_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reward_id')->nullable()->references('id')->on('rewards')->onDelete('cascade');
            $table->foreignId('visitor_exhibition_conference_id')->nullable()->references('id')->on('visitor_exhibition_conferences')->onDelete('cascade');
            $table->foreignId('participant_exhibition_conference_id')->nullable()->references('id')->on('participant_exhibition_conferences')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('request_id')->unique();
            $table->enum('status',['new','done'])->default('new');
            $table->dateTime('done_date')->nullable();
            $table->integer('done_by_service_provider')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_requests');
    }
};
