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
        Schema::create('service_stock_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_point_id')->references('id')->on('stock_points')->onDelete('cascade');
            $table->string('name');
            $table->double('amount');
            $table->double('point');
            $table->string('image')->nullable();
            $table->double('before_price');
            $table->double('after_price');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_stock_points');
    }
};
