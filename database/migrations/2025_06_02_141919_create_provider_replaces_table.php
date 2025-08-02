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
        Schema::create('provider_replaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('replace_id')->references('id')->on('replace_points')->onDelete('cascade');
            $table->string('name');
            $table->text('address');
            $table->text('website_url');
            $table->text('location');
            $table->double('num_hours');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_replaces');
    }
};
