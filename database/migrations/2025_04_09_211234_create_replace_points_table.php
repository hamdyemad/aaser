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
        Schema::create('replace_points', function (Blueprint $table) {
            $table->id();
            $table->string('reward_address');
            $table->text('reward_description');
            $table->text('location');
            $table->text('website_url');
            $table->string('file')->nullable();
            $table->string('image')->nullable();
            $table->boolean('send_notification')->default(0);
            $table->boolean('have_count')->default(0);
            $table->integer('count_people')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replace_points');
    }
};
