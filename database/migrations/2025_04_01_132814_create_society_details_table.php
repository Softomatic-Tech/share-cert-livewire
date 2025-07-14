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
        Schema::create('society_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('society_id'); 
            $table->foreign('society_id')->references('id')->on('societies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('building_name');
            $table->string('apartment_number');
            $table->string('owner1_name');
            $table->string('owner1_email')->nullable();
            $table->string('owner1_mobile')->nullable();
            $table->string('owner2_name')->nullable();
            $table->string('owner2_email')->nullable();
            $table->string('owner2_mobile')->nullable();
            $table->string('owner3_name')->nullable();
            $table->string('owner3_email')->nullable();
            $table->string('owner3_mobile')->nullable();
            $table->text('status')->nullable();
            $table->string('docs1')->nullable();
            $table->string('docs2')->nullable();
            $table->string('docs3')->nullable();
            $table->string('docs4')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('society_details');
    }
};
