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
        Schema::create('term_entertainment_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activitie_id')->references('id')->on('entertainment_activities')->onDelete('cascade');
            $table->string('term');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('term_entertainment_activities');
    }
};
