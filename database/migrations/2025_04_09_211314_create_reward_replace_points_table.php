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
        Schema::create('reward_replace_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('replace_point_id')->references('id')->on('replace_points')->onDelete('cascade');
            $table->string('name');
            $table->double('point');
            $table->string('image')->nullable();
            $table->double('qty');
            $table->double('available');
            $table->double('residual');
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_replace_points');
    }
};
