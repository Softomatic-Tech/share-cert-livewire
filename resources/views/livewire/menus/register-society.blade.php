<section class="w-full">
    <div class="relative w-full">
        <flux:heading size="xl" level="1" class="mb-2">{{ __('Register Society') }}</flux:heading>
        <flux:separator variant="subtle" />
    </div>
    <div class="shadow-lg rounded-lg p-6">
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
                <p>Apply</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" @if ($currentStep == 2) disabled @endif>2</button>
                <p>Verification</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" @if ($currentStep == 3) disabled @endif>3</button>
                <p>Need Clarification</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" @if ($currentStep == 4) disabled @endif>4</button>
                <p>Done</p>
            </div>
        </div>

        <!-- Form Section -->
        @if($currentStep == 1)
            <form wire:submit.prevent="save">
                <div class="step-one">
                    <div class="card">
                        <div class="card-header">Step 1: Apply</div>
                        <div class="card-body">
                            <div>
                                <label for="society_id">Society Name</label>
                                <flux:select id="society_id" wire:model.change="selectedSociety" placeholder="Choose Society...">
                                    <flux:select.option value="">Choose Society...</flux:select.option>
                                    @foreach($society  as $row)
                                        <flux:select.option value="{{ $row->id }}">{{ $row->society_name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error("society_id") <span class="text-red-500">The society name field is required.</span> @enderror
                                <label for="building_id">Building Name</label>
                                <flux:select wire:model.change="selectedBuilding" id="building_id" placeholder="Choose Building...">
                                    <flux:select.option value="">Choose Building...</flux:select.option>
                                    @foreach($buildings as $building)
                                        <flux:select.option value="{{ $building->building_name }}">{{ $building->building_name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error("building_id") <span class="text-red-500">The building name field is required.</span> @enderror

                                <label for="apartment">Apartment No</label>
                                <flux:select id="apartment" wire:model="selectedApartment" placeholder="Choose Apartment...">
                                    <flux:select.option value="">Choose Apartment...</flux:select.option>
                                    @foreach($apartments as $apartment)
                                        <flux:select.option value="{{ $apartment->apartment_number }}">{{ $apartment->apartment_number }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error("apartment") <span class="text-red-500">The apartment number field is required.</span> @enderror
                                <div class="owner-fields">
                                    <label for="owner_name">Owner Name</label>
                                    <flux:input wire:model="formData.owner_name" type="text" id="owner_name"/>
                                    @error("formData.owner_name") <span class="text-red-500">{{ str_replace('formData.', '', $message) }}</span> @enderror

                                    <!-- Email -->
                                    <label for="email">Email</label>
                                    <flux:input type="email" wire:model="formData.email" id="email"/>
                                    @error("formData.email") <span class="text-red-500">{{ str_replace('formData.', '', $message) }}</span> @enderror

                                    <!-- Phone -->
                                    <label for="phone">Phone</label>
                                    <flux:input type="text" wire:model="formData.phone" id="phone"/>
                                    @error("formData.phone") <span class="text-red-500">{{ str_replace('formData.', '', $message) }}</span> @enderror
                            </div>
                            <div class="flex justify-end mt-4">
                                <flux:button variant="primary" type="submit">{{ __('Next') }}</flux:button>
                            </div>
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
                    <div class="card-header">Step 2: Verification</div>
                    <div class="card-body">
                        <label for="document">Document:</label>
                        <button class="btn btn-secondary" type="button" wire:click="prevStep">Back</button>
                        <button class="btn btn-primary" type="button" wire:click="nextStep">Next</button>
                    </div>
                </div>
            </div>
        </form>
        @endif

        <!-- Step 3: Verification -->
        @if($currentStep == 3)
        <form wire:submit.prevent="nextStep">
            <div class="step-three">
                <div class="card">
                    <div class="card-header">Step 3: Need Clarification</div>
                    <div class="card-body">
                      
                        <button class="btn btn-secondary" type="button" wire:click="prevStep">Back</button>
                        <button class="btn btn-primary" type="button" wire:click="nextStep">Next</button>
                    </div>
                </div>
            </div>
        </form>
        @endif
        <!-- Step 3: Done -->
        @if($currentStep == 4)
        <form wire:submit.prevent="nextStep">
            <div class="step-four">
                <div class="card">
                    <div class="card-header">Step 4: Done</div>
                    <div class="card-body">
                        <button class="btn btn-secondary" type="button" wire:click="prevStep">Back</button>
                        <button class="btn btn-primary" type="Submit">Save</button>
                    </div>
                </div>
            </div>
        </form>
        @endif
    </div>
</section>
<script>
    function dismissAlert() {
        document.getElementById("alert-box").style.display = "none";
    }
</script>