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
        Schema::create('service_entertainment_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activitie_id')->references('id')->on('entertainment_activities')->onDelete('cascade');
            $table->string('service_type');
            $table->double('amount');
            $table->date('from');
            $table->date('to');
            $table->double('earn_points');
            $table->integer('num_tickets');
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_entertainment_activities');
    }
};
