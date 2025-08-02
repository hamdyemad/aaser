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
        Schema::create('guide_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guide_id')->references('id')->on('guides')->onDelete('cascade');
            $table->string('name');
            $table->float('points');
            $table->float('discount');
            $table->date('date');
            $table->string('image');
            $table->double('num_customers');
            $table->double('num_every_customer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_offers');
    }
};
