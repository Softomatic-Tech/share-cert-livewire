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
        Schema::table('society_details', function (Blueprint $table) {
            $table->string('is_byelaws_available')->default('no')->after('owner3_mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('society_details', function (Blueprint $table) {
            $table->dropColumn('is_byelaws_available');
        });
    }
};
