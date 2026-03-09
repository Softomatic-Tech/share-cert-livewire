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

    protected $fillable = ['society_id','user_id','building_name', 'apartment_number','certificate_no','no_of_shares','owner1_name','owner1_mobile','owner1_email','owner2_name','owner2_mobile','owner2_email','owner3_name','owner3_mobile','owner3_email','agreementCopy','memberShipForm','allotmentLetter','possessionLetter','status','comment', 'is_registration_no_available', 'is_byelaws_available', 'stampDutyProof', 'transferorSignature', 'deathCertificate', 'nominationRecord', 'successionCertificate'];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function byeLawCase()
    {
        return $this->hasOne(ByeLawCase::class,'society_detail_id');
    }
}
