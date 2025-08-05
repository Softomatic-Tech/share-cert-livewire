<?php

namespace App\Livewire\Menus;

use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserDashboard extends Component
{
    public $societyDetail = [];
    public $society_name, $total_flats, $address_1, $address_2, $pincode, $city, $state='';
    public $building_name, $apartment_number, $owner1_name, $owner1_mobile ,$owner1_email ,$owner2_name, $owner2_mobile ,$owner2_email ,$owner3_name, $owner3_mobile ,$owner3_email='';
    public $isSocietyAccordionOpen = false;
    public $isApartmentAccordionOpen = false;
    public $comment,$search;    
    public function render()
    {
        return view('livewire.menus.user-dashboard');
    }

    public function mount()
    {
        $this->getSocietyDetail();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2 || $this->search === '') {
            $this->getSocietyDetail();
        }
    }

    public function getSocietyDetail(){
        $this->societyDetail = SocietyDetail::with('society')
            ->where(function ($query) {
                $userMobile = Auth::user()->phone;
                $query->where('owner1_mobile', $userMobile)
                    ->orWhere('owner2_mobile', $userMobile)
                    ->orWhere('owner3_mobile', $userMobile);
            })
            ->when(strlen($this->search) >= 2, function ($query) {
                $query->whereHas('society', function ($q) {
                    $q->where('society_name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('building_name', 'like', '%' . $this->search . '%')
                ->orWhere('apartment_number', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    public function updateSociety()
    {
        $this->validate([
            'society_name' => 'required|string|max:255',
            'total_flats' => 'required|numeric',
            'address_1' => 'required|string|max:255',
            'pincode' => 'required|digits:6',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);

        $society = Society::find($this->selectedSociety);
        if ($society) {
            $society->update([
                'society_name' => $this->society_name,
                'total_flats' => $this->total_flats,
                'address_1' => $this->address_1,
                'address_2' => $this->address_2,
                'pincode' => $this->pincode,
                'state' => $this->state,
                'city' => $this->city,
            ]);

            $this->dispatch('show-success', message: 'Society details updated successfully!');
        }
    }

    public function updateApartment()
    {
        $this->validate([
            'building_name' => 'required|string|max:255',
            'apartment_number' => 'required|string|max:50',
            'owner1_name' => 'required|string|max:255',
            'owner1_email' => 'nullable|string|email|max:255',
            'owner1_mobile' => 'required|digits:10',
            'owner2_name' => 'nullable|string|max:255',
            'owner2_email' => 'nullable|string|email|max:255',
            'owner2_mobile' => 'nullable|digits:10',
            'owner3_name' => 'nullable|string|max:255',
            'owner3_email' => 'nullable|string|email|max:255',
            'owner3_mobile' => 'nullable|digits:10',
            'agreement_copy' => 'nullable|file|mimes:jpeg,png,jpg,docs,pdf,csv,xls,xlsx|max:2048',
            'membership_form' => 'nullable|file|mimes:jpeg,png,jpg,docs,pdf,csv,xls,xlsx|max:2048',
            'allotment_letter' => 'nullable|file|mimes:jpeg,png,jpg,docs,pdf,csv,xls,xlsx|max:2048',
            'possession_letter' => 'nullable|file|mimes:jpeg,png,jpg,docs,pdf,csv,xls,xlsx|max:2048',

        ]);

        $apartment = SocietyDetail::find($this->selectedBuilding);
            if (!$apartment) {
                $this->dispatch('showSuccess', message: 'Apartment details not found!');
                return;
            }
            $updateData = [
                'building_name'     => $this->building_name,
                'apartment_number'  => $this->apartment_number,
                'owner1_name'       => $this->owner1_name,
                'owner1_mobile'     => $this->owner1_mobile,
                'owner1_email'      => $this->owner1_email,
                'owner2_name'       => $this->owner2_name,
                'owner2_mobile'     => $this->owner2_mobile,
                'owner2_email'      => $this->owner2_email,
                'owner3_name'       => $this->owner3_name,
                'owner3_mobile'     => $this->owner3_mobile,
                'owner3_email'      => $this->owner3_email,
            ];
            if ($this->agreement_copy) {
                $agreementfileName = time().'.'.$this->agreement_copy->getClientOriginalExtension();
                $updateData['agreementCopy'] = $this->agreement_copy->storeAs('society_docs', $agreementfileName, 'public');
            }
            if ($this->membership_form) {
                $membershipfileName = time().'.'.$this->membership_form->getClientOriginalExtension();
                $updateData['memberShipForm'] = $this->membership_form->storeAs('society_docs', $membershipfileName, 'public');
            }
            if ($this->allotment_letter) {
            $allotmentfileName = time().'.'.$this->allotment_letter->getClientOriginalExtension();
            $updateData['allotmentLetter'] = $this->allotment_letter->storeAs('society_docs', $allotmentfileName, 'public');
            }
            if ($this->possession_letter) {
            $possessionfileName = time().'.'.$this->possession_letter->getClientOriginalExtension();
            $updateData['possessionLetter'] = $this->possession_letter->storeAs('society_docs', $possessionfileName, 'public');
            }
            
            $apartment->update($updateData);
            $this->dispatch('show-success', message: 'Apartment details updated successfully!');
    }
    
    public function verifyDetails($apartmentId)
    {
        return redirect()->route('menus.update_society_status',['apartmentId'=>$apartmentId]);
    }
}
