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
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->foreignId('society_id')->constrained('societies')->onDelete('cascade');
            $table->foreignId('building_id')->constrained('apartment_details')->onDelete('cascade');
            $table->foreignId('apartment_number')->constrained('apartment_details')->onDelete('cascade');
            $table->string('owner_name');
            $table->string('email');
            $table->string('phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};
