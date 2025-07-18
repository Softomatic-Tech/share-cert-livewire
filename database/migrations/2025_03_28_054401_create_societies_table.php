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
        Schema::create('societies', function (Blueprint $table) {
            $table->id();
            $table->string('society_name');
            $table->string('total_flats');
            $table->string('address_1');
            $table->string('address_2')->nullable();
            $table->string('pincode', 6);
            $table->string('state');
            $table->string('city'); 
            $table->timestamp('created_at')->useCurrent()->nullable(false);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('societies');
    }
};
