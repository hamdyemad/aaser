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
        Schema::create('request_replace_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->nullable()->references('id')->on('reward_requests')->onDelete('cascade');
            $table->foreignId('replace_reward_id')->nullable()->references('id')->on('reward_replace_points')->onDelete('cascade');
            $table->integer('products_count')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_replace_points');
    }
};
