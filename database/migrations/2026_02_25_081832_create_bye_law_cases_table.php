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
        Schema::create('bye_law_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('society_detail_id')->constrained('society_details')->onDelete('cascade');
            $table->string('membership_case')->nullable();
            
            // Case A fields
            $table->string('applicant_name')->nullable();
            $table->string('father_husband_name')->nullable();
            $table->string('age')->nullable();
            $table->string('monthly_income')->nullable();
            $table->string('occupation')->nullable();
            $table->text('office_addr')->nullable();
            $table->text('residential_addr')->nullable();
            $table->string('flat_area_sq_meters')->nullable();
            $table->string('builder_name')->nullable();
            $table->string('other_person_name1')->nullable();
            $table->text('other_property_particulars1')->nullable();
            $table->text('other_property_location1')->nullable();
            $table->text('reason_for_flat1')->nullable();
            $table->string('other_person_name2')->nullable();
            $table->text('other_property_particulars2')->nullable();
            $table->text('other_property_location2')->nullable();
            $table->text('reason_for_flat2')->nullable();
            
            // Case B fields
            $table->string('distinctive_no_from')->nullable();
            $table->string('distinctive_no_to')->nullable();
            $table->string('transferor_name')->nullable();
            $table->string('transferee_name')->nullable();
            $table->string('building_no')->nullable();
            $table->string('transfer_fee')->nullable();
            $table->string('transfer_premium_amount')->nullable();
            $table->string('transfer_ground_1')->nullable();
            $table->string('transfer_ground_2')->nullable();
            $table->string('transfer_ground_3')->nullable();
            
            // Case C & D fields
            $table->string('deceased_member_name')->nullable();
            $table->date('date_of_death')->nullable();
            $table->integer('society_shares')->nullable();
            $table->time('inspection_time_from')->nullable();
            $table->time('inspection_time_to')->nullable();
            $table->string('floor_no')->nullable();
            $table->string('flat_bearing_no')->nullable();
            $table->string('heir_1_name')->nullable();
            $table->string('heir_2_name')->nullable();
            $table->string('heir_3_name')->nullable();
            $table->string('heir_4_name')->nullable();
            $table->string('witness_name')->nullable();
            $table->string('witness_address')->nullable();

            // Specialized Files
            $table->string('allotmentMembershipLetter')->nullable();
            $table->string('stampDutyProof')->nullable();
            $table->string('transferorSignature')->nullable();
            $table->string('deathCertificate')->nullable();
            $table->string('nominationRecord')->nullable();
            $table->string('successionCertificate')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bye_law_cases');
    }
};
