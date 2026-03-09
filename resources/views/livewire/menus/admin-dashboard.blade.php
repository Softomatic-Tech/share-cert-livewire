
<div>
    <div class="w-full px-2">
        <div class="flex justify-between items-center">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('admin.dashboard') }}">Admin Dashboard</flux:breadcrumbs.item>
            </flux:breadcrumbs>
            <div class="flex gap-4">
                <flux:tooltip content="Create Society">
                <button type="button" class="border font-bold py-1 px-2 rounded cursor-pointer" wire:click="redirectToCreateSociety"><i class="fa-solid fa-plus"></i></button>
                </flux:tooltip>
                <flux:tooltip content="Add Apartment To Society">
                <button type="button" class="border font-bold py-1 px-2 rounded cursor-pointer" wire:click="redirectToCreateApartment"><i class="fa-solid fa-building"></i></button>
                </flux:tooltip>
            </div>
        </div>
        <flux:separator variant="subtle" />
        <div class="my-2">
            <livewire:menus.alerts />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 min-h-screen">
            <!-- Sidebar -->
            <aside class="col-span-1 border-r p-2">
                <div class="mb-2 flex-shrink-0">
                    <flux:input type="text" placeholder="Search Society..." wire:model.live="search" size="sm">
                        <x-slot name="iconTrailing">
                            @if ($search)
                            <flux:button size="sm" variant="subtle" icon="x-mark" class="-mr-1" wire:click="$set('search', '')" />
                            @endif
                            <flux:icon.magnifying-glass variant="outline" class="size-4 text-gray-400" />
                        </x-slot>
                    </flux:input>
                </div>

                <div class="space-y-4">
                    @forelse($societies as $index => $society)
                        <div wire:click="selectSociety({{ $society->id }})"
                                wire:key="society-{{ $society->id }}" 
                                class="p-2 rounded-lg border hover:shadow-md transition-all cursor-pointer @if($selectedSocietyId == $society->id) active-society @endif">
                            {{-- Society name (left) & Reg No (right) on same line --}}
                            <div class="flex items-start justify-between gap-1 mb-0.5">
                                <p class="text-blue-800 dark:text-white text-sm font-bold leading-tight">
                                    {{ $society->society_name }}
                                </p>
                                <p class="text-gray-400 dark:text-gray-500 text-[10px] font-mono shrink-0 mt-0.5">
                                    {{ $society->registration_no ?? 'N/A' }}
                                </p>
                                @if($society->changes_required_count > 0)
                                    <flux:badge color="red" size="xs" variant="solid" class="ml-1">
                                        {{ $society->changes_required_count }} Changes
                                    </flux:badge>
                                @endif
                            </div>

                            {{-- Address — wraps fully --}}
                            <p class="text-gray-500 dark:text-gray-400 text-[11px] leading-snug mb-2 whitespace-normal">
                                {{ $society->address_1 }}@if($society->city?->name), {{ $society->city->name }}@endif
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
                @if($selectedSocietyId && $societyById)
                    {{-- HEADER --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border shadow-sm mx-1 mt-1 mb-2 p-4 md:p-5">
                        {{-- Title + badges --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                            {{-- Society name --}}
                            <div>
                                <h1 class="text-xl md:text-2xl font-bold text-blue-900 dark:text-white uppercase tracking-tight">
                                    {{ $societyById->society_name }}
                                </h1>
                            </div>

                            {{-- Badges --}}
                            <div class="flex gap-2 overflow-x-auto sm:justify-end whitespace-nowrap pb-1">
                                <flux:badge color="blue" size="sm">
                                    Reg No: {{ $societyById->registration_no ?? 'N/A' }}
                                </flux:badge>

                                <flux:badge color="green" size="sm">
                                    Flats: {{ $societyById->total_flats }}
                                </flux:badge>

                                <flux:badge color="purple" size="sm">
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
                                @if($societyById->admin)
                                    <p class="text-gray-500 text-xs">{{ $societyById->admin->email }}</p>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <p class="text-gray-400 font-medium uppercase text-[10px]">Management</p>
                                <flux:button size="xs" wire:click="assignShareToApartment({{ $societyById->id }})">Assign
                                    Shares</flux:button>
                            </div>
                        </div>
                    </div>
                    {{-- FILTER TABS --}}
                    <div class="flex gap-2 overflow-x-auto border-t py-2">
                        {{-- 1. Pending Verification --}}
                        @if($pendingVerificationTimelineId)
                            @php 
                                $pendingVerification = $timelines->firstWhere('id', $pendingVerificationTimelineId);
                                $pvCount = $filterCounts[$pendingVerificationTimelineId] ?? 0;
                            @endphp
                            @if($pendingVerification)
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
                        @foreach($timelines as $label)
                            @if($label->id != $pendingVerificationTimelineId)
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
                        <div wire:loading.flex wire:target="setFilter"
                            class="justify-center items-center py-8">

                            <div class="animate-spin rounded-full h-6 w-6 border-2 border-t-transparent border-blue-500"></div>

                            <span class="ml-2 text-sm text-gray-600">
                                Loading...
                            </span>

                        </div>


                        {{-- Apartment list --}}
                        <div class="flex flex-col gap-3 sm:gap-4"
                            wire:loading.remove wire:target="setFilter">

                            @livewire('menus.society-stepper',
                                ['id' => $selectedSocietyId, 'key' => $filterKey],
                                key($selectedSocietyId . '-' . $filterKey)
                            )

                        </div>

                    </div>
                @else
                    {{-- Empty state --}}
                    <div class="flex flex-col items-center justify-center h-full text-gray-400 p-4 text-center">

                        <flux:icon.building-office-2 variant="outline"
                            class="size-12 sm:size-16 mb-3 opacity-30" />

                        <p class="text-sm">
                            Select a society from the left to view details
                        </p>

                    </div>
                @endif
            </main>
        </div>
    </div>
    
    <flux:modal  wire:model="showAssignModal" class="!max-w-3xl w-full">
        <div class="space-y-6">
            <div class="text-lg font-bold">
                <flux:heading size="lg">Assign Shares</flux:heading>
            </div>
            {{-- @if ($step === 1) --}}
                <!-- STEP 1: Basic Share Details Form -->
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
            {{-- @endif --}}

            {{-- @if ($step === 2)
                <!-- STEP 2: Apartment-level Share Form -->
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

                        <!-- Individual shares -->
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
            @endif --}}

        </div>
    </flux:modal>
    </div>
