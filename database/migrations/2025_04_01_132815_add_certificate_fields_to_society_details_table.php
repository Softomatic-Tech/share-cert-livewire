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
            $table->string('certificate_no')->nullable()->after('apartment_number');
            $table->enum('certificate_status', ['pending', 'approved', 'changes_required'])->default('pending')->after('comment');
            $table->text('certificate_remark')->nullable()->after('certificate_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('society_details', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_no',
                'certificate_status',
                'certificate_remark',
            ]);
        });
    }
};
