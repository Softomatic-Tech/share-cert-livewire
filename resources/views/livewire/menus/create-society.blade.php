<section class="w-full">
    <div class="relative w-full">
        <flux:heading size="xl" level="1">{{ __('Create Society') }}</flux:heading>
        <flux:separator variant="subtle" />

        <div class="rounded-lg shadow-lg p-6">
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
                                <flux:button variant="primary" type="button" wire:click="nextStep">{{ __('Next') }}</flux:button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif

            <!-- Step 2: Flat Details -->
            @if($currentStep == 2)
                <form wire:submit.prevent="nextStep">
                    <div class="step-two">
                        <div class="card">
                            <div class="card-header">Step 2: Flat Details</div>
                            <div class="card-body">
                                @foreach ($formData['apartments'] as $index => $apartments)
                                <div>
                                    <div class="form-group">
                                        <label for="building_name_{{ $index }}">Building Name:</label>
                                        <flux:input type="text" class="form-control" id="building_name_{{ $index }}" wire:model="formData.apartments.{{ $index }}.building_name" />
                                        @error("formData.apartments.{$index}.building_name") <span class="text-red-500">{{ str_replace('formData.apartments.'.$index.'.', '', str_replace('_', ' ', $message)) }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="apartment_number{{ $index }}">Flat Number:</label>
                                        <flux:input type="text" class="form-control" id="apartment_number{{ $index }}" wire:model="formData.apartments.{{ $index }}.apartment_number" />
                                        @error("formData.apartments.{$index}.apartment_number") <span class="text-red-500">{{ str_replace('formData.apartments.'.$index.'.', '', str_replace('_', ' ', $message)) }}</span> @enderror
                                    </div>
                                    <div class="flex justify-end mt-4">
                                        @if(count($formData['apartments']) > 1)
                                        <flux:button variant="danger" type="button" wire:click="removeApartments({{ $index }})">{{ __('Remove') }}</flux:button>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                                <div class="flex justify-end mt-4">
                                    <button class="bg-green-500 rounded-sm p-2 text-white font-bold" type="button" wire:click="addApartments">Add</button>
                                </div>
                                <flux:button variant="filled" type="button" wire:click="prevStep">{{ __('Back') }}</flux:button>
                                <flux:button variant="primary" type="button" wire:click="nextStep">{{ __('Next') }}</flux:button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif

            <!-- Step 3: Verification -->
            @if($currentStep == 3)
                <form wire:submit.prevent="save">
                    <div class="step-three">
                        <div class="card">
                            <div class="card-header">Step 3: Verification</div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="verification_document">Upload Document:</label>
                                    <flux:input type="file" class="form-control" id="verification_document" wire:model="formData.verification_document" />
                                    @error('formData.verification_document') <span class="text-red-500">{{ str_replace('The form data.', '', $message) }}</span> @enderror
                                </div>
                                <flux:button variant="filled" type="button" wire:click="prevStep">{{ __('Back') }}</flux:button>
                                <flux:button variant="primary" type="submit">{{ __('Submit') }}</flux:button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>
<script>
    function dismissAlert() {
        document.getElementById("alert-box").style.display = "none";
    }
</script>