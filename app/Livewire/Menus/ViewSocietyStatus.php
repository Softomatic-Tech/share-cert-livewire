<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use App\Models\Society; 
use App\Models\SocietyDetail; 
use App\Models\State;
use App\Models\City;
use App\Models\ByeLawCase;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class ViewSocietyStatus extends Component
{
    public $apartment,$state,$city;
    public $states, $cities=[];
    public $society_id,$society_name, $total_building, $total_flats, $address_1, $address_2, $pincode, $city_id, $state_id,$no_of_shares,$share_value,$apartment_id,$building_name, $apartment_number,$certificate_no,$individual_no_of_share,$owner1_name, $owner1_mobile ,$owner1_email ,$owner2_name, $owner2_mobile ,$owner2_email ,$owner3_name, $owner3_mobile ,$owner3_email;
    public $agreementCopy,$memberShipForm,$allotmentLetter,$possessionLetter;
    public $membership_case;
    protected $userService;
    public $is_byelaws_available='no';
    public $byelaws_id;
    
    // Case A fields
    public $applicant_name, $father_husband_name, $age, $monthly_income, $occupation, $office_addr, $residential_addr, $flat_area_sq_meters, $builder_name, $other_person_name1, $other_property_particulars1, $other_property_location1, $reason_for_flat1,$other_person_name2, $other_property_particulars2, $other_property_location2, $reason_for_flat2, $deceased_member_name;
    public $allotmentMembershipLetter;

    // Case B fields
    public $distinctive_no_from, $distinctive_no_to, $transferor_name, $transferee_name, $building_no;
    public $stampDutyProof, $transferorSignature;
    public $transfer_fee, $transfer_premium_amount, $transfer_ground_1, $transfer_ground_2, $transfer_ground_3;

    // Case C fields
    public $date_of_death, $society_shares, $deathCertificate, $nominationRecord;

    // Case D fields
    public $inspection_time_from, $inspection_time_to, $floor_no, $flat_bearing_no, $heir_1_name, $heir_2_name, $heir_3_name, $heir_4_name, $witness_name, $witness_address, $successionCertificate;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function render()
    {
        return view('livewire.menus.view-society-status');
    }

    public function mount($apartmentId)
    {
        $this->loadSocietyData($apartmentId);
    }

    public function loadSocietyData($apartmentId)
    {
        $apartment = SocietyDetail::with(['society.state','society.city'])->findOrFail($apartmentId);
        if ($apartment) {
            if ($apartment->society) {
                $this->society_id = $apartment->society->id;
                $this->society_name = $apartment->society->society_name;
                $this->total_building = $apartment->society->total_building;
                $this->total_flats = $apartment->society->total_flats;
                $this->address_1 = $apartment->society->address_1;
                $this->address_2 = $apartment->society->address_2;
                $this->pincode = $apartment->society->pincode;
                $this->state_id = $apartment->society->state_id;
                $this->city_id = $apartment->society->city_id;
                $this->state = $apartment->society->state->name ?? 'N/A';
                $this->city = $apartment->society->city->name ?? 'N/A';
                $this->no_of_shares = $apartment->society->no_of_shares;
                $this->share_value = $apartment->society->share_value;
            }
            $this->apartment_id = $apartment->id;
            $this->building_name = $apartment->building_name;
            $this->apartment_number = $apartment->apartment_number;
            $this->certificate_no = $apartment->certificate_no;
            $this->individual_no_of_share = $apartment->no_of_shares;
            // $this->share_capital_amount = $apartment->share_capital_amount;
            $this->owner1_name = $apartment->owner1_name;
            $this->owner1_mobile = $apartment->owner1_mobile;
            $this->owner1_email = $apartment->owner1_email;
            $this->owner2_name = $apartment->owner2_name;
            $this->owner2_mobile = $apartment->owner2_mobile;
            $this->owner2_email = $apartment->owner2_email;
            $this->owner3_name = $apartment->owner3_name;
            $this->owner3_mobile = $apartment->owner3_mobile;
            $this->owner3_email = $apartment->owner3_email;
            $this->agreementCopy=$apartment->agreementCopy;
            $this->memberShipForm=$apartment->memberShipForm;
            $this->allotmentLetter=$apartment->allotmentLetter;
            $this->possessionLetter=$apartment->possessionLetter;
            $this->is_byelaws_available = $apartment->is_byelaws_available ?? 'no';
            
            if($this->is_byelaws_available=='yes'){
                $byelaws = ByeLawCase::where('society_detail_id',$apartmentId)->first();
                if($byelaws) {
                    $this->byelaws_id = $byelaws->id;
                    $this->membership_case = $byelaws->membership_case;
                    
                    if($this->membership_case=='case_a'){
                        $this->applicant_name = $byelaws->applicant_name;
                        $this->father_husband_name = $byelaws->father_husband_name;
                        $this->deceased_member_name = $byelaws->deceased_member_name;
                        $this->age = $byelaws->age;
                        $this->monthly_income = $byelaws->monthly_income;
                        $this->occupation = $byelaws->occupation;
                        $this->office_addr = $byelaws->office_addr;
                        $this->residential_addr = $byelaws->residential_addr;
                        $this->flat_area_sq_meters = $byelaws->flat_area_sq_meters;
                        $this->builder_name = $byelaws->builder_name;
                        $this->other_person_name1 = $byelaws->other_person_name1;
                        $this->other_property_particulars1 = $byelaws->other_property_particulars1;
                        $this->other_property_location1 = $byelaws->other_property_location1;
                        $this->reason_for_flat1 = $byelaws->reason_for_flat1;
                        $this->other_person_name2 = $byelaws->other_person_name2;
                        $this->other_property_particulars2 = $byelaws->other_property_particulars2;
                        $this->other_property_location2 = $byelaws->other_property_location2;
                        $this->reason_for_flat2 = $byelaws->reason_for_flat2;
                        $this->allotmentMembershipLetter = $byelaws->allotmentMembershipLetter;
                    }

                    if($this->membership_case=='case_b'){
                        $this->distinctive_no_from = $byelaws->distinctive_no_from;
                        $this->distinctive_no_to = $byelaws->distinctive_no_to;
                        $this->transferor_name = $byelaws->transferor_name;
                        $this->transferee_name = $byelaws->transferee_name;
                        $this->building_no = $byelaws->building_no;
                        $this->flat_area_sq_meters = $byelaws->flat_area_sq_meters;
                        $this->transfer_fee = $byelaws->transfer_fee;
                        $this->transfer_premium_amount = $byelaws->transfer_premium_amount;
                        $this->transfer_ground_1 = $byelaws->transfer_ground_1;
                        $this->transfer_ground_2 = $byelaws->transfer_ground_2;
                        $this->transfer_ground_3 = $byelaws->transfer_ground_3;
                        $this->stampDutyProof = $byelaws->stampDutyProof;
                        $this->transferorSignature = $byelaws->transferorSignature;
                    }

                    if ($this->membership_case === 'case_c') {
                        $this->applicant_name = $byelaws->applicant_name;
                        $this->deceased_member_name = $byelaws->deceased_member_name;
                        $this->date_of_death = $byelaws->date_of_death;
                        $this->society_shares = $byelaws->society_shares;
                        $this->age = $byelaws->age;
                        $this->monthly_income = $byelaws->monthly_income;
                        $this->occupation = $byelaws->occupation;
                        $this->office_addr = $byelaws->office_addr;
                        $this->residential_addr = $byelaws->residential_addr;
                        $this->deathCertificate = $byelaws->deathCertificate;
                        $this->nominationRecord = $byelaws->nominationRecord;
                    }

                    if ($this->membership_case === 'case_d') {
                        $this->applicant_name = $byelaws->applicant_name;
                        $this->deceased_member_name = $byelaws->deceased_member_name;
                        $this->father_husband_name = $byelaws->father_husband_name;
                        $this->date_of_death = $byelaws->date_of_death;
                        $this->residential_addr = $byelaws->residential_addr;
                        $this->inspection_time_from = $byelaws->inspection_time_from;
                        $this->inspection_time_to = $byelaws->inspection_time_to;
                        $this->distinctive_no_from = $byelaws->distinctive_no_from;
                        $this->distinctive_no_to = $byelaws->distinctive_no_to;
                        $this->floor_no = $byelaws->floor_no;
                        $this->flat_bearing_no = $byelaws->flat_bearing_no;
                        $this->heir_1_name = $byelaws->heir_1_name;
                        $this->heir_2_name = $byelaws->heir_2_name;
                        $this->heir_3_name = $byelaws->heir_3_name;
                        $this->heir_4_name = $byelaws->heir_4_name;
                        $this->witness_address = $byelaws->witness_address;
                        $this->witness_name = $byelaws->witness_name;
                        $this->successionCertificate = $byelaws->successionCertificate;
                    }
                }
            }
        }
    }
}
