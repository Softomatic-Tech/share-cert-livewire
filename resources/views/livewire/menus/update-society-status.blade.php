<section>
    <div class="w-full">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('user.dashboard') }}">User</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="#">Verify Society Details</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:separator variant="subtle" />

        <div class="rounded-lg shadow-lg py-4 px-6">
            <div class="mb-2">
                <livewire:menus.alerts />
            </div>

            <form wire:submit.prevent="updateSocietyDetails">
                <!-- Section 1: Society & Owner Details -->
                <div class="card shadow-sm border-0 mb-6">
                    <div class="card-header font-bold text-lg bg-white py-4 border-b">Society & Owner Details</div>
                    <div class="card-body">
                        <flux:heading size="md" class="mb-4">Society Information</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                            <flux:input type="text" :label="__('Society Name :')" wire:model="society_name"
                                readonly />
                            <flux:input type="number" :label="__('Total No of Building :')"
                                wire:model="total_building" />
                            <flux:input type="number" :label="__('Total No of Unit :')" wire:model="total_flats" />
                            <flux:textarea :label="__('Address Line 1 :')" wire:model="address_1" />
                            <flux:textarea :label="__('Address Line 2 :')" wire:model="address_2" />
                            <flux:input type="text" :label="__('Pincode')" wire:model="pincode" />
                            <flux:select wire:model.live="state_id" placeholder="Choose State..."
                                :label="__('State')">
                                @foreach ($states as $st)
                                    <flux:select.option value="{{ $st->id }}">{{ $st->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:select wire:model="city_id" placeholder="Choose City..." :label="__('City')">
                                @foreach ($cities as $ct)
                                    <flux:select.option value="{{ $ct->id }}">{{ $ct->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:input type="number" :label="__('Total No of Shares :')" wire:model="no_of_shares" />
                            <flux:input type="number" :label="__('Share Value :')" wire:model="share_value" />
                            <flux:input type="text" :label="__('Building Name :')" wire:model="building_name" />
                            <flux:input type="text" :label="__('Apartment Number :')"
                                wire:model="apartment_number" />
                            {{-- <flux:input type="number" :label="__('No of Each Share :')" wire:model="individual_no_of_share" /> --}}
                            {{-- <flux:input type="number" :label="__('Each Share Amount :')" wire:model="share_capital_amount" /> --}}
                            <flux:input type="text" :label="__('Registration Certificate No :')"
                                wire:model="certificate_no" placeholder="Enter registration number" />
                        </div>

                        <flux:separator variant="subtle" class="my-8" />

                        <flux:heading size="md" class="mb-4">Owner Information</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:input type="text" :label="__('Owner 1 Name')" wire:model="owner1_name" />
                            <flux:input type="text" :label="__('Owner 1 Email')" wire:model="owner1_email" />
                            <flux:input type="text" :label="__('Owner 1 Mobile')" wire:model="owner1_mobile" />

                            <flux:input type="text" :label="__('Owner 2 Name')" wire:model="owner2_name" />
                            <flux:input type="text" :label="__('Owner 2 Email')" wire:model="owner2_email" />
                            <flux:input type="text" :label="__('Owner 2 Mobile')" wire:model="owner2_mobile" />

                            <flux:input type="text" :label="__('Owner 3 Name')" wire:model="owner3_name" />
                            <flux:input type="text" :label="__('Owner 3 Email')" wire:model="owner3_email" />
                            <flux:input type="text" :label="__('Owner 3 Mobile')" wire:model="owner3_mobile" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Bylaws Cases -->
                @if ($is_byelaws_available === 'yes')
                    <div class="card shadow-sm border-0 mb-6">
                        <div class="card-header font-bold text-lg bg-white py-4 border-b">Bylaws Cases</div>
                        <div class="card-body">
                            <div class="p-4 border rounded-xl bg-gray-50/50">
                                <div class="mt-6 space-y-3 animate-in fade-in slide-in-from-top-1">
                                    <flux:radio.group wire:model.live="membership_case"
                                        :label="__('Select Membership Case:')">
                                        <flux:radio value="case_a" label="Case A: Original Membership" />
                                        <flux:radio value="case_b" label="Case B: Transfer" />
                                        <flux:radio value="case_c" label="Case C: Nominee Succession" />
                                        <flux:radio value="case_d" label="Case D: Heir Succession" />
                                    </flux:radio.group>

                                    @if ($membership_case === 'case_a')
                                        <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <flux:icon.user-circle variant="mini" />
                                                <flux:label class="font-bold"><u>Additional Membership Details</u>
                                                </flux:label>
                                            </flux:heading>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Applicant Name')"
                                                        wire:model="applicant_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Father/Husband Name')"
                                                        wire:model="father_husband_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Deceased Member Name')"
                                                        wire:model="deceased_member_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Builder Name')"
                                                        wire:model="builder_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Age')" wire:model="age" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Occupation')" wire:model="occupation" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Monthly Income')"
                                                        wire:model="monthly_income" prefix="₹" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:textarea :label="__('Residential Address')"
                                                        wire:model="residential_addr" rows="2" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:textarea :label="__('Office Address')"
                                                        wire:model="office_addr" rows="2" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Flat Area (Sq. Meters)')"
                                                        wire:model="flat_area_sq_meters" />
                                                </flux:field>
                                            </div>

                                            <div class="space-y-4 p-4 rounded-xl bg-blue-50/30 border border-blue-100">
                                                <flux:label class="font-bold"><u>Other Particulars of
                                                        Plot/Flat/House:</u></flux:label>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <flux:input :label="__('Name of Person1')"
                                                        wire:model="other_person_name1" />
                                                    <flux:input :label="__('Location of Plot/Flat1')"
                                                        wire:model="other_property_location1" />
                                                    <flux:textarea :label="__('Particulars of Owned Property1')"
                                                        wire:model="other_property_particulars1"
                                                        placeholder="Owned by applicant or family members"
                                                        rows="2" />
                                                    <flux:textarea :label="__('Reason for necessity of flat1')"
                                                        wire:model="reason_for_flat1" rows="2" />
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <flux:input :label="__('Name of Person2')"
                                                        wire:model="other_person_name2" />
                                                    <flux:input :label="__('Location of Plot/Flat2')"
                                                        wire:model="other_property_location2" />
                                                    <flux:textarea :label="__('Particulars of Owned Property2')"
                                                        wire:model="other_property_particulars2"
                                                        placeholder="Owned by applicant or family members"
                                                        rows="2" />
                                                    <flux:textarea :label="__('Reason for necessity of flat2')"
                                                        wire:model="reason_for_flat2" rows="2" />
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($membership_case === 'case_b')
                                        <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <flux:icon.arrows-right-left variant="mini" />
                                                <u>Property Transfer Details (Appendix 20-1 & 20-2 and Appendix 21)</u>
                                            </flux:heading>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Transferee Name')"
                                                        wire:model="transferee_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Transferor Name')"
                                                        wire:model="transferor_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Distinctive No From')"
                                                        wire:model="distinctive_no_from" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Distinctive No To')"
                                                        wire:model="distinctive_no_to" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Building No')" wire:model="building_no" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Flat Area (Sq. Meters)')"
                                                        wire:model="flat_area_sq_meters" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Transfer Fee')"
                                                        wire:model="transfer_fee" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Transfer Premium Amount')"
                                                        wire:model="transfer_premium_amount" />
                                                </flux:field>
                                            </div>
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <u>Grounds for Transfer</u>
                                            </flux:heading>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Ground 1')"
                                                        wire:model="transfer_ground_1" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Ground 2')"
                                                        wire:model="transfer_ground_2" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Ground 3')"
                                                        wire:model="transfer_ground_3" />
                                                </flux:field>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($membership_case === 'case_c')
                                        <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <flux:icon.user-minus variant="mini" class="text-indigo-500" />
                                                <u>Nominee Succession Details (Appendix 15)</u>
                                            </flux:heading>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Applicant Name')"
                                                        wire:model="applicant_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Deceased Member Name')"
                                                        wire:model="deceased_member_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="date" :label="__('Date of Death')"
                                                        wire:model="date_of_death" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="number" :label="__('No of Shares')"
                                                        wire:model="society_shares" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Occupation')" wire:model="occupation" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Age')" wire:model="age" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Monthly Income')"
                                                        wire:model="monthly_income" prefix="₹" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:textarea :label="__('Residential Address')"
                                                        wire:model="residential_addr" rows="2" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:textarea :label="__('Office Address')"
                                                        wire:model="office_addr" rows="2" />
                                                </flux:field>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($membership_case === 'case_d')
                                        <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <flux:icon.user-minus variant="mini" />
                                                <u>Heir Succession Details (Appendix 16 & 19)</u>
                                            </flux:heading>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Applicant Name')"
                                                        wire:model="applicant_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Deceased Member Name')"
                                                        wire:model="deceased_member_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Father/Husband Name')"
                                                        wire:model="father_husband_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="date" :label="__('Date of Death')"
                                                        wire:model="date_of_death" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="text" :label="__('Residential Address')"
                                                        wire:model="residential_addr" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Distinctive No From')"
                                                        wire:model="distinctive_no_from" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Distinctive No To')"
                                                        wire:model="distinctive_no_to" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="time" :label="__('Inspection From')"
                                                        wire:model="inspection_time_from" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="time" :label="__('Inspection To')"
                                                        wire:model="inspection_time_to" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Floor No')" wire:model="floor_no" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Flat Bearing No')"
                                                        wire:model="flat_bearing_no" />
                                                </flux:field>
                                            </div>
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <u>Heir Details</u>
                                            </flux:heading>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Heir 1 Name')" wire:model="heir_1_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Heir 2 Name')" wire:model="heir_2_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Heir 3 Name')" wire:model="heir_3_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Heir 4 Name')" wire:model="heir_4_name" />
                                                </flux:field>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Witness Name')"
                                                        wire:model="witness_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Witness Address')"
                                                        wire:model="witness_address" />
                                                </flux:field>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Case Specific Uploads --}}
                                    @php
                                        $specializedLabelsMap = [
                                            'case_a' => [
                                                'allotmentMembershipLetter' => 'Allotment Letter',
                                            ],
                                            'case_b' => [
                                                'stampDutyProof' => 'Stamp Duty Proof',
                                                'transferorSignature' => 'Transferor Signature',
                                            ],
                                            'case_c' => [
                                                'deathCertificate' => 'Death Certificate',
                                                'nominationRecord' => 'Nomination Record',
                                            ],
                                            'case_d' => [
                                                'successionCertificate' => 'Succession Cert/Heirship',
                                            ],
                                        ];
                                        $currentSpecializedLabels = $specializedLabelsMap[$membership_case] ?? [];
                                        $specializedColumns = [
                                            'allotmentMembershipLetter',
                                            'stampDutyProof',
                                            'transferorSignature',
                                            'deathCertificate',
                                            'nominationRecord',
                                            'successionCertificate',
                                        ];
                                    @endphp

                                    @if (count($currentSpecializedLabels) > 0)
                                        <div class="mt-8 pt-6 border-t border-gray-100">
                                            <flux:heading size="sm" class="mb-4">Case Specific Document Uploads
                                            </flux:heading>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                @foreach ($specializedColumns as $col)
                                                    @php
                                                        $label = $currentSpecializedLabels[$col] ?? null;
                                                        if (!$label) {
                                                            continue;
                                                        }
                                                        $existingFile = $this->$col;
                                                        $newFileProp = 'new' . ucfirst($col);
                                                        $uploadMethod = 'upload' . ucfirst($col);
                                                    @endphp
                                                    <div class="p-4 border rounded-xl bg-gray-50/30">
                                                        <flux:label class="font-bold mb-2 block">{{ $label }}
                                                        </flux:label>
                                                        <div class="space-y-3"
                                                            wire:key="spec-file-{{ $col }}-{{ $fileKey }}">
                                                            <flux:input type="file"
                                                                wire:model="{{ $newFileProp }}" />
                                                            @error($newFileProp)
                                                                <p class="text-red-500 text-xs">{{ $message }}</p>
                                                            @enderror

                                                            <div class="flex gap-2">
                                                                @if ($existingFile)
                                                                    <flux:button variant="primary" size="sm"
                                                                        icon="eye"
                                                                        href="{{ asset('storage/society_docs/' . $existingFile) }}"
                                                                        target="_blank">View {{ $label }}
                                                                    </flux:button>
                                                                @endif

                                                                {{-- @if ($this->$newFileProp)
                                                                <flux:button variant="primary" size="sm" wire:click="{{ $uploadMethod }}" wire:loading.attr="disabled" wire:target="{{ $newFileProp }}, {{ $uploadMethod }}">
                                                                    Upload
                                                                </flux:button>
                                                            @endif --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Section 3: Document Uploads -->
                <div class="card shadow-sm border-0 mb-6">
                    <div class="card-header font-bold text-lg bg-white py-4 border-b">Standard Document Uploads</div>
                    <div class="card-body">
                        @php
                            $currentStandardLabels = [
                                'agreementCopy' => 'Agreement Copy',
                                'memberShipForm' => 'MemberShip Form',
                                'allotmentLetter' => 'Allotment Letter',
                                'possessionLetter' => 'Possession Letter',
                            ];
                            $standardColumns = [
                                'agreementCopy',
                                'memberShipForm',
                                'allotmentLetter',
                                'possessionLetter',
                            ];
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach ($standardColumns as $col)
                                @php
                                    $label = $currentStandardLabels[$col];
                                    $existingFile = $this->$col;
                                    $newFileProp = 'new' . ucfirst($col);
                                    $uploadMethod = 'upload' . ucfirst($col);
                                @endphp
                                <div class="p-4 border rounded-xl">
                                    <flux:label class="font-bold mb-2 block">{{ $label }}</flux:label>
                                    <div class="space-y-3"
                                        wire:key="std-file-{{ $col }}-{{ $fileKey }}">
                                        <flux:input type="file" wire:model="{{ $newFileProp }}" />
                                        @error($newFileProp)
                                            <p class="text-red-500 text-xs">{{ $message }}</p>
                                        @enderror

                                        <div class="flex gap-2">
                                            @if ($existingFile)
                                                <flux:button variant="primary" size="sm" icon="eye"
                                                    href="{{ asset('storage/society_docs/' . $existingFile) }}"
                                                    target="_blank">View {{ $label }}</flux:button>
                                            @endif

                                            {{-- @if ($this->$newFileProp)
                                                <flux:button variant="primary" size="sm" wire:click="{{ $uploadMethod }}" wire:loading.attr="disabled" wire:target="{{ $newFileProp }}, {{ $uploadMethod }}">
                                                    Upload
                                                </flux:button>
                                            @endif --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-8 pb-8">
                    <flux:button variant="primary" type="submit">Verify & Submit</flux:button>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('scroll-to-top', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>
