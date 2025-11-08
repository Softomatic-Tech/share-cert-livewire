<div>
    <div class="mb-2 w-full">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="#">Create Society</flux:breadcrumbs.item>
        </flux:breadcrumbs>
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
                        <flux:select wire:model.live="state_id" placeholder="Choose State..." :label="__('State')">
                            <flux:select.option value="">Choose State...</flux:select.option>
                            @foreach($states  as $st)
                                <flux:select.option value="{{ $st->id }}">{{ $st->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:select wire:model="city_id" placeholder="Choose City..." :label="__('City')">
                            <flux:select.option value="">Choose City...</flux:select.option>
                            @foreach($cities  as $ct)
                                <flux:select.option value="{{ $ct->id }}">{{ $ct->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input type="text" :label="__('Pincode')" wire:model="pincode" />
                        <flux:input type="text"  :label="__('Registration No :')" wire:model="registration_no" />
                        <flux:input type="text"  :label="__('No of Shares :')" wire:model="no_of_shares" />
                        <flux:input type="text"  :label="__('Share Value :')" wire:model="share_value" />
                    </div>
                    <div class="flex justify-end mt-4">
                        <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>