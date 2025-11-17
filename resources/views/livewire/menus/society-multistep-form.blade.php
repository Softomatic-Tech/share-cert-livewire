<section>
    <div class="mb-1 w-full">
        <flux:breadcrumbs>
        <flux:breadcrumbs.item href="#">Super Admin</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="#">Create Society</flux:breadcrumbs.item>
    </flux:breadcrumbs>
        <flux:separator variant="subtle" />
    </div>
    
    <div class="rounded-lg shadow-lg px-6 py-2">
        <div class="stepwizard">
            <div class="stepwizard-step">
                <button type="button" @if ($currentStep == 1) disabled @endif>1</button>
                <p @if ($currentStep == 1) disabled @endif>Basic</p>
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
        @if($currentStep == 1)
            <form wire:submit.prevent="nextStep">
                <div class="step-one">
                    <div class="card">
                        <div class="card-header">Step 1: Basic Information</div>
                        <div class="card-body">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                                <flux:input type="text" :label="__('Society Name :')" wire:model="formData.society_name" />
                                <flux:input type="number" :label="__('Total Flats :')" wire:model="formData.total_flats" />
                                <flux:input type="text"  :label="__('Address Line 1 :')" wire:model="formData.address_1" />
                                <flux:input type="text"  :label="__('Address Line 2 :')" wire:model="formData.address_2" />
                                <flux:select wire:model.live="formData.state_id" placeholder="Choose State..." :label="__('State')">
                                    <flux:select.option value="">Choose State...</flux:select.option>
                                    @foreach($states  as $st)
                                        <flux:select.option value="{{ $st->id }}">{{ $st->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:select wire:model="formData.city_id" placeholder="Choose City..." :label="__('City')">
                                    <flux:select.option value="">Choose City...</flux:select.option>
                                    @foreach($cities  as $ct)
                                        <flux:select.option value="{{ $ct->id }}">{{ $ct->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:input type="text" :label="__('Pincode :')" wire:model="formData.pincode" />
                                <flux:input type="text"  :label="__('Registration No :')" wire:model="formData.registration_no" />
                                <flux:input type="number"  :label="__('Total No of Shares :')" wire:model="formData.no_of_shares" />
                                <flux:input type="number"  :label="__('Each Share Value :')" wire:model="formData.share_value" />
                            </div>
                            <div class="flex justify-end mt-4">
                                <flux:button variant="primary" type="button" wire:click="nextStep">{{ __('Next') }}</flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        <!-- Step 2: Flat Details -->
        @if($currentStep == 2)
            <div class="step-two">
                <div class="card">
                    <div class="card-header">Step 2: Flat Details</div>
                    <div class="card-body">
                        <div class="card">
                            <div class="card-body">
                                <form wire:submit.prevent="csvImport">
                                    @if (!$csvUploaded)
                                    <div class="w-full mb-4">
                                    <label for="csv_file">Upload Document:</label>
                                    <flux:input type="file" wire:model="csv_file" class="border border-gray-300 rounded px-2 py-1 w-full" />
                                    @error('csv_file') <span class="text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                                    <div class="p-4">
                                        <flux:button variant="filled" class="w-full" type="button" wire:click="csvExport">
                                            {{ __('CSV EXPORT') }}
                                        </flux:button>
                                    </div>
                                    @if ($csv_file)
                                    <div class="p-4 items-end">
                                        <flux:button variant="primary" class="w-full" type="submit" wire:loading.attr="disabled">{{ __('CSV IMPORT') }}</flux:button>
                                    </div>
                                    @endif
                                    @else
                                        <flux:button variant="filled" type="button" wire:click="csvExport">{{ __('CSV EXPORT') }}</flux:button>
                                        <div class="text-green-600 font-semibold">CSV already uploaded successfully. No re-upload allowed.</div>
                                    @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <flux:button variant="filled" class="mr-2" type="button" wire:click="prevStep">{{ __('Back') }}</flux:button>
                            <flux:button variant="primary" type="button" wire:click="nextStep">{{ __('Next') }}</flux:button>
                        </div>
                    </div> 
                    </div>
                </div>
            </div>
        @endif

        <!-- Step 3: Verification -->
        @if($currentStep == 3)
            <div class="step-three">
                <div class="card">
                    <div class="card-header">Step 3: Verification</div>
                    <div class="card-body">
                        <div class="m-4">
                            @if ($this->societyDetails)
                            <h4><strong>{{ $this->societyDetails->society_name}}</strong></h4>
                            <p><strong>Total Flats:</strong> {{ $this->societyDetails->total_flats}}</p>
                            <p><strong>Registration No:</strong> {{ $this->societyDetails->registration_no }}</p>
                            <p><strong>Total No of Shares:</strong> {{ $this->societyDetails->no_of_shares }}</p>
                            <p><strong>Each Share Value:</strong> {{ $this->societyDetails->share_value }}</p>
                            <p><strong>Address:</strong> 
                                @if($this->societyDetails->address_1){{ $this->societyDetails->address_1 }},@endif
                                @if($this->societyDetails->address_2){{ $this->societyDetails->address_2 }},@endif
                                @if($this->societyDetails->city->name){{ $this->societyDetails->city->name }},@endif
                                @if($this->societyDetails->state->name){{ $this->societyDetails->state->name }}@endif
                                @if($this->societyDetails->pincode) - {{ $this->societyDetails->pincode }}@endif
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
                                    $expectedShares = (float) ($society->no_of_shares ?? 0);
                                    $uploadedShares = (float) $this->uploadedDetails->sum('no_of_shares');
                                    $diffShares = (float)$uploadedShares - (float)$expectedShares;
                                    $expectedAmount = (float) ($society->no_of_shares * $society->share_value ?? 0);
                                    $uploadedAmount = (float) $this->uploadedDetails->sum('share_capital_amount');
                                    $diffAmount = (float)$uploadedAmount - (float)$expectedAmount;
                                @endphp
                                @if ($diffShares != 0)
                                    @if($diffAmount != 0)
                                        <div class="bg-red-100 text-red-800 p-2 mb-3 rounded">
                                            Total shares and amount mismatch! Expected {{ $expectedShares }}, but found {{ $uploadedShares }} ({{ $diffShares > 0 ? 'more' : 'less' }} by {{ abs($diffShares) }}) and Expected {{ $expectedAmount }}, but found {{ $uploadedAmount }} ({{ $diffAmount > 0 ? 'more' : 'less' }} by {{ abs($diffAmount) }}).
                                        </div>
                                    @else
                                        <div class="bg-red-100 text-red-800 p-2 mb-3 rounded">
                                        Total shares mismatch! Expected {{ $expectedShares }}, but found {{ $uploadedShares }} ({{ $diffShares > 0 ? 'more' : 'less' }} by {{ abs($diffShares) }}).
                                    </div>
                                    @endif
                                @else
                                    @if($diffAmount != 0)
                                        <div class="bg-red-100 text-red-800 p-2 mb-3 rounded">
                                            Total shares amount mismatch! Expected {{ $expectedAmount }}, but found {{ $uploadedAmount }} ({{ $diffAmount > 0 ? 'more' : 'less' }} by {{ abs($diffAmount) }}).
                                        </div>
                                    @else
                                    <div class="bg-green-100 text-green-800 p-2 mb-3 rounded">
                                        Total shares ({{ $expectedShares }}) and amount ({{ $expectedAmount }}) perfectly match.
                                    </div>
                                    @endif
                                @endif

                                <table class="min-w-full text-start text-sm font-light text-surface dark:text-white">
                                    <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
                                        <tr>
                                            <th scope="col" class="px-6 py-4">#</th>
                                            <th scope="col" class="px-6 py-4">Building Name</th>
                                            <th scope="col" class="px-6 py-4">Apartment Number</th>
                                            <th scope="col" class="px-6 py-4">Certificate No</th>
                                            <th scope="col" class="px-6 py-4">No of Shares/Each Share Amount</th>
                                            <th scope="col" class="px-6 py-4">Owner 1 Details</th>
                                            <th scope="col" class="px-6 py-4">Owner 2 Details</th>
                                            <th scope="col" class="px-6 py-4">Owner 3 Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($this->uploadedDetails as $index => $detail)
                                        <tr class="border-b border-neutral-200 dark:border-white/10">
                                            <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $index + 1 }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ $detail->building_name }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ $detail->apartment_number }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ $detail->certificate_no }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">{{ $detail->no_of_shares }}/{{ $detail->share_capital_amount }}</td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                {{ $detail->owner1_name }} 
                                                <br />@if($detail->owner1_mobile)<i class="fa-solid fa-phone"></i> {{ $detail->owner1_mobile }} @endif
                                                <br /> @if($detail->owner1_email)<i class="fas fa-envelope"></i> {{ $detail->owner1_email }}@endif
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                {{ $detail->owner2_name }} 
                                                <br />@if($detail->owner2_mobile)<i class="fa-solid fa-phone"></i> {{ $detail->owner2_mobile }} @endif
                                                <br />@if($detail->owner2_email)<i class="fas fa-envelope"></i> {{ $detail->owner2_email }}@endif
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                {{ $detail->owner3_name }} 
                                                <br />@if($detail->owner3_mobile)<i class="fa-solid fa-phone"></i> {{ $detail->owner3_mobile }} @endif
                                                <br />@if($detail->owner3_email)<i class="fas fa-envelope"></i> {{ $detail->owner3_email }}@endif
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
                            <flux:button variant="filled" class="mr-2" type="button" wire:click="prevStep">{{ __('Back') }}</flux:button>
                            <flux:button variant="primary" type="button" wire:click="done">{{ __('Done') }}</flux:button>
                        </div>
                    </div>
                    
                </div>
            </div>
        @endif
    </div>
</section>
