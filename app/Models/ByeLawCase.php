<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ByeLawCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'society_detail_id',
        'membership_case',
        'applicant_name',
        'father_husband_name',
        'age',
        'monthly_income',
        'occupation',
        'office_addr',
        'residential_addr',
        'flat_area_sq_meters',
        'builder_name',
        'other_person_name1',
        'other_property_particulars1',
        'other_property_location1',
        'reason_for_flat1',
        'other_person_name2',
        'other_property_particulars2',
        'other_property_location2',
        'reason_for_flat2',
        'deceased_member_name',
        'transferee_name',
        'transferor_name',
        'distinctive_no_from',
        'distinctive_no_to',
        'building_no',
        'flat_area_sq_meters',
        'transfer_fee',
        'transfer_premium_amount',
        'transfer_ground_1',
        'transfer_ground_2',
        'transfer_ground_3',
        'date_of_death',
        'society_shares',
        'inspection_time_from',
        'inspection_time_to',
        'floor_no',
        'flat_bearing_no',
        'heir_1_name',
        'heir_2_name',
        'heir_3_name',
        'heir_4_name',
        'witness_name',
        'witness_address',
        'allotmentMembershipLetter',
        'stampDutyProof',
        'transferorSignature',
        'deathCertificate',
        'nominationRecord',
        'successionCertificate',
    ];

    public function societyDetail()
    {
        return $this->belongsTo(SocietyDetail::class);
    }
}
