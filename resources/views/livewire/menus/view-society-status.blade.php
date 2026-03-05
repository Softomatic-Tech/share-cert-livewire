<section>
    <div class="w-full">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('user.dashboard') }}">User</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="#">View Apartment Details</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:separator variant="subtle" />

        <div class="rounded-lg shadow-lg py-4 px-6">
            <!-- Section 1: Society & Owner Details -->
            <div class="card shadow-sm border-0 mb-6">
                <div class="card-header font-bold text-lg bg-white py-4 border-b">Section 1: Society & Owner Details</div>
                <div class="card-body">
                    <flux:heading size="md" class="mb-4">Society Information</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <flux:field>
                            <flux:label>Society Name</flux:label>
                            <flux:text>{{ $society_name }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Total No of Building</flux:label>
                            <flux:text>{{ $total_building }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Total No of Unit</flux:label>
                            <flux:text>{{ $total_flats }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Address Line 1</flux:label>
                            <flux:text>{{ $address_1 }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Address Line 2</flux:label>
                            <flux:text>{{ $address_2 }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Pincode</flux:label>
                            <flux:text>{{ $pincode }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>State</flux:label>
                            <flux:text>{{ $state }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>City</flux:label>
                            <flux:text>{{ $city }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Total No of Shares</flux:label>
                            <flux:text>{{ $no_of_shares }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Share Value</flux:label>
                            <flux:text>{{ $share_value }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Building Name</flux:label>
                            <flux:text>{{ $building_name }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Apartment Number</flux:label>
                            <flux:text>{{ $apartment_number }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>No of Each Share</flux:label>
                            <flux:text>{{ $individual_no_of_share }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Each Share Amount</flux:label>
                            <flux:text>₹{{ number_format($share_capital_amount) }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Registration Certificate No</flux:label>
                            <flux:text>{{ $certificate_no }}</flux:text>
                        </flux:field>
                    </div>

                    <flux:separator variant="subtle" class="my-8" />

                    <flux:heading size="md" class="mb-4">Owner Information</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Owner 1 Name</flux:label>
                            <flux:text>{{ $owner1_name }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Owner 1 Email</flux:label>
                            <flux:text>{{ $owner1_email }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Owner 1 Mobile</flux:label>
                            <flux:text>{{ $owner1_mobile }}</flux:text>
                        </flux:field>
                        
                        @if($owner2_name)
                        <flux:field>
                            <flux:label>Owner 2 Name</flux:label>
                            <flux:text>{{ $owner2_name }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Owner 2 Email</flux:label>
                            <flux:text>{{ $owner2_email }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Owner 2 Mobile</flux:label>
                            <flux:text>{{ $owner2_mobile }}</flux:text>
                        </flux:field>
                        @endif
                        
                        @if($owner3_name)
                        <flux:field>
                            <flux:label>Owner 3 Name</flux:label>
                            <flux:text>{{ $owner3_name }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Owner 3 Email</flux:label>
                            <flux:text>{{ $owner3_email }}</flux:text>
                        </flux:field>
                        <flux:field>
                            <flux:label>Owner 3 Mobile</flux:label>
                            <flux:text>{{ $owner3_mobile }}</flux:text>
                        </flux:field>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section 2: Bylaws Cases -->
            <div class="card shadow-sm border-0 mb-6">
                <div class="card-header font-bold text-lg bg-white py-4 border-b">Section 2: Bylaws Cases</div>
                <div class="card-body">
                    <div class="p-4 border rounded-xl bg-gray-50/50">
                        <flux:field>
                            <flux:label>Is bye laws available?</flux:label>
                            <flux:text>{{ ucfirst($is_byelaws_available) }}</flux:text>
                        </flux:field>

                        @if($is_byelaws_available === 'yes')
                        <div class="mt-6 space-y-3">
                            <flux:field>
                                <flux:label>Membership Case:</flux:label>
                                <flux:text>
                                    @switch($membership_case)
                                        @case('case_a') Case A: Original Membership @break
                                        @case('case_b') Case B: Transfer @break
                                        @case('case_c') Case C: Nominee Succession @break
                                        @case('case_d') Case D: Heir Succession @break
                                    @endswitch
                                </flux:text>
                            </flux:field>

                            @if($membership_case === 'case_a')
                                <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                    <flux:heading size="sm">Additional Membership Details</flux:heading>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <flux:field><flux:label>Applicant Name</flux:label><flux:text>{{ $applicant_name }}</flux:text></flux:field>
                                        <flux:field><flux:label>Father/Husband Name</flux:label><flux:text>{{ $father_husband_name }}</flux:text></flux:field>
                                        <flux:field><flux:label>Deceased Member Name</flux:label><flux:text>{{ $deceased_member_name }}</flux:text></flux:field>
                                        <flux:field><flux:label>Builder Name</flux:label><flux:text>{{ $builder_name }}</flux:text></flux:field>
                                        <flux:field><flux:label>Age</flux:label><flux:text>{{ $age }}</flux:text></flux:field>
                                        <flux:field><flux:label>Occupation</flux:label><flux:text>{{ $occupation }}</flux:text></flux:field>
                                        <flux:field><flux:label>Monthly Income</flux:label><flux:text>₹{{ number_format($monthly_income) }}</flux:text></flux:field>
                                        <flux:field><flux:label>Residential Address</flux:label><flux:text>{{ $residential_addr }}</flux:text></flux:field>
                                        <flux:field><flux:label>Office Address</flux:label><flux:text>{{ $office_addr }}</flux:text></flux:field>
                                        <flux:field><flux:label>Flat Area (Sq. Meters)</flux:label><flux:text>{{ $flat_area_sq_meters }}</flux:text></flux:field>
                                    </div>
                                    <div class="space-y-4 p-4 rounded-xl bg-blue-50/30 border border-blue-100">
                                        <flux:label class="font-bold">Other Particulars of Plot/Flat/House:</flux:label>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <flux:label>Person 1 Details</flux:label>
                                                <flux:text>{{ $other_person_name1 }} - {{ $other_property_location1 }}</flux:text>
                                                <p class="text-sm text-gray-500 mt-1">{{ $other_property_particulars1 }}</p>
                                                <p class="text-xs italic mt-1">Reason: {{ $reason_for_flat1 }}</p>
                                            </div>
                                            @if($other_person_name2)
                                            <div>
                                                <flux:label>Person 2 Details</flux:label>
                                                <flux:text>{{ $other_person_name2 }} - {{ $other_property_location2 }}</flux:text>
                                                <p class="text-sm text-gray-500 mt-1">{{ $other_property_particulars2 }}</p>
                                                <p class="text-xs italic mt-1">Reason: {{ $reason_for_flat2 }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($membership_case === 'case_b')
                                <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                    <flux:heading size="sm">Property Transfer Details</flux:heading>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <flux:field><flux:label>Transferee Name</flux:label><flux:text>{{ $transferee_name }}</flux:text></flux:field>
                                        <flux:field><flux:label>Transferor Name</flux:label><flux:text>{{ $transferor_name }}</flux:text></flux:field>
                                        <flux:field><flux:label>Distinctive No</flux:label><flux:text>{{ $distinctive_no_from }} to {{ $distinctive_no_to }}</flux:text></flux:field>
                                        <flux:field><flux:label>Building No</flux:label><flux:text>{{ $building_no }}</flux:text></flux:field>
                                        <flux:field><flux:label>Flat Area (Sq. Meters)</flux:label><flux:text>{{ $flat_area_sq_meters }}</flux:text></flux:field>
                                        <flux:field><flux:label>Transfer Fee</flux:label><flux:text>₹{{ number_format($transfer_fee) }}</flux:text></flux:field>
                                        <flux:field><flux:label>Transfer Premium</flux:label><flux:text>₹{{ number_format($transfer_premium_amount) }}</flux:text></flux:field>
                                    </div>
                                    <div class="space-y-2">
                                        <flux:label class="font-bold">Grounds for Transfer:</flux:label>
                                        <ul class="list-disc list-inside text-sm text-gray-600">
                                            @if($transfer_ground_1) <li>{{ $transfer_ground_1 }}</li> @endif
                                            @if($transfer_ground_2) <li>{{ $transfer_ground_2 }}</li> @endif
                                            @if($transfer_ground_3) <li>{{ $transfer_ground_3 }}</li> @endif
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            @if($membership_case === 'case_c')
                                <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                    <flux:heading size="sm">Nominee Succession Details</flux:heading>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <flux:field><flux:label>Applicant Name</flux:label><flux:text>{{ $applicant_name }}</flux:text></flux:field>
                                        <flux:field><flux:label>Deceased Member</flux:label><flux:text>{{ $deceased_member_name }}</flux:text></flux:field>
                                        <flux:field><flux:label>Date of Death</flux:label><flux:text>{{ $date_of_death }}</flux:text></flux:field>
                                        <flux:field><flux:label>No of Shares</flux:label><flux:text>{{ $society_shares }}</flux:text></flux:field>
                                        <flux:field><flux:label>Occupation</flux:label><flux:text>{{ $occupation }}</flux:text></flux:field>
                                        <flux:field><flux:label>Age</flux:label><flux:text>{{ $age }}</flux:text></flux:field>
                                        <flux:field><flux:label>Monthly Income</flux:label><flux:text>₹{{ number_format($monthly_income) }}</flux:text></flux:field>
                                        <flux:field><flux:label>Residential Address</flux:label><flux:text>{{ $residential_addr }}</flux:text></flux:field>
                                        <flux:field><flux:label>Office Address</flux:label><flux:text>{{ $office_addr }}</flux:text></flux:field>
                                    </div>
                                </div>
                            @endif

                            @if($membership_case === 'case_d')
                                <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                    <flux:heading size="sm">Heir Succession Details</flux:heading>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <flux:field><flux:label>Applicant Name</flux:label><flux:text>{{ $applicant_name }}</flux:text></flux:field>
                                        <flux:field><flux:label>Deceased Member</flux:label><flux:text>{{ $deceased_member_name }}</flux:text></flux:field>
                                        <flux:field><flux:label>Father/Husband Name</flux:label><flux:text>{{ $father_husband_name }}</flux:text></flux:field>
                                        <flux:field><flux:label>Date of Death</flux:label><flux:text>{{ $date_of_death }}</flux:text></flux:field>
                                        <flux:field><flux:label>Distinctive No</flux:label><flux:text>{{ $distinctive_no_from }} to {{ $distinctive_no_to }}</flux:text></flux:field>
                                        <flux:field><flux:label>Residential Address</flux:label><flux:text>{{ $residential_addr }}</flux:text></flux:field>
                                        <flux:field><flux:label>Floor No</flux:label><flux:text>{{ $floor_no }}</flux:text></flux:field>
                                        <flux:field><flux:label>Flat Bearing No</flux:label><flux:text>{{ $flat_bearing_no }}</flux:text></flux:field>
                                        <flux:field><flux:label>Inspection Time</flux:label><flux:text>{{ $inspection_time_from }} to {{ $inspection_time_to }}</flux:text></flux:field>
                                    </div>
                                    <div class="space-y-2">
                                        <flux:label class="font-bold">Heir Details:</flux:label>
                                        <div class="flex flex-wrap gap-4 text-sm">
                                            @if($heir_1_name)<span class="bg-gray-100 px-3 py-1 rounded-full">{{ $heir_1_name }}</span>@endif
                                            @if($heir_2_name)<span class="bg-gray-100 px-3 py-1 rounded-full">{{ $heir_2_name }}</span>@endif
                                            @if($heir_3_name)<span class="bg-gray-100 px-3 py-1 rounded-full">{{ $heir_3_name }}</span>@endif
                                            @if($heir_4_name)<span class="bg-gray-100 px-3 py-1 rounded-full">{{ $heir_4_name }}</span>@endif
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-gray-50">
                                        <flux:field>
                                            <flux:label>Witness Name</flux:label>
                                            <flux:text>{{ $witness_name }}</flux:text>
                                        </flux:field>
                                        <flux:field>
                                            <flux:label>Witness Address</flux:label>
                                            <flux:text>{{ $witness_address }}</flux:text>
                                        </flux:field>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Case Specific Uploads (View Mode) --}}
                            @php
                                $specializedLabelsMap = [
                                    'case_a' => ['allotmentMembershipLetter' => 'Allotment Letter'],
                                    'case_b' => ['stampDutyProof' => 'Stamp Duty Proof', 'transferorSignature' => 'Transferor Signature'],
                                    'case_c' => ['deathCertificate' => 'Death Certificate', 'nominationRecord' => 'Nomination Record'],
                                    'case_d' => ['successionCertificate' => 'Succession Cert/Heirship'],
                                ];
                                $currentSpecializedLabels = $specializedLabelsMap[$membership_case] ?? [];
                            @endphp

                            @if(count($currentSpecializedLabels) > 0)
                                <div class="mt-8 pt-6 border-t border-gray-100">
                                    <flux:heading size="sm" class="mb-4">Case Specific Documents</flux:heading>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($currentSpecializedLabels as $col => $label)
                                            @if($this->$col)
                                                <div class="flex items-center justify-between p-3 border rounded-xl bg-white shadow-sm">
                                                    <div class="flex items-center gap-3">
                                                        <i class="fa-solid fa-file-pdf text-red-500 text-xl"></i>
                                                        <span class="text-sm font-medium">{{ $label }}</span>
                                                    </div>
                                                    <flux:button variant="ghost" size="sm" icon="eye" href="{{ asset('storage/society_docs/'.$this->$col) }}" target="_blank">View</flux:button>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section 3: Standard Document Uploads -->
            <div class="card shadow-sm border-0 mb-6">
                <div class="card-header font-bold text-lg bg-white py-4 border-b">Section 3: Standard Documents</div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $standardDocs = [
                                'agreementCopy' => 'Agreement Copy',
                                'memberShipForm' => 'Membership Form',
                                'allotmentLetter' => 'Allotment Letter',
                                'possessionLetter' => 'Possession Letter',
                            ];
                        @endphp
                        @foreach($standardDocs as $col => $label)
                            @if($this->$col)
                                <div class="flex items-center justify-between p-3 border rounded-xl bg-white shadow-sm">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid fa-file-pdf text-red-500 text-xl"></i>
                                        <span class="text-sm font-medium">{{ $label }}</span>
                                    </div>
                                    <flux:button variant="ghost" size="sm" icon="eye" href="{{ asset('storage/society_docs/'.$this->$col) }}" target="_blank">View</flux:button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-start mt-8 pb-8">
                <flux:button href="{{ route('user.dashboard') }}" icon="chevron-left">Back to Dashboard</flux:button>
            </div>
        </div>
    </div>
</section>
