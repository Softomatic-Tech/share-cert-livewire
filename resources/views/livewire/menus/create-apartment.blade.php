<section class="w-full">
    <div class="flex justify-between">
        <h1 class="text-xl font-bold">Create Apartment</h1>
    </div>

    <div class="rounded-lg shadow-lg p-6">
        <div class="mb-2">
            <livewire:menus.alerts />
        </div>
        <!-- Form Section -->
        <form wire:submit.prevent="saveApartment">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="society_id">Society Name</label>
                        <flux:select id="society_id" wire:model="society_id" placeholder="Choose Society...">
                            <flux:select.option value="">Choose Society...</flux:select.option>
                            @foreach($society  as $row)
                                <flux:select.option value="{{ $row->id }}">{{ $row->society_name }} , (Total Flats: {{ $row->total_flats }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error("society_id") <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                    <label for="csv_file">Upload Document:</label>
                    <flux:input type="file" id="csv_file" wire:model="csv_file" />
                    @error('csv_file') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end mt-4">
                    <flux:button variant="filled" type="button" wire:click="csvExport">{{ __('CSV EXPORT') }}</flux:button>
                    <flux:button variant="primary" type="submit">{{ __('CSV IMPORT') }}</flux:button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>