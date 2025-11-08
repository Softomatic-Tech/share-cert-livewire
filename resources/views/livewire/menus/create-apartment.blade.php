<section class="w-full">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}">Admin</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="#">Create Apartment</flux:breadcrumbs.item>
    </flux:breadcrumbs>
    <flux:separator variant="subtle" />

    <div class="rounded-lg shadow-lg p-6">
        <div class="mb-2">
            <livewire:menus.alerts />
        </div>
        <!-- Form Section -->
        <form wire:submit.prevent="saveApartment">
            <div class="card">
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                        <div>
                        <label for="csv_file">Society Name:</label>
                        <flux:select wire:model="society_id" placeholder="Choose Society...">
                            <flux:select.option value="">Choose Society...</flux:select.option>
                            @foreach($society  as $row)
                                <flux:select.option value="{{ $row->id }}">{{ $row->society_name }} , (Total Flats: {{ $row->total_flats }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error("society_id") <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                        <label for="csv_file">Upload Document:</label>
                        <flux:input type="file" id="csv_file" wire:model="csv_file" class="border border-gray-300 rounded px-2 py-1 w-full" />
                        @error('csv_file') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-between mt-4">
                    <flux:button variant="filled" type="button" wire:click="csvExport">{{ __('CSV EXPORT') }}</flux:button>
                    <flux:button variant="primary" type="submit">{{ __('CSV IMPORT') }}</flux:button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>