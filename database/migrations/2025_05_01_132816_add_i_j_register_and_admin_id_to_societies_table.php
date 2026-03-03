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
            $table->string('i_register')->nullable()->after('share_value');
            $table->string('j_register')->nullable()->after('i_register');
            $table->unsignedBigInteger('admin_id')->nullable()->after('j_register');

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn(['i_register', 'j_register', 'admin_id']);
        });
    }
};
