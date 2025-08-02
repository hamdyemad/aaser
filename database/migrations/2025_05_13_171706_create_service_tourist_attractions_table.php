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
        Schema::create('service_tourist_attractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tourist_attraction_id')->references('id')->on('tourist_attractions')->onDelete('cascade');
            $table->string('name');
            $table->string('image');
            $table->double('before_tax');
            $table->double('price');
            $table->date('date');
            $table->double('earn_points');
            $table->integer('count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_tourist_attractions');
    }
};
