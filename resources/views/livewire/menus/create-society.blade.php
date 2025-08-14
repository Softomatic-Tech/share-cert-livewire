<div class="w-full">
    <div class="relative mb-2 w-full">
        <flux:heading size="xl" level="1">{{ __('Create Society') }}</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    <div class="rounded-lg shadow-lg p-6">
        <!-- Form Section -->
        <div class="mb-2">
            <livewire:menus.alerts />
        </div>
        <form wire:submit.prevent="saveSociety">
            <div class="card">
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                        <flux:input type="text" :label="__('Society Name')" wire:model="society_name" />
                        <flux:input type="text" :label="__('Total Flats')" wire:model="total_flats" />
                        <flux:input type="text"  :label="__('Address Line 1')" wire:model="address_1" />
                        <flux:input type="text"  :label="__('Address Line 2')" wire:model="address_2" />
                        <flux:input type="text" :label="__('Pincode')" wire:model="pincode" />
                        <flux:input type="text" :label="__('City')" wire:model="city" />
                        <flux:input type="text" :label="__('State')" wire:model="state" />
                    </div>
                    <div class="flex justify-end mt-4">
                        <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>