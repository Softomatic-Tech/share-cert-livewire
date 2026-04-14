<section>
    <div class="mb-1 w-full">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="#">Super Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="#">Create Society</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:separator />
    </div>

    <div class="rounded-lg shadow-lg px-6 py-2">
        <div class="stepwizard">
            <div class="stepwizard-step">
                <button type="button" @if ($currentStep == 1) disabled @endif>1</button>
                <p @if ($currentStep == 1) disabled @endif>Basic </p>
            </div>
            <div class="stepwizard-step">
                <button type="button" @if ($currentStep == 2) disabled @endif>2</button>
                <p @if ($currentStep == 2) disabled @endif>Flat Details</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" @if ($currentStep == 3) disabled @endif>3</button>
                <p @if ($currentStep == 3) disabled @endif>Verification</p>
            </div>
        </div>
        <div class="py-2">
            <livewire:menus.alerts />
        </div>
        <!-- Form Section -->
        @if ($currentStep == 1)
            <form wire:submit.prevent="nextStep">
                <div class="step-one">
                    <div class="card">
                        <div class="card-header">Step 1: Basic Information</div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-2">
                                <flux:input type="text" :label="__('Society Name / सोसायटीचे नाव :')"
                                    wire:model="formData.society_name" />
                                <flux:input type="number" :label="__('Total No Of Building / इमारतींची एकूण संख्या :')"
                                    wire:model="formData.total_building" />
                                <flux:input type="number" :label="__('Total No Of Units / एककांची एकूण संख्या :')"
                                    wire:model="formData.total_flats" />
                                <flux:textarea :label="__('Address Line 1 / पत्ता ओळ 1 :')"
                                    wire:model="formData.address_1" />
                                <flux:textarea type="text" :label="__('Address Line 2 / पत्ता ओळ 2 :')"
                                    wire:model="formData.address_2" />
                                <flux:select wire:model.live="formData.state_id"
                                    placeholder="Choose State... / राज्य निवडा..." :label="__('State / राज्य')">
                                    <flux:select.option value="">Choose State...
                                    </flux:select.option>
                                    @foreach ($states as $st)
                                        <flux:select.option value="{{ $st->id }}">{{ $st->name }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:select wire:model="formData.city_id" placeholder="Choose City... / शहर निवडा..."
                                    :label="__('City / शहर')">
                                    <flux:select.option value="">Choose City...
                                    </flux:select.option>
                                    @foreach ($cities as $ct)
                                        <flux:select.option value="{{ $ct->id }}">{{ $ct->name }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:input type="text" :label="__('Pincode / पिनकोड :')"
                                    wire:model="formData.pincode" />
                                <flux:input type="text"
                                    :label="__('Registration Certificate No / नोंदणी प्रमाणपत्र क्रमांक :')"
                                    wire:model="formData.registration_no" />
                                <flux:input type="number" :label="__('Total No of Shares / शेअर्सची एकूण संख्या :')"
                                    wire:model="formData.no_of_shares" />
                                <flux:input type="number" :label="__('Each Share Value / प्रत्येक शेअरची किंमत :')"
                                    wire:model="formData.share_value" />
                                {{-- <flux:input type="text" :label="__('I Register :')" wire:model="formData.i_register" />
                                <flux:input type="text" :label="__('J Register :')" wire:model="formData.j_register" /> --}}
                                <flux:select wire:model="formData.admin_id"
                                    placeholder="Choose Admin... / प्रशासक निवडा..."
                                    :label="__('Assigned Admin / नियुक्त प्रशासक')">
                                    <flux:select.option value="">Choose Admin...
                                    </flux:select.option>
                                    @foreach ($admins as $admin)
                                        <flux:select.option value="{{ $admin->id }}">{{ $admin->name }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>

                                <div>
                                    <flux:label>
                                        {{ __('Is list of signed member available? / स्वाक्षरी केलेल्या सदस्यांची यादी उपलब्ध आहे का?') }}
                                    </flux:label>
                                    <flux:radio.group wire:model.live="formData.is_list_of_signed_member_available"
                                        class="flex gap-4">
                                        <flux:radio value="Yes" label="Yes / होय" />
                                        <flux:radio value="No" label="No / नाही" />
                                    </flux:radio.group>
                                </div>

                                <div>
                                    <flux:label>{{ __('Is byelaws available? / बायलॉज उपलब्ध आहेत का?') }}</flux:label>
                                    <flux:radio.group wire:model="formData.is_byelaws_available" class="flex gap-4">
                                        <flux:radio value="Yes" label="Yes / होय" />
                                        <flux:radio value="No" label="No / नाही" />
                                    </flux:radio.group>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <flux:button variant="primary" type="button" wire:click="nextStep">
                                    {{ __('Next') }}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        <!-- Step 2: Flat Details -->
        @if ($currentStep == 2)
            <div class="step-two">
                <div class="card">
                    <div class="card-header">Step 2: Flat Details</div>
                    <div class="card-body">
                        <div class="card">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <h3 class="text-yellow-800 font-semibold text-lg mb-2">
                                    ⚠️ Important Instructions for Excel Upload
                                </h3>

                                <ul class="list-disc pl-5 space-y-2 text-sm text-gray-700">

                                    <li class="text-red-600 font-medium">
                                        Do NOT change Excel Column Names. Column must remain exactly as provided
                                        in the sample file, otherwise the file will not upload or may return errors.
                                    </li>

                                    <li>
                                        <span class="font-medium text-gray-900">Required Fields:</span>
                                        <code>Building Name</code>, <code>Apartment Number</code>,
                                        <code>Certificate No</code> must not be empty.
                                    </li>

                                    <li>
                                        <span class="font-medium text-gray-900">Unique Flat:</span>
                                        Combination of <code>Building Name</code> + <code>Apartment Number</code> must
                                        be unique.
                                    </li>

                                    <li>
                                        <span class="font-medium text-gray-900">Signed Member Required </span><span
                                            class="font-medium text-red-500">(Allowed values:
                                            yes,no,Yes, No, होय, नाही):</span>
                                        If <code>Is List of Signed Member Available = Yes</code>
                                        <ul>
                                            <li>Did you purchase the apartment before the society was registered?</li>
                                            <li>Did you sign at the time of the society registration?</li>
                                            <li>Did the previous owner sign the registration documents?</li>
                                            <li>Has the flat transfer-related fee been paid to the Society?</li>
                                            <li>Have physical documents been submitted to the society?</li>
                                        </ul>
                                    <li>
                                        <span class="font-medium text-gray-900">Owner Details Required:</span>
                                        If <code>Is List of Signed Member Available = Yes</code>, then Owner Details
                                        (Owner 1
                                        Name and mobile or
                                        Owner 2 Name and Mobile or Owner 3 Name and Mobile) should not be
                                        empty.
                                    </li>

                                    <li>
                                        <span class="font-medium text-gray-900">Mobile Number Validation:</span>
                                        <ul class="list-disc pl-5 mt-1">
                                            <li>Owner mobile number should be 10 digits and valid format.</li>
                                            <li>No duplicate mobile in same row</li>
                                            <li>No duplicate mobile in entire file</li>
                                            <li>No duplicate mobile with existing records</li>
                                        </ul>
                                    </li>

                                </ul>
                            </div>
                            <div class="card-body">
                                <form wire:submit.prevent="excelImport">
                                    @if (!$fileUploaded)
                                        <div class="w-full mb-4">
                                            <label for="excel_file">Upload Document / दस्तऐवज अपलोड करा:</label>
                                            <flux:input type="file" wire:model="excel_file"
                                                class="border border-gray-300 rounded px-2 py-1 w-full" />
                                            @error('excel_file')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div wire:loading wire:target="excel_file" class="text-blue-500 mt-1">
                                            Uploading file, please wait...
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                                            <div class="p-4">
                                                <flux:button variant="filled" class="w-full" type="button"
                                                    wire:click="excelExport">
                                                    {{ __('EXCEL EXPORT') }}
                                                </flux:button>
                                            </div>
                                            @if ($excel_file)
                                                <div class="p-4 items-end">
                                                    <flux:button variant="primary" class="w-full" type="submit"
                                                        wire:loading.attr="disabled" wire:target="excel_file">
                                                        {{ __('EXCEL IMPORT') }}
                                                    </flux:button>
                                                </div>
                                            @endif
                                        @else
                                            <flux:button variant="filled" type="button" wire:click="excelExport">
                                                {{ __('Excel EXPORT') }}</flux:button>
                                            <div class="text-green-600 font-semibold">Excel file already uploaded
                                                successfully.
                                                No re-upload allowed</div>
                                    @endif
                            </div>
                            </form>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <flux:button variant="filled" class="mr-2" type="button" wire:click="prevStep">
                            {{ __('Back') }}</flux:button>
                        <flux:button variant="primary" type="button" wire:click="nextStep">{{ __('Next') }}
                        </flux:button>
                    </div>
                </div>
            </div>
    </div>
    </div>
    @endif

    <!-- Step 3: Verification -->
    @if ($currentStep == 3)
        <div class="step-three">
            <div class="card">
                <div class="card-header">Step 3: Verification </div>
                <div class="card-body">
                    <div class="m-4">
                        @if ($this->societyDetails)
                            <h4><strong>{{ $this->societyDetails->society_name }}</strong></h4>
                            <p><strong>Total Flats:</strong> {{ $this->societyDetails->total_flats }}</p>
                            <p><strong>Registration No:</strong> {{ $this->societyDetails->registration_no }}</p>
                            <p><strong>Assigned Admin:</strong> {{ $this->societyDetails->admin->name ?? 'None' }}</p>
                            <p><strong>Address:</strong>
                                @if ($this->societyDetails->address_1)
                                    {{ $this->societyDetails->address_1 }},
                                @endif
                                @if ($this->societyDetails->address_2)
                                    {{ $this->societyDetails->address_2 }},
                                @endif
                                @if ($this->societyDetails->city->name)
                                    {{ $this->societyDetails->city->name }},
                                @endif
                                @if ($this->societyDetails->state->name)
                                    {{ $this->societyDetails->state->name }}
                                @endif
                                @if ($this->societyDetails->pincode)
                                    - {{ $this->societyDetails->pincode }}
                                @endif
                            </p>
                        @else
                            <p>No society selected.</p>
                        @endif
                    </div>

                    <div class="overflow-x-auto w-full">
                        <div class="max-h-90 overflow-y-auto">
                            @if ($this->uploadedDetails->isNotEmpty())
                                @php
                                    $society = \App\Models\Society::find($this->societyId);
                                    // $expectedShares = (float) ($society->no_of_shares ?? 0);
                                    // $uploadedShares = (float) $this->uploadedDetails->sum('no_of_shares');
                                    // $diffShares = (float) $uploadedShares - (float) $expectedShares;
                                    // $expectedAmount = (float) ($society->no_of_shares * $society->share_value ?? 0);
                                @endphp
                                {{-- @if ($diffShares != 0)
                                    <div class="bg-red-100 text-red-800 p-2 mb-3 rounded">
                                        Total shares mismatch! Expected {{ $expectedShares }}, but found
                                        {{ $uploadedShares }} ({{ $diffShares > 0 ? 'more' : 'less' }} by
                                        {{ abs($diffShares) }}).
                                    </div>
                                @else
                                    <div class="bg-green-100 text-green-800 p-2 mb-3 rounded">
                                        Total shares ({{ $expectedShares }}) and amount ({{ $expectedAmount }})
                                        perfectly match.
                                    </div>
                                @endif --}}

                                <table class="min-w-full text-start text-sm font-light text-surface dark:text-white">
                                    <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
                                        <tr>
                                            <th scope="col" class="px-6 py-4">#</th>
                                            <th scope="col" class="px-6 py-4">Building Name</th>
                                            <th scope="col" class="px-6 py-4">Apartment Number</th>
                                            <th scope="col" class="px-6 py-4">Certificate No</th>
                                            <th scope="col" class="px-6 py-4">No of Shares</th>
                                            <th scope="col" class="px-6 py-4">Share Value</th>
                                            <th scope="col" class="px-6 py-4">Did you purchase the apartment before
                                                the society was registered?</th>
                                            <th scope="col" class="px-6 py-4">Did you sign at the time of the
                                                society registration?</th>
                                            <th scope="col" class="px-6 py-4">Did the previous owner sign the
                                                registration documents?</th>
                                            <th scope="col" class="px-6 py-4">Has the flat transfer-related fee
                                                been paid to the Society?</th>
                                            <th scope="col" class="px-6 py-4">Have physical documents been
                                                submitted to the society?</th>
                                            <th scope="col" class="px-6 py-4">Owner 1 Details</th>
                                            <th scope="col" class="px-6 py-4">Owner 2 Details</th>
                                            <th scope="col" class="px-6 py-4">Owner 3 Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($this->uploadedDetails as $index => $detail)
                                            <tr class="border-b border-neutral-200 dark:border-white/10">
                                                <td class="whitespace-nowrap px-6 py-4 font-medium">
                                                    {{ $index + 1 }}</td>
                                                <td class="whitespace-nowrap px-6 py-4">{{ $detail->building_name }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    {{ $detail->apartment_number }}</td>
                                                <td class="whitespace-nowrap px-6 py-4">{{ $detail->certificate_no }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">{{ $society->no_of_shares }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">{{ $society->share_value }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    {{ $detail->did_you_purchase_the_apartment_before_the_society_was_registered }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    {{ $detail->did_you_sign_at_the_time_of_the_society_registration }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    {{ $detail->did_the_previous_owner_sign_the_registration_documents }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    {{ $detail->has_the_flat_transfer_related_fee_been_paid_to_the_society }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    {{ $detail->have_physical_documents_been_submitted_to_the_society }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    {{ $detail->owner1_name }}
                                                    <br />
                                                    @if ($detail->owner1_mobile)
                                                        <i class="fa-solid fa-phone"></i> {{ $detail->owner1_mobile }}
                                                    @endif
                                                    <br />
                                                    @if ($detail->owner1_email)
                                                        <i class="fas fa-envelope"></i> {{ $detail->owner1_email }}
                                                    @endif
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    {{ $detail->owner2_name }}
                                                    <br />
                                                    @if ($detail->owner2_mobile)
                                                        <i class="fa-solid fa-phone"></i> {{ $detail->owner2_mobile }}
                                                    @endif
                                                    <br />
                                                    @if ($detail->owner2_email)
                                                        <i class="fas fa-envelope"></i> {{ $detail->owner2_email }}
                                                    @endif
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    {{ $detail->owner3_name }}
                                                    <br />
                                                    @if ($detail->owner3_mobile)
                                                        <i class="fa-solid fa-phone"></i> {{ $detail->owner3_mobile }}
                                                    @endif
                                                    <br />
                                                    @if ($detail->owner3_email)
                                                        <i class="fas fa-envelope"></i> {{ $detail->owner3_email }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No society details uploaded.</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <flux:button variant="filled" class="mr-2" type="button" wire:click="prevStep">
                            {{ __('Back') }}</flux:button>
                        <flux:button variant="primary" type="button" wire:click="done">{{ __('Done') }}
                        </flux:button>
                    </div>
                </div>

            </div>
        </div>
    @endif
    </div>
</section>
