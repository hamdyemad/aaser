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
        Schema::create('entertainment_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->text('location');
            $table->float('tax');
            $table->text('website_url');
            $table->string('email'); //
            $table->dateTime('appointment'); //
            $table->dateTime('apper_appointment'); //
            $table->text('address');
            $table->string('country');
            $table->string('place');
            $table->boolean('status')->default(1);
            $table->integer('view')->default(0);
            $table->boolean('send_notification')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entertainment_activities');
    }
};
