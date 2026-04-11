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
                        <flux:heading size="md" class="mb-4">Society Information / सोसायटी माहिती</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                            <flux:input type="text" :label="__('Society Name / सोसायटीचे नाव :')"
                                wire:model="society_name" readonly />
                            <flux:input type="number" :label="__('Total No of Building / इमारतींची एकूण संख्या :')"
                                wire:model="total_building" />
                            <flux:input type="number" :label="__('Total No of Unit / एककांची एकूण संख्या :')"
                                wire:model="total_flats" />
                            <flux:textarea :label="__('Address Line 1 / पत्ता ओळ 1 :')" wire:model="address_1" />
                            <flux:textarea :label="__('Address Line 2 / पत्ता ओळ 2 :')" wire:model="address_2" />
                            <flux:input type="text" :label="__('Pincode / पिनकोड')" wire:model="pincode" />
                            <flux:select wire:model.live="state_id" placeholder="Choose State... / राज्य निवडा..."
                                :label="__('State / राज्य')">
                                @foreach ($states as $st)
                                    <flux:select.option value="{{ $st->id }}">{{ $st->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:select wire:model="city_id" placeholder="Choose City... / शहर निवडा..."
                                :label="__('City / शहर')">
                                @foreach ($cities as $ct)
                                    <flux:select.option value="{{ $ct->id }}">{{ $ct->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:input type="number" :label="__('Total No of Shares / शेअर्सची एकूण संख्या :')"
                                wire:model="no_of_shares" />
                            <flux:input type="number" :label="__('Share Value / शेअरची किंमत :')"
                                wire:model="share_value" />
                            <flux:input type="text" :label="__('Building Name / इमारतीचे नाव :')"
                                wire:model="building_name" />
                            <flux:input type="text" :label="__('Apartment Number / अपार्टमेंट क्रमांक :')"
                                wire:model="apartment_number" />
                            {{-- <flux:input type="number" :label="__('No of Each Share :')" wire:model="individual_no_of_share" /> --}}
                            {{-- <flux:input type="number" :label="__('Each Share Amount :')" wire:model="share_capital_amount" /> --}}
                            <flux:input type="text"
                                :label="__('Registration Certificate No / नोंदणी प्रमाणपत्र क्रमांक :')"
                                wire:model="certificate_no" placeholder="Enter registration number" />
                        </div>

                        <flux:separator variant="subtle" class="my-8" />

                        <flux:heading size="md" class="mb-4">Owner Information / मालक माहिती</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:input type="text" :label="__('Owner 1 Name / मालक 1 चे नाव')"
                                wire:model="owner1_name" />
                            <flux:input type="text" :label="__('Owner 1 Email / मालक 1 चा ईमेल')"
                                wire:model="owner1_email" />
                            <flux:input type="text" :label="__('Owner 1 Mobile / मालक 1 चा मोबाइल')"
                                wire:model="owner1_mobile" />

                            <flux:input type="text" :label="__('Owner 2 Name / मालक 2 चे नाव')"
                                wire:model="owner2_name" />
                            <flux:input type="text" :label="__('Owner 2 Email / मालक 2 चा ईमेल')"
                                wire:model="owner2_email" />
                            <flux:input type="text" :label="__('Owner 2 Mobile / मालक 2 चा मोबाइल')"
                                wire:model="owner2_mobile" />

                            <flux:input type="text" :label="__('Owner 3 Name / मालक 3 चे नाव')"
                                wire:model="owner3_name" />
                            <flux:input type="text" :label="__('Owner 3 Email / मालक 3 चा ईमेल')"
                                wire:model="owner3_email" />
                            <flux:input type="text" :label="__('Owner 3 Mobile / मालक 3 चा मोबाइल')"
                                wire:model="owner3_mobile" />
                        </div>
                    </div>
                </div>

                <!-- Section 2: Bylaws Cases -->
                @if ($is_byelaws_available === 'Yes')
                    <div class="card shadow-sm border-0 mb-6">
                        <div class="card-header font-bold text-lg bg-white py-4 border-b">Bylaws Cases
                        </div>
                        <div class="card-body">
                            <div class="p-4 border rounded-xl bg-gray-50/50">
                                <div class="mt-6 space-y-3 animate-in fade-in slide-in-from-top-1">
                                    <flux:radio.group wire:model.live="membership_case"
                                        :label="__('Select Membership Case / सदस्यत्व केस निवडा:')">
                                        <flux:radio value="case_a"
                                            label="Case A: Original Membership / केस ए: मूळ सदस्यत्व" />
                                        <flux:radio value="case_b" label="Case B: Transfer / केस बी: हस्तांतरण" />
                                        <flux:radio value="case_c"
                                            label="Case C: Nominee Succession / केस सी: नामनिर्देशित वारस" />
                                        <flux:radio value="case_d" label="Case D: Heir Succession / केस डी: वारस" />
                                    </flux:radio.group>

                                    @if ($membership_case === 'case_a')
                                        <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <flux:icon.user-circle variant="mini" />
                                                <flux:label class="font-bold"><u>Additional Membership Details /
                                                        अतिरिक्त सदस्यत्व तपशील</u>
                                                </flux:label>
                                            </flux:heading>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Applicant Name / अर्जदाराचे नाव')"
                                                        wire:model="applicant_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Father/Husband Name / वडिलांचे/पतीचे नाव')"
                                                        wire:model="father_husband_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Deceased Member Name / मृत सदस्याचे नाव')"
                                                        wire:model="deceased_member_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Builder Name / बिल्डरचे नाव')"
                                                        wire:model="builder_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Age / वय')" wire:model="age" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Occupation / व्यवसाय')"
                                                        wire:model="occupation" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Monthly Income / मासिक उत्पन्न')"
                                                        wire:model="monthly_income" prefix="₹" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:textarea :label="__('Residential Address / राहण्याचा पत्ता')"
                                                        wire:model="residential_addr" rows="2" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:textarea :label="__('Office Address / कार्यालयाचा पत्ता')"
                                                        wire:model="office_addr" rows="2" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input
                                                        :label="__('Flat Area (Sq. Meters) / फ्लॅट क्षेत्र (वर्ग मीटर)')"
                                                        wire:model="flat_area_sq_meters" />
                                                </flux:field>
                                            </div>

                                            <div class="space-y-4 p-4 rounded-xl bg-blue-50/30 border border-blue-100">
                                                <flux:label class="font-bold"><u>Other Particulars of
                                                        Plot/Flat/House / प्लॉट/फ्लॅट/घराचे इतर तपशील:</u></flux:label>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <flux:input :label="__('Name of Person1 / व्यक्ती 1 चे नाव')"
                                                        wire:model="other_person_name1" />
                                                    <flux:input
                                                        :label="__('Location of Plot/Flat1 / प्लॉट/फ्लॅट 1 चे स्थान')"
                                                        wire:model="other_property_location1" />
                                                    <flux:textarea
                                                        :label="__('Particulars of Owned Property1 / मालकीच्या मालमत्तेचे तपशील 1')"
                                                        wire:model="other_property_particulars1"
                                                        placeholder="Owned by applicant or family members"
                                                        rows="2" />
                                                    <flux:textarea
                                                        :label="__('Reason for necessity of flat1 / फ्लॅट 1 च्या गरजेचे कारण')"
                                                        wire:model="reason_for_flat1" rows="2" />
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <flux:input :label="__('Name of Person2 / व्यक्ती 2 चे नाव')"
                                                        wire:model="other_person_name2" />
                                                    <flux:input
                                                        :label="__('Location of Plot/Flat2 / प्लॉट/फ्लॅट 2 चे स्थान')"
                                                        wire:model="other_property_location2" />
                                                    <flux:textarea
                                                        :label="__('Particulars of Owned Property2 / मालकीच्या मालमत्तेचे तपशील 2')"
                                                        wire:model="other_property_particulars2"
                                                        placeholder="Owned by applicant or family members"
                                                        rows="2" />
                                                    <flux:textarea
                                                        :label="__('Reason for necessity of flat2 / फ्लॅट 2 च्या गरजेचे कारण')"
                                                        wire:model="reason_for_flat2" rows="2" />
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($membership_case === 'case_b')
                                        <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <flux:icon.arrows-right-left variant="mini" />
                                                <u>Property Transfer Details (Appendix 20-1 & 20-2 and Appendix 21) /
                                                    मालमत्तेचे हस्तांतरण तपशील (परिशिष्ट 20-1 आणि 20-2 आणि परिशिष्ट
                                                    21)</u>
                                            </flux:heading>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Transferee Name / हस्तांतरिताचे नाव')"
                                                        wire:model="transferee_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Transferor Name / हस्तांतरकर्त्याचे नाव')"
                                                        wire:model="transferor_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input
                                                        :label="__('Distinctive No From / विशिष्ट क्रमांक पासून')"
                                                        wire:model="distinctive_no_from" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input
                                                        :label="__('Distinctive No To / विशिष्ट क्रमांक पर्यंत')"
                                                        wire:model="distinctive_no_to" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Building No / इमारत क्रमांक')"
                                                        wire:model="building_no" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input
                                                        :label="__('Flat Area (Sq. Meters) / फ्लॅट क्षेत्र (वर्ग मीटर)')"
                                                        wire:model="flat_area_sq_meters" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Transfer Fee / हस्तांतरण शुल्क')"
                                                        wire:model="transfer_fee" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input
                                                        :label="__('Transfer Premium Amount / हस्तांतरण प्रीमियम रक्कम')"
                                                        wire:model="transfer_premium_amount" />
                                                </flux:field>
                                            </div>
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <u>Grounds for Transfer / हस्तांतरणाचे कारण</u>
                                            </flux:heading>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Ground 1 / कारण 1')"
                                                        wire:model="transfer_ground_1" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Ground 2 / कारण 2')"
                                                        wire:model="transfer_ground_2" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Ground 3 / कारण 3')"
                                                        wire:model="transfer_ground_3" />
                                                </flux:field>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($membership_case === 'case_c')
                                        <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <flux:icon.user-minus variant="mini" class="text-indigo-500" />
                                                <u>Nominee Succession Details (Appendix 15) / नामनिर्देशित वारस तपशील
                                                    (परिशिष्ट 15)</u>
                                            </flux:heading>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Applicant Name / अर्जदाराचे नाव')"
                                                        wire:model="applicant_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Deceased Member Name / मृत सदस्याचे नाव')"
                                                        wire:model="deceased_member_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="date"
                                                        :label="__('Date of Death / मृत्यूची तारीख')"
                                                        wire:model="date_of_death" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="number"
                                                        :label="__('No of Shares / शेअर्सची संख्या')"
                                                        wire:model="society_shares" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Occupation / व्यवसाय')"
                                                        wire:model="occupation" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Age / वय')" wire:model="age" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Monthly Income / मासिक उत्पन्न')"
                                                        wire:model="monthly_income" prefix="₹" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:textarea :label="__('Residential Address / राहण्याचा पत्ता')"
                                                        wire:model="residential_addr" rows="2" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:textarea :label="__('Office Address / कार्यालयाचा पत्ता')"
                                                        wire:model="office_addr" rows="2" />
                                                </flux:field>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($membership_case === 'case_d')
                                        <div class="mt-8 space-y-6 pt-6 border-t border-gray-100">
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <flux:icon.user-minus variant="mini" />
                                                <u>Heir Succession Details (Appendix 16 & 19) / वारस तपशील (परिशिष्ट 16
                                                    आणि 19)</u>
                                            </flux:heading>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Applicant Name / अर्जदाराचे नाव')"
                                                        wire:model="applicant_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Deceased Member Name / मृत सदस्याचे नाव')"
                                                        wire:model="deceased_member_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Father/Husband Name / वडिलांचे/पतीचे नाव')"
                                                        wire:model="father_husband_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="date"
                                                        :label="__('Date of Death / मृत्यूची तारीख')"
                                                        wire:model="date_of_death" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="text"
                                                        :label="__('Residential Address / राहण्याचा पत्ता')"
                                                        wire:model="residential_addr" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input
                                                        :label="__('Distinctive No From / विशिष्ट क्रमांक पासून')"
                                                        wire:model="distinctive_no_from" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input
                                                        :label="__('Distinctive No To / विशिष्ट क्रमांक पर्यंत')"
                                                        wire:model="distinctive_no_to" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="time"
                                                        :label="__('Inspection From / तपासणी पासून')"
                                                        wire:model="inspection_time_from" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input type="time"
                                                        :label="__('Inspection To / तपासणी पर्यंत')"
                                                        wire:model="inspection_time_to" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Floor No / मजला क्रमांक')"
                                                        wire:model="floor_no" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Flat Bearing No / फ्लॅट बेअरिंग क्रमांक')"
                                                        wire:model="flat_bearing_no" />
                                                </flux:field>
                                            </div>
                                            <flux:heading size="sm" class="flex items-center gap-2">
                                                <u>Heir Details / वारस तपशील</u>
                                            </flux:heading>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Heir 1 Name / वारस 1 चे नाव')"
                                                        wire:model="heir_1_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Heir 2 Name / वारस 2 चे नाव')"
                                                        wire:model="heir_2_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Heir 3 Name / वारस 3 चे नाव')"
                                                        wire:model="heir_3_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Heir 4 Name / वारस 4 चे नाव')"
                                                        wire:model="heir_4_name" />
                                                </flux:field>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <flux:field>
                                                    <flux:input :label="__('Witness Name / साक्षीदाराचे नाव')"
                                                        wire:model="witness_name" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:input :label="__('Witness Address / साक्षीदाराचा पत्ता')"
                                                        wire:model="witness_address" />
                                                </flux:field>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Case Specific Uploads --}}
                                    @php
                                        $specializedLabelsMap = [
                                            'case_a' => [
                                                'allotmentMembershipLetter' => 'Allotment Letter / वाटप पत्र',
                                            ],
                                            'case_b' => [
                                                'stampDutyProof' => 'Stamp Duty Proof / स्टॅंप ड्यूटी पुरावा',
                                                'transferorSignature' => 'Transferor Signature / हस्तांतरकर्त्याची सही',
                                            ],
                                            'case_c' => [
                                                'deathCertificate' => 'Death Certificate / मृत्यू प्रमाणपत्र',
                                                'nominationRecord' => 'Nomination Record / नामनिर्देशन रेकॉर्ड',
                                            ],
                                            'case_d' => [
                                                'successionCertificate' => 'Succession Cert/Heirship / वारस प्रमाणपत्र',
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
                                                / केस-विशिष्ट दस्तऐवज अपलोड
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
                                'agreementCopy' => 'Agreement Copy / कराराची प्रत',
                                'memberShipForm' => 'MemberShip Form / सदस्यत्व फॉर्म',
                                'allotmentLetter' => 'Allotment Letter / वाटप पत्र',
                                'possessionLetter' => 'Possession Letter / ताबा पत्र',
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
