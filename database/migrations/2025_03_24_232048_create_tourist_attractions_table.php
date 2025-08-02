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
        Schema::create('tourist_attractions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->text('location');
            $table->text('website_url');
            $table->string('country');
            $table->float('tax');
            $table->text('address');
            $table->integer('view')->default(0);
            $table->double('rate')->default(0);
            $table->boolean('send_notification')->default(0);
            $table->text('hours_work');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourist_attractions');
    }
};
