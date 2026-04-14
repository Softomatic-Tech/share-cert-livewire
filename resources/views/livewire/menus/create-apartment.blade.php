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
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h3 class="text-yellow-800 font-semibold text-lg mb-2">
                            ⚠️ Important Instructions for Excel Upload
                        </h3>

                        <ul class="list-disc pl-5 space-y-2 text-sm text-gray-700">

                            <li class="text-red-600 font-medium">
                                Do NOT change Excel Column Names. Column must remain exactly as provided
                                in the sample file, otherwise the file will not upload or may return errors.
                            </li>

                            <li>
                                <span class="font-medium text-gray-900">Required Fields:</span>
                                <code>Building Name</code>, <code>Apartment Number</code>,
                                <code>Certificate No</code> must not be empty.
                            </li>

                            <li>
                                <span class="font-medium text-gray-900">Unique Flat:</span>
                                Combination of <code>Building Name</code> + <code>Apartment Number</code> must
                                be unique.
                            </li>

                            <li>
                                <span class="font-medium text-gray-900">Signed Member Required </span><span
                                    class="font-medium text-red-500">(Allowed values:
                                    yes,no,Yes, No, होय, नाही):</span>
                                If <code>Is List of Signed Member Available = Yes</code>
                                <ul>
                                    <li>Did you purchase the apartment before the society was registered?</li>
                                    <li>Did you sign at the time of the society registration?</li>
                                    <li>Did the previous owner sign the registration documents?</li>
                                    <li>Has the flat transfer-related fee been paid to the Society?</li>
                                    <li>Have physical documents been submitted to the society?</li>
                                </ul>
                            <li>
                                <span class="font-medium text-gray-900">Owner Details Required:</span>
                                If <code>Is List of Signed Member Available = Yes</code>, then Owner Details (Owner 1
                                Name and mobile or
                                Owner 2 Name and Mobile or Owner 3 Name and Mobile) should not be
                                empty.
                            </li>

                            <li>
                                <span class="font-medium text-gray-900">Mobile Number Validation:</span>
                                <ul class="list-disc pl-5 mt-1">
                                    <li>Owner mobile number should be 10 digits and valid format.</li>
                                    <li>No duplicate mobile in same row</li>
                                    <li>No duplicate mobile in entire file</li>
                                    <li>No duplicate mobile with existing records</li>
                                </ul>
                            </li>

                        </ul>
                    </div>
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
                        <div wire:loading wire:target="excel_file" class="text-blue-500 mt-1">
                            Uploading file, please wait...
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
                        <flux:button variant="primary" type="submit" wire:loading.attr="disabled"
                            wire:target="excel_file">
                            {{ __('EXCEL IMPORT') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
