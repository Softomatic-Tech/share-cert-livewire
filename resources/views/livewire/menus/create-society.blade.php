<section class="w-full">
    <div class="relative w-full">
        <div class="flex justify-between">
            <h1 class="text-xl font-bold">Create Society</h1>
            <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 mr-5 rounded" wire:click="redirectToSocietyPage">View Society</button>
        </div>

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
            <!-- Form Section -->
            <form wire:submit.prevent="saveSociety">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="society_name">Society Name:</label>
                            <flux:input type="text" class="form-control" id="society_name" wire:model="society_name" />
                            @error('society_name') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="society_name">Total Flats:</label>
                            <flux:input type="text" class="form-control" id="total_flats" wire:model="total_flats" />
                            @error('total_flats') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="address_1">Address Line 1:</label>
                            <flux:input type="text" class="form-control" id="address_1" wire:model="address_1" />
                            @error('address_1') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="address_2">Address Line 2:</label>
                            <flux:input type="text" class="form-control" id="address_2" wire:model="address_2" />
                            @error('address_2') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="pincode">Pincode:</label>
                            <flux:input type="text" class="form-control" id="pincode" wire:model="pincode" />
                            @error('pincode') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="city">City:</label>
                            <flux:input type="text" class="form-control" id="city" wire:model="city" />
                            @error('city') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="state">State:</label>
                            <flux:input type="text" class="form-control" id="state" wire:model="state" />
                            @error('state') <span class="text-red-500">{{ str_replace('form data.', '', $message) }}</span> @enderror
                        </div>
                        <div class="flex justify-end mt-4">
                            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>