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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // city name
            $table->unsignedBigInteger('state_id'); // foreign key
            $table->timestamps();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade'); // if state deleted, delete cities
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
