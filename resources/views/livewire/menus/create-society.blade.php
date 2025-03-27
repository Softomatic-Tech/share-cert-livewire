<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Society') }}</flux:heading>
        <flux:separator variant="subtle" />

        <div class="max-w-3xl bg-white shadow-lg rounded-lg p-6">
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
            <div class="flex justify-between items-center mb-6" wire:key="step-indicator">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 flex items-center justify-center 
                    {{ $currentStep == 1 ? 'bg-amber-500 text-dark' : 'bg-zinc-200 text-gray-600' }} font-bold rounded-full">
                    1
                </div>
                <p class="mt-2 {{ $currentStep == 1 ? 'text-dark font-medium' : 'text-gray-600' }}">Basic</p>
                </div>
                <div class="flex-1 h-0.5 bg-zinc-200 mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 flex items-center justify-center 
                    {{ $currentStep == 2 ? 'bg-amber-500 text-dark' : 'bg-zinc-200 text-gray-600' }} font-bold rounded-full">
                    2
                </div>
                    <p class="mt-2 {{ $currentStep == 2 ? 'text-dark font-medium' : 'text-gray-600' }}">Flat Details</p>
                </div>
                <div class="flex-1 h-0.5 bg-zinc-200 mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 flex items-center justify-center 
                    {{ $currentStep == 3 ? 'bg-amber-500 text-dark' : 'bg-zinc-200 text-gray-600' }} font-bold rounded-full">
                    3
                </div>
                    <p class="mt-2 {{ $currentStep == 3 ? 'text-dark font-medium' : 'text-gray-600' }}">Verification</p>
                </div>
            </div>

            <!-- Form Section -->
            @if($currentStep == 1)
                <h2 class="text-xl font-semibold mb-4">Step 1: Basic Information</h2>
                <form wire:submit.prevent="nextStep">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-medium">Society Name:</label>
                            <flux:input wire:model="formData.society_name" type="text" required />
                            @error('formData.society_name') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Address Line 1:</label>
                            {{-- <input type="text" wire:model="formData.address_1" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"> --}}
                            <flux:input wire:model="formData.address_1" type="text" required />
                            @error('formData.address_1') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Address Line 2:</label>
                            {{-- <input type="text" wire:model="formData.address_2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"> --}}
                            <flux:input wire:model="formData.address_2" type="text" />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Pincode:</label>
                            {{-- <input type="text" wire:model="formData.pincode" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"> --}}
                            <flux:input wire:model="formData.pincode" type="text" required />
                            @error('formData.pincode')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">State:</label>
                            {{-- <input type="text" wire:model="formData.state" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"> --}}
                            <flux:input wire:model="formData.state" type="text" required />
                            @error('formData.state') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">City:</label>
                            {{-- <input type="text" wire:model="formData.city" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"> --}}
                            <flux:input wire:model="formData.city" type="text" required />
                            @error('formData.city') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Contact Phone Number:</label>
                            {{-- <input type="text" wire:model="formData.phone" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"> --}}
                            <flux:input wire:model="formData.phone" type="text" required />
                            @error('formData.phone') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-amber-500 text-dark px-6 py-2 rounded-lg hover:bg-blue-600 transition">Save And Next</button>
                        </div>
                    </div>
                </form>
            @endif

            <!-- Step 2: Flat Details -->
            @if($currentStep == 2)
                <h2 class="text-xl font-semibold mb-4">Step 2: Flat Details</h2>
                <form wire:submit.prevent="nextStep">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-medium">Flat Number:</label>
                            {{-- <input type="text" wire:model="formData.flat_number" class="w-full px-4 py-2 border rounded-lg"> --}}
                            <flux:input wire:model="formData.flat_number" type="text" required />
                            @error('formData.flat_number') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium">Owner Name:</label>
                            {{-- <input type="text" wire:model="formData.owner_name" class="w-full px-4 py-2 border rounded-lg"> --}}
                            <flux:input wire:model="formData.owner_name" type="text" required />
                            @error('formData.owner_name') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-between">
                        <button type="button" wire:click="prevStep" class="bg-amber-500 text-dark px-6 py-2 rounded-lg">Back</button>
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg">Next</button>
                    </div>
                </form>
            @endif

            <!-- Step 3: Verification -->
            @if($currentStep == 3)
                <h2 class="text-xl font-semibold mb-4">Step 3: Verification</h2>
                <form wire:submit.prevent="save">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-medium">Upload Verification Document:</label>
                            {{-- <input type="text" wire:model="formData.verification_document" class="w-full px-4 py-2 border rounded-lg"> --}}
                            <flux:input wire:model="formData.verification_document" type="text" required />
                            @error('formData.verification_document') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-between">
                        <button type="button" wire:click="prevStep" class="bg-gray-500 text-white px-6 py-2 rounded-lg">Back</button>
                        <button type="submit" class="bg-amber-500 text-dark px-6 py-2 rounded-lg">Submit</button>
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