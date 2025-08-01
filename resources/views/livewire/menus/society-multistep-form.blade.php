<section class="w-full">
    <div class="relative w-full">
        <flux:heading size="xl" level="1">{{ __('Create Society') }}</flux:heading>
        <flux:separator variant="subtle" />

        <div class="rounded-lg shadow-lg p-6">
            <div class="stepwizard">
                <div class="stepwizard-step">
                    <button type="button" @if ($currentStep == 1) disabled @endif>1</button>
                    <p>Basic</p>
                </div>
                <div class="stepwizard-step">
                    <button type="button" @if ($currentStep == 2) disabled @endif>2</button>
                    <p>Flat Details</p>
                </div>
                <div class="stepwizard-step">
                    <button type="button" @if ($currentStep == 3) disabled @endif>3</button>
                    <p>Verification</p>
                </div>
            </div>
            <div class="py-4">
                <livewire:menus.alerts />
            </div>
            <!-- Form Section -->
            @if($currentStep == 1)
                <form wire:submit.prevent="nextStep">
                    <div class="step-one">
                        <div class="card">
                            <div class="card-header">Step 1: Basic Information</div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="society_name">Society Name:</label>
                                    <flux:input type="text" class="form-control" id="society_name" wire:model="formData.society_name" />
                                    @error('formData.society_name') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="society_name">Total Flats:</label>
                                    <flux:input type="text" class="form-control" id="total_flats" wire:model="formData.total_flats" />
                                    @error('formData.total_flats') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="address_1">Address Line 1:</label>
                                    <flux:input type="text" class="form-control" id="address_1" wire:model="formData.address_1" />
                                    @error('formData.address_1') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="address_2">Address Line 2:</label>
                                    <flux:input type="text" class="form-control" id="address_2" wire:model="formData.address_2" />
                                    @error('formData.address_2') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="pincode">Pincode:</label>
                                    <flux:input type="text" class="form-control" id="pincode" wire:model="formData.pincode" />
                                    @error('formData.pincode') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="city">City:</label>
                                    <flux:input type="text" class="form-control" id="city" wire:model="formData.city" />
                                    @error('formData.city') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="state">State:</label>
                                    <flux:input type="text" class="form-control" id="state" wire:model="formData.state" />
                                    @error('formData.state') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
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
                                        <div class="form-group">
                                        <label for="csv_file">Upload Document:</label>
                                        <flux:input type="file" id="csv_file" wire:model="csv_file" />
                                        @error('csv_file') <span class="text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                        <flux:button variant="filled" type="button" wire:click="csvExport">{{ __('CSV EXPORT') }}</flux:button>
                                        <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="csv_file, csvImport">{{ __('CSV IMPORT') }}</flux:button>
                                        @else
                                            <flux:button variant="filled" type="button" wire:click="csvExport">{{ __('CSV EXPORT') }}</flux:button>
                                            <div class="text-green-600 font-semibold">CSV already uploaded successfully. No re-upload allowed.</div>
                                        @endif
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
                        <div class="m-5">
                            <h4>Society: <strong>{{ $this->societyName }}</strong></h4>
                        </div>
                        <div class="card-body">
                            <div class="flex flex-col overflow-x-auto">
                                <div class="sm:-mx-6 lg:-mx-8">
                                    <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                                        <div class="overflow-x-auto">
                                            @if ($this->uploadedDetails->isNotEmpty())
                                            <table class="min-w-full text-start text-sm font-light text-surface dark:text-white">
                                                <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-4">#</th>
                                                        <th scope="col" class="px-6 py-4">Building Name</th>
                                                        <th scope="col" class="px-6 py-4">Apartment Number</th>
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
    </div>
</section>
