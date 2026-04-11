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
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                                            <div class="p-4">
                                                <flux:button variant="filled" class="w-full" type="button"
                                                    wire:click="excelExport">
                                                    {{ __('Excel EXPORT / एक्सेल निर्यात') }}
                                                </flux:button>
                                            </div>
                                            @if ($excel_file)
                                                <div class="p-4 items-end">
                                                    <flux:button variant="primary" class="w-full" type="submit"
                                                        wire:loading.attr="disabled">
                                                        {{ __('Excel IMPORT / एक्सेल आयात') }}
                                                    </flux:button>
                                                </div>
                                            @endif
                                        @else
                                            <flux:button variant="filled" type="button" wire:click="excelExport">
                                                {{ __('Excel EXPORT / एक्सेल निर्यात') }}</flux:button>
                                            <div class="text-green-600 font-semibold">Excel file already uploaded
                                                successfully / एक्सेल फाइल यशस्वीरित्या अपलोड झाली आहे.
                                                No re-upload allowed / पुन्हा अपलोड करण्यास परवानगी नाही.</div>
                                    @endif
                            </div>
                            </form>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <flux:button variant="filled" class="mr-2" type="button" wire:click="prevStep">
                            {{ __('Back / मागे') }}</flux:button>
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
                            {{-- <p><strong>I Register:</strong> {{ $this->societyDetails->i_register ?? 'N/A' }}</p>
                            <p><strong>J Register:</strong> {{ $this->societyDetails->j_register ?? 'N/A' }}</p>
                            <p><strong>Total No of Shares:</strong> {{ $this->societyDetails->no_of_shares }}</p>
                            <p><strong>Each Share Value:</strong> {{ $this->societyDetails->share_value }}</p> --}}
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
                                            <th scope="col" class="px-6 py-4">Is Membership Application Signed</th>
                                            <th scope="col" class="px-6 py-4">Is Membership Application Signed by
                                                One of the Current Owners</th>
                                            <th scope="col" class="px-6 py-4">Signed Member Name</th>
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
                                                    {{ $detail->is_membership_application_signed }}</td>
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    {{ $detail->is_membership_application_signed_by_one_of_the_current_owners }}
                                                </td>
                                                <td class="whitespace-nowrap px-6 py-4">
                                                    {{ $detail->signed_member_name }}</td>
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
                            {{ __('Back / मागे') }}</flux:button>
                        <flux:button variant="primary" type="button" wire:click="done">{{ __('Done') }}
                        </flux:button>
                    </div>
                </div>

            </div>
        </div>
    @endif
    </div>
</section>
