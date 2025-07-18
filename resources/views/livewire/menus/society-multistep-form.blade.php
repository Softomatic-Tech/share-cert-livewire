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
            @if(session()->has('success'))
            <div id="alert-box" class="p-4 mb-4 text-sm text-white rounded-lg bg-green-500 flex justify-between items-center" role="alert">
                <div>{{ session('success') }}</div>
                <button onclick="dismissAlert()" class="ml-4 text-white font-medium">X</button>
            </div>
            @endif

            @if(session()->has('error'))
            <div id="alert-box" class="p-4 mb-4 text-sm text-white rounded-lg bg-red-500 flex justify-between items-center" role="alert">
                <div>{{ session('error') }}</div>
                <button onclick="dismissAlert()" class="ml-4 text-white font-medium">X</button>
            </div>
            @endif
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
                                        <flux:button variant="primary" type="submit">{{ __('CSV IMPORT') }}</flux:button>
                                        @else
                                            <flux:button variant="filled" type="button" wire:click="csvExport">{{ __('CSV EXPORT') }}</flux:button>
                                            <div class="text-green-600 font-semibold">CSV already uploaded successfully. No re-upload allowed.</div>
                                        @endif
                                    </form>
                                </div>
                            </div>

                            {{-- <div class="card mt-2 mb-2">
                                <div class="card-body">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <form wire:submit.prevent="agreementCopy">
                                                <div class="form-group">
                                                    <label for="agreement_copy">Xerox Copy Of Agreement:</label>
                                                    <flux:input type="file" id="agreement_copy" wire:model="agreement_copy" />
                                                    @error('agreement_copy') <span class="text-red-500">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="flex justify-end mr-4">
                                                    <button class="flex items-center rounded-md bg-gradient-to-tr from-slate-800 to-slate-700 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-sm hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="submit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1.5">
                                                          <path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.03 5.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72v4.94a.75.75 0 0 0 1.5 0v-4.94l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clip-rule="evenodd" />
                                                        </svg>
                                                       
                                                        Upload Files
                                                      </button>
                                                </div>
                                            </form>
                                        </div>

                                        <div>
                                            <form wire:submit.prevent="memberShipForm">
                                                <div class="form-group">
                                                    <label for="membership_form">MemberShip Form:</label>
                                                    <flux:input type="file" id="membership_form" wire:model="membership_form" />
                                                    @error('membership_form') <span class="text-red-500">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="flex justify-end">
                                                    <button class="flex items-center rounded-md bg-gradient-to-tr from-slate-800 to-slate-700 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-sm hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="submit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1.5">
                                                          <path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.03 5.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72v4.94a.75.75 0 0 0 1.5 0v-4.94l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clip-rule="evenodd" />
                                                        </svg>
                                                        Upload Files
                                                      </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <form wire:submit.prevent="allotmentLetter">
                                                <div class="form-group">
                                                    <label for="allotment_letter">Parking Allotment Letter:</label>
                                                    <flux:input type="file" id="allotment_letter" wire:model="allotment_letter" />
                                                    @error('allotment_letter') <span class="text-red-500">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="flex justify-end">
                                                    <button class="flex items-center rounded-md bg-gradient-to-tr from-slate-800 to-slate-700 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-sm hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="submit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1.5">
                                                          <path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.03 5.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72v4.94a.75.75 0 0 0 1.5 0v-4.94l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clip-rule="evenodd" />
                                                        </svg>
                                                        Upload Files
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <div>
                                            <form wire:submit.prevent="possesionLetter">
                                                <div class="form-group">
                                                    <label for="possesion_letter">Possesion Letter:</label>
                                                    <flux:input type="file" id="possesion_letter" wire:model="possesion_letter" />
                                                    @error('possesion_letter') <span class="text-red-500">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="flex justify-end">
                                                    <button class="flex items-center rounded-md bg-gradient-to-tr from-slate-800 to-slate-700 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-sm hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="submit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1.5">
                                                          <path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.03 5.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72v4.94a.75.75 0 0 0 1.5 0v-4.94l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clip-rule="evenodd" />
                                                        </svg>
                                                       
                                                        Upload Files
                                                      </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
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
<script>
    function dismissAlert() {
        document.getElementById("alert-box").style.display = "none";
    }
</script>