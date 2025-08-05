<section class="w-full">
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold">Create Society</h1>
    </div>

    <div class="rounded-lg shadow-lg p-6">
        <!-- Form Section -->
        <div class="mb-2">
            <livewire:menus.alerts />
        </div>
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
</section>