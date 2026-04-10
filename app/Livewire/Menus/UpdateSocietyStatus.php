<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Society;
use App\Models\SocietyDetail;
use App\Models\State;
use App\Models\City;
use App\Models\ByeLawCase;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class UpdateSocietyStatus extends Component
{
    use WithFileUploads;
    public $apartment, $state, $city;
    public $states, $cities = [];
    public $society_id, $society_name, $total_building, $total_flats, $address_1, $address_2, $pincode, $city_id, $state_id, $no_of_shares, $share_value, $is_byelaws_available, $apartment_id, $building_name, $apartment_number, $certificate_no, $owner1_name, $owner1_mobile, $owner1_email, $owner2_name, $owner2_mobile, $owner2_email, $owner3_name, $owner3_mobile, $owner3_email;
    public $agreementCopy, $memberShipForm, $allotmentLetter, $possessionLetter;
    public $newAgreementCopy, $newMemberShipForm, $newAllotmentLetter, $newPossessionLetter, $newStampDutyProof, $newAllotmentMembershipLetter, $newTransferorSignature;
    public $membership_case;
    protected $userService;
    public $fileKey;
    public $agreementUploaded = false;
    public $membershipUploaded = false;
    public $allotmentUploaded = false;
    public $possessionUploaded = false;
    public $approvedFiles;
    public $byelaws_id;
    // Case A (Appendix 2 & 3) fields
    public $applicant_name, $father_husband_name, $age, $monthly_income, $occupation, $office_addr, $residential_addr, $flat_area_sq_meters, $builder_name, $other_person_name1, $other_property_particulars1, $other_property_location1, $reason_for_flat1, $other_person_name2, $other_property_particulars2, $other_property_location2, $reason_for_flat2, $deceased_member_name;
    public $allotmentMembershipLetter;

    // Case B (Appendix 20(1) & 20(2) & 21) fields
    public $distinctive_no_from, $distinctive_no_to, $transferor_name, $transferee_name, $building_no;
    public $stampDutyProof, $transferorSignature;
    public $transfer_fee, $transfer_premium_amount, $transfer_ground_1, $transfer_ground_2, $transfer_ground_3;

    // Case C (Appendix 15) fields
    public $date_of_death, $society_shares, $deathCertificate, $nominationRecord, $newDeathCertificate, $newNominationRecord;

    // Case D (Appendix 16 & 19) fields
    public $inspection_time_from, $inspection_time_to, $floor_no, $flat_bearing_no, $heir_1_name, $heir_2_name, $heir_3_name, $heir_4_name, $witness_name, $witness_address, $successionCertificate, $newSuccessionCertificate;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function render()
    {
        return view('livewire.menus.update-society-status');
    }

    public function mount($apartmentId)
    {
        $this->states = State::all();
        $this->fileKey = now()->timestamp;
        $this->loadSocietyData($apartmentId);
    }

    public function updatedStateID($value)
    {
        $this->cities = City::where('state_id', $value)->get();
    }

    public function loadSocietyData($apartmentId)
    {
        $apartment = SocietyDetail::with(['society.state', 'society.city'])->findOrFail($apartmentId);
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
                $this->cities = City::where('state_id', $this->state_id)->get();
                $this->city_id = $apartment->society->city_id;
                $this->state = $apartment->society->state->name;
                $this->city = $apartment->society->city->name;
                $this->no_of_shares = $apartment->society->no_of_shares;
                $this->share_value = $apartment->society->share_value;
            }
            $this->apartment_id = $apartment->id;
            $this->building_name = $apartment->building_name;
            $this->apartment_number = $apartment->apartment_number;
            $this->certificate_no = $apartment->certificate_no;
            // $this->individual_no_of_share = $apartment->no_of_shares;
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
            $this->agreementCopy = $apartment->agreementCopy;
            $this->memberShipForm = $apartment->memberShipForm;
            $this->allotmentLetter = $apartment->allotmentLetter;
            $this->possessionLetter = $apartment->possessionLetter;
            $this->is_byelaws_available = strtolower($apartment->society->is_byelaws_available ?? 'no');
            if ($this->is_byelaws_available == 'yes') {
                $byelaws = ByeLawCase::where('society_detail_id', $apartmentId)->first();
                $this->byelaws_id = $byelaws->id ?? null;
                $this->membership_case = $byelaws->membership_case ?? null;
                // Load Case A fields
                if ($this->membership_case == 'case_a') {
                    $this->applicant_name = $byelaws->applicant_name ?? null;
                    $this->father_husband_name = $byelaws->father_husband_name ?? null;
                    $this->deceased_member_name = $byelaws->deceased_member_name ?? null;
                    $this->age = $byelaws->age ?? null;
                    $this->monthly_income = $byelaws->monthly_income ?? null;
                    $this->occupation = $byelaws->occupation ?? null;
                    $this->office_addr = $byelaws->office_addr ?? null;
                    $this->residential_addr = $byelaws->residential_addr ?? null;
                    $this->flat_area_sq_meters = $byelaws->flat_area_sq_meters ?? null;
                    $this->builder_name = $byelaws->builder_name ?? null;
                    $this->other_person_name1 = $byelaws->other_person_name1 ?? null;
                    $this->other_property_particulars1 = $byelaws->other_property_particulars1 ?? null;
                    $this->other_property_location1 = $byelaws->other_property_location1 ?? null;
                    $this->reason_for_flat1 = $byelaws->reason_for_flat1 ?? null;
                    $this->other_person_name2 = $byelaws->other_person_name2 ?? null;
                    $this->other_property_particulars2 = $byelaws->other_property_particulars2 ?? null;
                    $this->other_property_location2 = $byelaws->other_property_location2 ?? null;
                    $this->reason_for_flat2 = $byelaws->reason_for_flat2 ?? null;
                    $this->allotmentMembershipLetter = $byelaws->allotmentMembershipLetter ?? null;
                }


                // Load Case B fields
                if ($this->membership_case == 'case_b') {
                    $this->distinctive_no_from = $byelaws->distinctive_no_from ?? null;
                    $this->distinctive_no_to = $byelaws->distinctive_no_to ?? null;
                    $this->transferor_name = $byelaws->transferor_name ?? null;
                    $this->transferee_name = $byelaws->transferee_name ?? null;
                    $this->building_no = $byelaws->building_no ?? null;
                    $this->flat_area_sq_meters = $byelaws->flat_area_sq_meters ?? null;
                    $this->transfer_fee = $byelaws->transfer_fee ?? null;
                    $this->transfer_premium_amount = $byelaws->transfer_premium_amount ?? null;
                    $this->transfer_ground_1 = $byelaws->transfer_ground_1 ?? null;
                    $this->transfer_ground_2 = $byelaws->transfer_ground_2 ?? null;
                    $this->transfer_ground_3 = $byelaws->transfer_ground_3 ?? null;
                    $this->stampDutyProof = $byelaws->stampDutyProof ?? null;
                    $this->transferorSignature = $byelaws->transferorSignature ?? null;
                }
                // Load Case C fields
                if ($this->membership_case === 'case_c') {
                    $this->deceased_member_name = $byelaws->deceased_member_name ?? null;
                    $this->date_of_death = $byelaws->date_of_death ?? null;
                    $this->society_shares = $byelaws->society_shares ?? null;
                    $this->age = $byelaws->age ?? null;
                    $this->monthly_income = $byelaws->monthly_income ?? null;
                    $this->occupation = $byelaws->occupation ?? null;
                    $this->office_addr = $byelaws->office_addr ?? null;
                    $this->residential_addr = $byelaws->residential_addr ?? null;
                    $this->deathCertificate = $byelaws->deathCertificate ?? null;
                    $this->nominationRecord = $byelaws->nominationRecord ?? null;
                }

                // Load Case C fields
                if ($this->membership_case === 'case_d') {
                    $this->applicant_name = $byelaws->applicant_name ?? null;
                    $this->deceased_member_name = $byelaws->deceased_member_name ?? null;
                    $this->father_husband_name = $byelaws->father_husband_name ?? null;
                    $this->date_of_death = $byelaws->date_of_death ?? null;
                    $this->residential_addr = $byelaws->residential_addr ?? null;
                    $this->inspection_time_from = $byelaws->inspection_time_from ?? null;
                    $this->inspection_time_to = $byelaws->inspection_time_to ?? null;
                    $this->distinctive_no_from = $byelaws->distinctive_no_from ?? null;
                    $this->distinctive_no_to = $byelaws->distinctive_no_to ?? null;
                    $this->floor_no = $byelaws->floor_no ?? null;
                    $this->flat_bearing_no = $byelaws->flat_bearing_no ?? null;
                    $this->heir_1_name = $byelaws->heir_1_name ?? null;
                    $this->heir_2_name = $byelaws->heir_2_name ?? null;
                    $this->heir_3_name = $byelaws->heir_3_name ?? null;
                    $this->heir_4_name = $byelaws->heir_4_name ?? null;
                    $this->witness_address = $byelaws->witness_address ?? null;
                    $this->witness_name = $byelaws->witness_name ?? null;
                    $this->successionCertificate = $byelaws->successionCertificate ?? null;
                }
            }
            // prepare approved files array
            $this->approvedFiles = [];
            if (!empty($statusData['tasks'])) {
                foreach ($statusData['tasks'] as $task) {
                    if ($task['name'] === 'Application') {
                        foreach ($task['subtasks'] ?? [] as $subtask) {
                            if ($subtask['status'] === 'Approved') {
                                $this->approvedFiles[] = $subtask['fileName'];
                            }
                        }
                    }
                }
            }
        }
    }

    public function isFileApproved($statusData, $fileName)
    {
        $data = [
            'statusData' => $statusData,
            'fileName' => $fileName,
        ];
        $response = $this->userService->checkFileApproval($data);
        return $response;
    }

    public function updateSocietyDetails()
    {
        Log::info('Updating society details for apartment ID: ' . $this->apartment_id);
        $fileFields = [
            'agreementCopy' => 'newAgreementCopy',
            'memberShipForm' => 'newMemberShipForm',
            'allotmentLetter' => 'newAllotmentLetter',
            'possessionLetter' => 'newPossessionLetter',
            'stampDutyProof' => 'newStampDutyProof',
            'transferorSignature' => 'newTransferorSignature',
            'deathCertificate' => 'newDeathCertificate',
            'nominationRecord' => 'newNominationRecord',
            'successionCertificate' => 'newSuccessionCertificate',
            'allotmentMembershipLetter' => 'newAllotmentMembershipLetter',
        ];
        $fileRules = [];
        foreach ($fileFields as $dbField => $livewireField) {
            if ($this->$livewireField) {
                $fileRules[$livewireField] = 'file|mimes:jpeg,png,jpg,pdf|max:2048';
            }
        }

        if ($this->is_byelaws_available === 'Yes') {
            $rules['membership_case'] = 'required';
            if ($this->membership_case === 'case_a') {
                $rules['applicant_name'] = 'required|string';
                $rules['father_husband_name'] = 'required|string';
                $rules['deceased_member_name'] = 'required|string';
                $rules['occupation'] = 'required|string';
                $rules['age'] = 'required|numeric';
                $rules['monthly_income'] = 'required|numeric';
                $rules['residential_addr'] = 'required|string';
                $rules['office_addr'] = 'required|string';
                $rules['flat_area_sq_meters'] = 'required';
                $rules['builder_name'] = 'required|string';
                $rules['other_person_name1'] = 'required|string';
                $rules['other_property_location1'] = 'required|string';
                $rules['other_property_particulars1'] = 'required|string';
                $rules['reason_for_flat1'] = 'required|string';
            }

            if ($this->membership_case === 'case_b') {
                $rules['distinctive_no_from'] = 'required';
                $rules['distinctive_no_to'] = 'required';
                $rules['building_no'] = 'required';
                $rules['flat_area_sq_meters'] = 'required';
                $rules['transferor_name'] = 'required';
                $rules['transferee_name'] = 'required';
                $rules['transfer_fee'] = 'required|numeric';
                $rules['transfer_premium_amount'] = 'required|numeric';
                $rules['transfer_ground_1'] = 'required|string';
                $rules['transfer_ground_2'] = 'required|string';
                $rules['transfer_ground_3'] = 'required|string';
            }

            if ($this->membership_case === 'case_c') {
                $rules['applicant_name'] = 'required|string';
                $rules['deceased_member_name'] = 'required|string';
                $rules['society_shares'] = 'required|numeric';
                $rules['date_of_death'] = 'required|date';
                $rules['occupation'] = 'required|string';
                $rules['age'] = 'required|numeric';
                $rules['monthly_income'] = 'required|numeric';
                $rules['residential_addr'] = 'required|string';
                $rules['office_addr'] = 'required|string';
            }

            if ($this->membership_case === 'case_d') {
                $rules['applicant_name'] = 'required|string';
                $rules['deceased_member_name'] = 'required|string';
                $rules['father_husband_name'] = 'required|string';
                $rules['date_of_death'] = 'required|date';
                $rules['residential_addr'] = 'required|string';
                $rules['inspection_time_from'] = 'required';
                $rules['inspection_time_to'] = 'required';
                $rules['distinctive_no_from'] = 'required';
                $rules['distinctive_no_to'] = 'required';
                $rules['floor_no'] = 'required|numeric';
                $rules['flat_bearing_no'] = 'required|numeric';
                $rules['heir_1_name'] = 'required|string';
                $rules['witness_address'] = 'required|string';
            }
        }

        $baseRules = [
            'society_name' => 'required|string|max:255',
            'total_flats' => 'required|numeric',
            'total_building' => 'required|numeric',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'pincode' => 'required|digits:6',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'no_of_shares' => 'required|numeric',
            'share_value' => 'required|numeric',
            'building_name' => 'required|string|max:255',
            'apartment_number' => 'required|string|max:50',
            // 'individual_no_of_share' => 'required|numeric',
            // 'share_capital_amount' => 'required|numeric',
            'certificate_no' => 'required|string|max:255',
            'owner1_name' => 'required|string|max:255',
            'owner1_email' => 'nullable|string|email|max:255',
            'owner1_mobile' => 'required|digits:10',
        ];

        if ($this->is_byelaws_available === 'Yes') {
            $rules = array_merge($baseRules, $rules, $fileRules);
        } else {
            $rules = array_merge($baseRules, $fileRules);
        }
        $this->validate($rules);
        Log::info('Validation passed for apartment ID: ' . $this->apartment_id);
        $response = $this->userService->updateSocietyDetails(
            [
                'society_name' => $this->society_name,
                'total_building' => $this->total_building,
                'total_flats' => $this->total_flats,
                'address_1' => $this->address_1,
                'address_2' => $this->address_2,
                'pincode' => $this->pincode,
                'state_id' => $this->state_id,
                'city_id' => $this->city_id,
                'no_of_shares' => $this->no_of_shares,
                'share_value' => $this->share_value,
                'building_name' => $this->building_name,
                'apartment_number' => $this->apartment_number,
                'certificate_no' => $this->certificate_no,
                // 'individual_no_of_share' => $this->individual_no_of_share,
                // 'share_capital_amount' => $this->share_capital_amount,
                'owner1_name' => $this->owner1_name,
                'owner1_email' => $this->owner1_email,
                'owner1_mobile' => $this->owner1_mobile,
                'owner2_name' => $this->owner2_name,
                'owner2_email' => $this->owner2_email,
                'owner2_mobile' => $this->owner2_mobile,
                'owner3_name' => $this->owner3_name,
                'owner3_email' => $this->owner3_email,
                'owner3_mobile' => $this->owner3_mobile,
                'is_byelaws_available' => $this->is_byelaws_available,
                'membership_case' => $this->membership_case,
                'applicant_name' => $this->applicant_name,
                'father_husband_name' => $this->father_husband_name,
                'age' => $this->age,
                'monthly_income' => $this->monthly_income,
                'occupation' => $this->occupation,
                'office_addr' => $this->office_addr,
                'residential_addr' => $this->residential_addr,
                'flat_area_sq_meters' => $this->flat_area_sq_meters,
                'builder_name' => $this->builder_name,
                'other_person_name1' => $this->other_person_name1,
                'other_property_particulars1' => $this->other_property_particulars1,
                'other_property_location1' => $this->other_property_location1,
                'reason_for_flat1' => $this->reason_for_flat1,
                'other_person_name2' => $this->other_person_name2,
                'other_property_particulars2' => $this->other_property_particulars2,
                'other_property_location2' => $this->other_property_location2,
                'reason_for_flat2' => $this->reason_for_flat2,
                'deceased_member_name' => $this->deceased_member_name,
                'distinctive_no_from' => $this->distinctive_no_from,
                'distinctive_no_to' => $this->distinctive_no_to,
                'transferor_name' => $this->transferor_name,
                'transferee_name' => $this->transferee_name,
                'building_no' => $this->building_no,
                'transfer_fee' => $this->transfer_fee,
                'transfer_premium_amount' => $this->transfer_premium_amount,
                'transfer_ground_1' => $this->transfer_ground_1,
                'transfer_ground_2' => $this->transfer_ground_2,
                'transfer_ground_3' => $this->transfer_ground_3,
                'date_of_death' => $this->date_of_death,
                'society_shares' => $this->society_shares,
                'inspection_time_from' => $this->inspection_time_from,
                'inspection_time_to' => $this->inspection_time_to,
                'floor_no' => $this->floor_no,
                'flat_bearing_no' => $this->flat_bearing_no,
                'heir_1_name' => $this->heir_1_name,
                'heir_2_name' => $this->heir_2_name,
                'heir_3_name' => $this->heir_3_name,
                'heir_4_name' => $this->heir_4_name,
                'witness_name' => $this->witness_name,
                'witness_address' => $this->witness_address,
            ],
            $this->society_id,
            $this->apartment_id
        );
        Log::info('Society details update response for apartment ID: ' . $this->apartment_id);
        $uploadedFiles = [];
        $fieldLabels = [
            'agreementCopy' => 'Agreement Copy',
            'memberShipForm' => 'Membership Form',
            'allotmentLetter' => 'Allotment Letter',
            'possessionLetter' => 'Possession Letter',
            'stampDutyProof' => 'Stamp Duty Proof',
            'transferorSignature' => 'Transferor Signature',
            'deathCertificate' => 'Death Certificate',
            'nominationRecord' => 'Nomination Record',
            'successionCertificate' => 'Succession Certificate',
            'allotmentMembershipLetter' => 'Allotment Membership Letter',
        ];
        Log::info('File upload results for apartment ID: ' . $this->apartment_id);
        foreach ($fileFields as $dbField => $livewireField) {
            if ($this->$livewireField) {
                $uploadResult = $this->userService->uploadSocietyDocument(
                    $this->apartment_id,
                    $this->$livewireField,
                    $dbField
                );

                if ($uploadResult['status']) {
                    $uploadedFiles[] = $fieldLabels[$dbField] ?? $dbField;
                    $this->reset($livewireField);
                }
            }
        }
        if ($response['status'] || !empty($uploadedFiles)) {
            $this->loadSocietyData($this->apartment_id);
            // Automatically update status based on uploaded documents
            $user = Auth::user();
            $statusResponse = $this->userService->updateStatus($this->apartment_id, $user->id);
            $message = $response['message'] ?? 'Society details updated.';
            if (!empty($uploadedFiles)) {
                $message .= ' Files updated: ' . implode(', ', $uploadedFiles) . '.';
            }

            if (isset($statusResponse['message'])) {
                $message .= ' ' . $statusResponse['message'];
            }

            $this->dispatch('show-success', message: $message);
            $this->dispatch('scroll-to-top');
            $this->fileKey = now()->timestamp;
            return true;
        }

        $this->dispatch('show-error', message: $response['message'] ?? 'Update failed');
        return false;
    }

    public function updatedMembershipCase()
    {
        // Reset all case-related fields
        $this->reset([
            'applicant_name',
            'father_husband_name',
            'occupation',
            'age',
            'monthly_income',
            'residential_addr',
            'office_addr',
            'flat_area_sq_meters',
            'builder_name',
            'other_person_name1',
            'other_property_location1',
            'other_property_particulars1',
            'reason_for_flat1',
            'other_person_name2',
            'other_property_location2',
            'other_property_particulars2',
            'reason_for_flat2',

            'distinctive_no_from',
            'distinctive_no_to',
            'building_no',
            'flat_area_sq_meters',
            'transferor_name',
            'transferee_name',
            'transfer_fee',
            'transfer_premium_amount',
            'transfer_ground_1',
            'transfer_ground_2',
            'transfer_ground_3',

            'deceased_member_name',
            'date_of_death',
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
        ]);

        $this->resetErrorBag();
    }

    public function updatedIsByelawsAvailable()
    {
        if ($this->is_byelaws_available !== 'yes') {

            $this->membership_case = null;

            $this->updatedMembershipCase(); // reuse reset logic
        }
    }
}
