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
        Schema::table('societies', function (Blueprint $table) {
            $table->string('registration_no')->nullable()->after('city_id');
            $table->integer('no_of_shares')->nullable()->after('registration_no');
            $table->integer('share_value')->nullable()->after('no_of_shares');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            $table->dropColumn([
                'registration_no',
                'no_of_shares',
                'share_value'
            ]);
        });
    }
};
