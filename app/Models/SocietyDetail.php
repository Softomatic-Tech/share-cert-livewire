<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocietyDetail extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => 'array',
    ];

    protected $fillable = ['society_id', 'user_id', 'building_name', 'apartment_number', 'certificate_no', 'did_you_purchase_the_apartment_before_the_society_was_registered', 'did_you_sign_at_the_time_of_the_society_registration', 'did_the_previous_owner_sign_the_registration_documents', 'has_the_flat_transfer_related_fee_been_paid_to_the_society', 'have_physical_documents_been_submitted_to_the_society', 'owner1_name', 'owner1_mobile', 'owner1_email', 'owner2_name', 'owner2_mobile', 'owner2_email', 'owner3_name', 'owner3_mobile', 'owner3_email', 'agreementCopy', 'memberShipForm', 'allotmentLetter', 'possessionLetter', 'status', 'comment', 'stampDutyProof', 'transferorSignature', 'deathCertificate', 'nominationRecord', 'successionCertificate'];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function byeLawCase()
    {
        return $this->hasOne(ByeLawCase::class, 'society_detail_id');
    }
}
