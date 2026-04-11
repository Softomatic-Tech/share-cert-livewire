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
        <form wire:submit.prevent="saveApartmentExcel">
            <div class="card">
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                        <div>
                            <label for="society_id">Society Name / सोसायटीचे नाव:</label>
                            <flux:select wire:model="society_id">
                                <flux:select.option value="">Choose Society...</flux:select.option>
                                @foreach ($society as $row)
                                    <flux:select.option value="{{ $row->id }}">{{ $row->society_name }} , (Total
                                        Flats: {{ $row->total_flats }})</flux:select.option>
                                @endforeach
                            </flux:select>
                            @error('society_id')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <div>
                                <label for="excel_file">Upload Excel Document / Excel दस्तावेज अप्लोड करा:</label>
                                <div wire:key="excel-upload">
                                    <flux:input type="file" id="excel_file" wire:model="excel_file"
                                        class="border border-gray-300 rounded px-2 py-1 w-full" />
                                </div>
                                @error('excel_file')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div>
                            <flux:label>File Type:</flux:label>
                            <flux:radio.group wire:model.live="file_type" class="flex gap-4">
                                <flux:radio value="csv" label="CSV" />
                                <flux:radio value="excel" label="Excel" />
                            </flux:radio.group>
                        </div> --}}
                    </div>

                    {{-- @if ($file_type === 'csv')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                            <div>
                                <label for="csv_file">Upload CSV Document / CSV दस्तावेज अप्लोड करा:</label>
                                <flux:input type="file" id="csv_file" wire:model="csv_file"
                                    class="border border-gray-300 rounded px-2 py-1 w-full" />
                                @error('csv_file')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @elseif ($file_type === 'excel') --}}
                    {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                        <div>
                            <label for="excel_file">Upload Excel Document / Excel दस्तावेज अप्लोड करा:</label>
                            <div wire:key="excel-upload">
                                <flux:input type="file" id="excel_file" wire:model="excel_file"
                                    class="border border-gray-300 rounded px-2 py-1 w-full" />
                            </div>
                            @error('excel_file')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div> --}}
                    {{-- @endif --}}

                    <div class="flex justify-between mt-4">
                        {{-- @if ($file_type === 'csv')
                            <flux:button variant="filled" type="button" wire:click="csvExport">
                                {{ __('CSV SAMPLE DOWNLOAD') }}
                            </flux:button>
                        @elseif ($file_type === 'excel') --}}
                        <flux:button variant="filled" type="button" wire:click="excelExport">
                            {{ __('EXCEL SAMPLE DOWNLOAD') }}
                        </flux:button>
                        {{-- @endif --}}
                        <flux:button variant="primary" type="submit">{{ __('IMPORT') }}</flux:button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
