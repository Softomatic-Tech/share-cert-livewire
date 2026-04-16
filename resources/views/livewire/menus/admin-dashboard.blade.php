<div>
    <div class="w-full px-2">
        <div class="flex justify-between items-center">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}">Admin Dashboard</flux:breadcrumbs.item>
            </flux:breadcrumbs>
            <div class="flex gap-4">
                <flux:tooltip content="Create Society">
                    <button type="button" class="border font-bold py-1 px-2 rounded cursor-pointer"
                        wire:click="redirectToCreateSociety"><i class="fa-solid fa-plus"></i></button>
                </flux:tooltip>
                <flux:tooltip content="Add Apartment To Society">
                    <button type="button" class="border font-bold py-1 px-2 rounded cursor-pointer"
                        wire:click="redirectToCreateApartment"><i class="fa-solid fa-building"></i></button>
                </flux:tooltip>
            </div>
        </div>
        <flux:separator variant="subtle" />
        <div class="my-2">
            <livewire:menus.alerts type="success" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
            <!-- Sidebar -->
            <aside class="col-span-1 border-r p-2">
                <div class="mb-2 flex-shrink-0">
                    <flux:input type="text" placeholder="Search Society..." wire:model.live="search" size="sm">
                        <x-slot name="iconTrailing">
                            @if ($search)
                                <flux:button size="sm" variant="subtle" icon="x-mark" class="-mr-1"
                                    wire:click="$set('search', '')" />
                            @endif
                            <flux:icon.magnifying-glass variant="outline" class="size-4 text-gray-400" />
                        </x-slot>
                    </flux:input>
                </div>

                <div class="space-y-4">
                    @forelse($societies as $index => $society)
                        <div wire:click="selectSociety({{ $society->id }})" wire:key="society-{{ $society->id }}"
                            class="p-2 rounded-lg border hover:shadow-md transition-all cursor-pointer @if ($selectedSocietyId == $society->id) active-society @endif">
                            {{-- Society name (left) & Reg No (right) on same line --}}
                            <div class="flex items-start justify-between gap-1 mb-0.5">
                                <p class="text-blue-800 dark:text-white text-sm font-bold leading-tight">
                                    {{ $society->society_name }}
                                </p>
                                <p class="text-gray-400 dark:text-gray-500 text-[10px] font-mono shrink-0 mt-0.5">
                                    {{ $society->registration_no ?? 'N/A' }}
                                </p>
                                @if ($society->changes_required_count > 0)
                                    <flux:badge color="red" size="xs" variant="solid" class="ml-1">
                                        {{ $society->changes_required_count }} Changes
                                    </flux:badge>
                                @endif
                            </div>

                            {{-- Address — wraps fully --}}
                            <p class="text-gray-500 dark:text-gray-400 text-[11px] leading-snug mb-2 whitespace-normal">
                                {{ $society->address_1 }}@if ($society->city?->name)
                                    , {{ $society->city->name }}
                                @endif
                            </p>

                            {{-- Stats — all 4 in one row --}}
                            <div class="flex items-center gap-2 flex-wrap border-t pt-1 mt-1">
                                <div class="flex items-center gap-1 text-[10px] text-gray-500">
                                    <flux:icon.building-office-2 variant="outline" class="size-5 shrink-0" />
                                    <span>{{ $society->total_flats }}</span>
                                </div>
                                <div class="flex items-center gap-1 text-[10px] text-gray-500">
                                    <flux:icon.squares-2x2 variant="outline" class="size-5 shrink-0" />
                                    <span>{{ $society->no_of_shares ?? 0 }}</span>
                                </div>
                                <div
                                    class="flex items-center gap-1 text-[10px] {{ $society->i_register ? 'text-green-600' : 'text-gray-400' }}">
                                    <flux:icon.document-check variant="outline" class="size-5 shrink-0" />
                                    <span>I</span>
                                </div>
                                <div
                                    class="flex items-center gap-1 text-[10px] {{ $society->j_register ? 'text-green-600' : 'text-gray-400' }}">
                                    <flux:icon.document-check variant="outline" class="size-5 shrink-0" />
                                    <span>J</span>
                                </div>
                                <div class="flex items-center gap-1 text-[10px] text-gray-400 italic ml-auto">
                                    <flux:icon.user-circle variant="outline" class="size-5 shrink-0" />
                                    <span class="truncate max-w-[80px]">{{ $society->admin->name ?? 'None' }}</span>
                                </div>
                            </div>
                        </div>

                        @empty
                            <div class="p-8 text-center border-2 border-dashed border-gray-200 rounded-lg">
                                <p class="text-gray-500 text-sm">No societies found matching "{{ $search }}"</p>
                            </div>
                        @endforelse
                    </div>
                </aside>
                <div wire:loading.flex wire:target="selectSociety" class="justify-center items-center py-4 px-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-2 border-t-transparent border-green-500"></div>
                    <span class="ml-2 text-sm text-gray-600">Loading...</span>
                </div>
                <!-- Main -->
                <main class="col-span-2 p-2" wire:loading.remove wire:target="selectSociety">
                    @if ($selectedSocietyId && $societyById)
                        {{-- HEADER --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl border shadow-sm mx-1 mt-1 mb-2 p-4 md:p-5">
                            {{-- Title + badges --}}
                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-2 mb-4 items-start">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h1
                                                class="text-xl md:text-2xl font-bold text-blue-900 dark:text-white uppercase tracking-tight break-words">
                                                {{ $societyById->society_name }}
                                            </h1>
                                            <span
                                                class="inline-flex items-center gap-1 rounded-sm px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ strtolower($societyById->is_list_of_signed_member_available) === 'yes' ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-600' }}">
                                                <flux:icon.users variant="mini" class="size-3 shrink-0" />
                                                <span class="whitespace-nowrap">Signed Mbrs:
                                                    {{ $societyById->is_list_of_signed_member_available ?? 'No' }}</span>
                                            </span>

                                            <span
                                                class="inline-flex items-center gap-1 rounded-sm px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ strtolower($societyById->is_byelaws_available) === 'yes' ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-600' }}">
                                                <flux:icon.document-text variant="mini" class="size-3 shrink-0" />
                                                <span class="whitespace-nowrap">Byelaws:
                                                    {{ $societyById->is_byelaws_available ?? 'No' }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end">
                                        <flux:tooltip content="Edit Society">
                                            <button class="font-bold px-2 py-1 border rounded-md" wire:click="editSociety">
                                                <i class="fa-solid fa-edit text-sm"></i>
                                            </button>
                                        </flux:tooltip>
                                    </div>
                                </div>

                                {{-- Badges --}}
                                <div class="flex gap-2 overflow-x-auto w-full mt-2">
                                    <flux:badge color="blue" size="sm" class="whitespace-nowrap flex-shrink-0">
                                        Reg No: {{ $societyById->registration_no ?? 'N/A' }}
                                    </flux:badge>

                                    <flux:badge color="green" size="sm" class="whitespace-nowrap flex-shrink-0">
                                        Flats: {{ $societyById->total_flats }}
                                    </flux:badge>

                                    <flux:badge color="purple" size="sm" class="whitespace-nowrap flex-shrink-0">
                                        Shares: {{ $societyById->no_of_shares ?? '0' }}
                                    </flux:badge>
                                </div>
                            </div>

                            {{-- DETAILS GRID --}}
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-sm mb-3">
                                <div class="space-y-1 col-span-2">
                                    <p class="text-gray-400 font-medium uppercase text-[10px]">Location</p>
                                    <p class="text-gray-700 dark:text-gray-300 leading-tight">
                                        {{ $societyById->address_1 }}<br>
                                        {{ $societyById->city?->name }}, {{ $societyById->state?->name }}<br>
                                        {{ $societyById->pincode }}
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-gray-400 font-medium uppercase text-[10px]">Registers</p>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>I Register:</strong>
                                        {{ $societyById->i_register ?? 'N/A' }}</p>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>J Register:</strong>
                                        {{ $societyById->j_register ?? 'N/A' }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-gray-400 font-medium uppercase text-[10px]">Society Admin</p>
                                    <p class="text-gray-700 dark:text-gray-300 font-semibold">
                                        {{ $societyById->admin->name ?? 'Unassigned' }}</p>
                                    @if ($societyById->admin)
                                        <p class="text-gray-500 text-xs">{{ $societyById->admin->email }}</p>
                                    @endif
                                </div>
                                {{-- <div class="space-y-1">
                                    <p class="text-gray-400 font-medium uppercase text-[10px]">Management</p>
                                    <flux:button size="xs"
                                        wire:click="assignShareToApartment({{ $societyById->id }})">Assign
                                        Shares</flux:button>
                                </div> --}}
                            </div>
                        </div>
                        {{-- FILTER TABS --}}
                        <div class="flex gap-2 overflow-x-auto border-t py-2">
                            {{-- 1. Pending Verification --}}
                            @if ($pendingVerificationTimelineId)
                                @php
                                    $pendingVerification = $timelines->firstWhere('id', $pendingVerificationTimelineId);
                                    $pvCount = $filterCounts[$pendingVerificationTimelineId] ?? 0;
                                @endphp
                                @if ($pendingVerification)
                                    <button
                                        class="whitespace-nowrap rounded-md px-3 py-1 text-xs font-medium cursor-pointer border transition-colors {{ $filterKey == $pendingVerificationTimelineId ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                        wire:click="setFilter({{ $selectedSocietyId }}, {{ $pendingVerificationTimelineId }})">
                                        Pending {{ $pendingVerification->name }} ({{ $pvCount }})
                                    </button>
                                @endif
                            @endif
                            {{-- 2. All --}}
                            <button
                                class="whitespace-nowrap  rounded-md px-3 py-1 text-xs font-medium cursor-pointer border transition-colors {{ $filterKey == 0 ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                wire:click="setFilter({{ $selectedSocietyId }}, 0)">
                                All ({{ $filterCounts[0] ?? 0 }})
                            </button>

                            {{-- 3. Remaining Timelines --}}
                            @foreach ($timelines as $label)
                                @if ($label->id != $pendingVerificationTimelineId)
                                    @php $count = $filterCounts[$label->id] ?? 0; @endphp
                                    <button
                                        class="whitespace-nowrap rounded-md px-3 py-1 text-xs font-medium cursor-pointer border transition-colors {{ $filterKey == $label->id ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                                        wire:click="setFilter({{ $selectedSocietyId }}, {{ $label->id }})">
                                        Pending {{ $label->name }} ({{ $count }})
                                    </button>
                                @endif
                            @endforeach

                            @php $pendingCount = $filterCounts['pendingCertificateStatus'] ?? 0; @endphp
                            <button
                                class="whitespace-nowrap rounded-md px-3 py-1 text-xs font-medium cursor-pointer border transition-colors {{ $filterKey === 'certificate_pending' ? 'bg-red-500 text-white border-red-500' : 'border-red-300 text-red-600 hover:bg-red-50' }}"
                                wire:click="setFilter({{ $selectedSocietyId }}, 'certificate_pending')">
                                Certificate Status Pending ({{ $pendingCount }})
                            </button>

                            @php $changedCount = $filterCounts['changedCertificateStatus'] ?? 0; @endphp
                            <button
                                class="whitespace-nowrap rounded-md px-3 py-1 text-xs font-medium cursor-pointer border transition-colors {{ $filterKey === 'changes_required' ? 'bg-red-500 text-white border-red-500' : 'border-red-300 text-red-600 hover:bg-red-50' }}"
                                wire:click="setFilter({{ $selectedSocietyId }}, 'changes_required')">
                                Certificate Status Required Changes ({{ $changedCount }})
                            </button>
                        </div>


                        {{-- SCROLLABLE LIST --}}
                        <div class="flex-1 overflow-y-auto px-1 pb-2">
                            {{-- Loader --}}
                            <div wire:loading.flex wire:target="setFilter" class="justify-center items-center py-8">

                                <div
                                    class="animate-spin rounded-full h-6 w-6 border-2 border-t-transparent border-blue-500">
                                </div>

                                <span class="ml-2 text-sm text-gray-600">
                                    Loading...
                                </span>

                            </div>


                            {{-- Apartment list --}}
                            <div class="flex flex-col gap-3 sm:gap-4" wire:loading.remove wire:target="setFilter">

                                @livewire('menus.society-stepper', ['id' => $selectedSocietyId, 'key' => $filterKey], key($selectedSocietyId . '-' . $filterKey))

                            </div>

                        </div>
                    @else
                        {{-- Empty state --}}
                        <div class="flex flex-col items-center justify-center h-full text-gray-400 p-4 text-center">

                            <flux:icon.building-office-2 variant="outline" class="size-12 sm:size-16 mb-3 opacity-30" />

                            <p class="text-sm">
                                Select a society from the left to view details
                            </p>

                        </div>
                    @endif
                </main>
            </div>
        </div>

        {{-- <flux:modal wire:model="showAssignModal" class="!max-w-3xl w-full">
            <div class="space-y-6">
                <div class="text-lg font-bold">
                    <flux:heading size="lg">Assign Shares</flux:heading>
                </div>
                <div>
                    <div class="mb-3">
                        <flux:input type="number" :label="__('Total No of Shares :')" wire:model="no_of_shares" />
                    </div>

                    <div class="mb-3">
                        <flux:input type="number" :label="__('Each Share Value :')" wire:model="share_value" />
                    </div>

                    <div class="flex justify-end mt-4">
                        <flux:button variant="primary" wire:click="saveShares">Save</flux:button>
                        <button wire:click="closeModal" class="px-3 py-1 border rounded-md text-sm">Close</button>
                    </div>
                </div>

                <div>
                    <h3 class="text-base font-semibold mb-3">Choose Share Assignment Type</h3>
                    <div class="space-y-2 mb-4">
                        <label class="flex items-center space-x-2">
                            <input type="radio" wire:model.live="assignType" value="equal" />
                            <span class="text-sm">Assign equal number of shares to all apartments</span>
                        </label>

                        <label class="flex items-center space-x-2">
                            <input type="radio" wire:model.live="assignType" value="individual" />
                            <span class="text-sm">Assign individual number of shares to each apartment</span>
                        </label>
                    </div>

                    <div wire:loading.flex wire:target="assignType" class="justify-center items-center py-4 px-4">
                        <div class="animate-spin rounded-full h-6 w-6 border-2 border-t-transparent border-green-500"></div>
                        <span class="ml-2 text-sm text-gray-600">Loading...</span>
                    </div>
                    <div wire:loading.remove wire:target="assignType">
                        <!-- Equal shares -->
                        @if ($assignType === 'equal')
                            <div class="border rounded-md p-3 bg-gray-50">
                                <div class="mb-3">
                                    <flux:input type="number"  :label="__('No. of Shares (Each) :')" wire:model="individual_no_of_share" />
                                </div>
                                
                                <div class="mb-3">
                                    <flux:input type="number"  :label="__('Share Capital Amount (Each) :')" wire:model="share_capital_amount" />
                                </div>

                                <div class="flex justify-end">
                                    <flux:button variant="primary" type="button" wire:click="saveEqualShares">Save</flux:button>
                                </div>
                            </div>
                        @endif

                        @if ($assignType === 'individual')
                            <div class="max-h-64 overflow-y-auto border rounded-md p-3 bg-gray-50">
                                @foreach ($apartments as $index => $apt)
                                    <div class="grid grid-cols-3 gap-2 mb-2 items-center">
                                        <div>
                                        <span class="text-sm font-medium">{{ $apt['name'] }}</span>
                                        </div>
                                        <div>
                                        <flux:input type="number" wire:model="apartments.{{ $index }}.individual_no_of_share" placeholder="No of shares" />
                                        @error('apartments.' . $index . '.individual_no_of_share')
                                            <span class="text-xs text-red-500">{{ $message }}</span>
                                        @enderror
                                        </div>
                                        <div>
                                        <flux:input type="number" wire:model="apartments.{{ $index }}.share_capital_amount" placeholder="Amount" />
                                        @error('apartments.' . $index . '.share_capital_amount')
                                            <span class="text-xs text-red-500">{{ $message }}</span>
                                        @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-end mt-4">
                                <flux:button variant="primary" type="button"  wire:click="saveIndividualShares"
                                    >Save All</flux:button>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </flux:modal> --}}

        <flux:modal wire:model="showEditSocietyModal" class="!max-w-3xl w-full">
            <div class="space-y-6">
                <div class="text-lg font-bold">
                    <flux:heading size="lg">Edit Society</flux:heading>
                </div>

                @if ($edit_is_list_of_signed_member_available === 'Yes')
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
                @endif
                <form wire:submit.prevent="saveSocietyChanges">
                    <!-- SECTION 1: Basic Society Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border shadow-sm p-6 mb-4">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Society Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:input type="text" label="Society Name / सोसायटीचे नाव"
                                    wire:model="edit_society_name" id="edit_society_name" />
                            </div>
                            <div>
                                <flux:input type="text" label="Registration Certificate No / नोंदणी प्रमाणपत्र क्रमांक"
                                    wire:model="edit_registration_no" id="edit_registration_no" />
                            </div>
                            <div>
                                <flux:input type="number" label="Total No Of Building / इमारतींची एकूण संख्या"
                                    wire:model="edit_total_building" id="edit_total_building" />
                            </div>
                            <div>
                                <flux:input type="number" label="Total No Of Units / युनिट्सची एकूण संख्या"
                                    wire:model="edit_total_flats" id="edit_total_flats" />
                                @if ($selectedSocietyId)
                                    @php
                                        $currentCount = \App\Models\SocietyDetail::where(
                                            'society_id',
                                            $selectedSocietyId,
                                        )->count();
                                    @endphp
                                    @if ($currentCount > 0)
                                        <p class="text-xs text-gray-500 mt-1">Current society unit entries:
                                            {{ $currentCount }}. Cannot set below this number.</p>
                                    @endif
                                @endif
                            </div>
                            <div class="md:col-span-2">
                                <flux:textarea label="Address Line 1 / पत्ता ओळ 1" wire:model="edit_address_1"
                                    id="edit_address_1"></flux:textarea>
                            </div>
                            <div class="md:col-span-2">
                                <flux:textarea label="Address Line 2 / पत्ता ओळ 2" wire:model="edit_address_2"
                                    id="edit_address_2"></flux:textarea>
                            </div>
                            <div>
                                <flux:select wire:model.live="edit_state_id"
                                    placeholder="Choose State... / राज्य निवडा..." label="State / राज्य">
                                    <flux:select.option value="">Choose State... / राज्य निवडा...
                                    </flux:select.option>
                                    @foreach ($states as $st)
                                        <flux:select.option value="{{ $st->id }}">{{ $st->name }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div>
                                <flux:select wire:model="edit_city_id" placeholder="Choose City... / शहर निवडा..."
                                    label="City / शहर">
                                    <flux:select.option value="">Choose City...</flux:select.option>
                                    @foreach ($cities as $ct)
                                        <flux:select.option value="{{ $ct->id }}">{{ $ct->name }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            </div>
                            <div>
                                <flux:input type="text" label="Pincode / पिनकोड" wire:model="edit_pincode"
                                    id="edit_pincode" />
                            </div>
                            <div>
                                <flux:input type="number" label="Total No of Shares / शेअर्सची एकूण संख्या"
                                    wire:model="edit_no_of_shares" id="edit_no_of_shares" />
                            </div>
                            <div>
                                <flux:input type="number" label="Each Share Value / प्रत्येक शेअरची किंमत"
                                    wire:model="edit_share_value" id="edit_share_value" />
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: Signed Members & Byelaws -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border shadow-sm p-6 mb-4">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Documents & Members</h3>
                        <div class="space-y-6">
                            <!-- Is list of signed member available? -->
                            <div>
                                <flux:label>
                                    {{ __('Is list of signed member available? / स्वाक्षरी केलेल्या सदस्यांची यादी उपलब्ध आहे का?') }}
                                </flux:label>
                                <flux:radio.group wire:model.live="edit_is_list_of_signed_member_available"
                                    class="flex gap-4 mt-2">
                                    <flux:radio value="Yes" label="Yes / होय" />
                                    <flux:radio value="No" label="No / नाही" />
                                </flux:radio.group>
                            </div>

                            @if ($signedMembersMessage)
                                <div class="md:col-span-2 mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-sm">
                                    {!! nl2br(e($signedMembersMessage)) !!}
                                </div>
                            @endif
                            <!-- Excel uploader -->
                            @if ($showSignedMembersUploader)
                                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 border rounded-lg">
                                    <flux:input type="file"
                                        label="Upload Signed Members Excel File / स्वाक्षरी केलेल्या सदस्यांचा एक्सेल फाइल अपलोड करा"
                                        wire:model="signedMembersFile" accept=".xlsx,.xls" />
                                    @error('signedMembersFile')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                    <div class="flex justify-between mt-4 gap-2">
                                        <flux:button variant="filled" type="button" wire:click="excelExport">
                                            {{ __('EXCEL EXPORT') }}
                                        </flux:button>
                                        <flux:button variant="primary" type="button" wire:click="uploadSignedMembers"
                                            wire:loading.attr="disabled">
                                            {{ __('EXCEL IMPORT') }}
                                        </flux:button>
                                    </div>
                                </div>
                            @endif

                            <!-- Divider -->
                            <flux:separator variant="subtle" />

                            <!-- Is byelaws available? -->
                            <div>
                                <flux:label>{{ __('Is byelaws available? / बायलॉज उपलब्ध आहेत का?') }}</flux:label>
                                <flux:radio.group wire:model.live="edit_is_byelaws_available" class="flex gap-4 mt-2">
                                    <flux:radio value="Yes" label="Yes / होय" />
                                    <flux:radio value="No" label="No / नाही" />
                                </flux:radio.group>
                            </div>
                        </div>
                    </div>
                    <!-- Action Buttons -->
                    <!-- Error Messages Above Submit Button -->
                    <livewire:menus.alerts type="error" />
                    <div class="flex justify-end gap-2 mt-2">
                        <button type="button" wire:click="closeEditSocietyModal"
                            class="px-4 py-2 rounded-md border text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition">Cancel</button>
                        <flux:button variant="primary" type="submit">Save Changes</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </div>
