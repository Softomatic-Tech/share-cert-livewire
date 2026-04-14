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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('building_name');
            $table->string('apartment_number');
            $table->string('did_you_purchase_the_apartment_before_the_society_was_registered')->nullable();
            $table->string('did_you_sign_at_the_time_of_the_society_registration')->nullable();
            $table->string('did_the_previous_owner_sign_the_registration_documents')->nullable();
            $table->string('has_the_flat_transfer_related_fee_been_paid_to_the_society')->nullable();
            $table->string('have_physical_documents_been_submitted_to_the_society')->nullable();
            $table->string('owner1_name');
            $table->string('owner1_email')->nullable();
            $table->string('owner1_mobile')->nullable();
            $table->string('owner2_name')->nullable();
            $table->string('owner2_email')->nullable();
            $table->string('owner2_mobile')->nullable();
            $table->string('owner3_name')->nullable();
            $table->string('owner3_email')->nullable();
            $table->string('owner3_mobile')->nullable();
            $table->json('status')->nullable();
            $table->string('agreementCopy')->nullable();
            $table->string('memberShipForm')->nullable();
            $table->string('allotmentLetter')->nullable();
            $table->string('possessionLetter')->nullable();
            $table->text('comment')->nullable();
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
