
<div>
    <div class="w-full">
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
            <aside class="border-r flex flex-col md:flex-row p-2 space-y-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">SOCIETIES</h2>
                <div class="space-y-4">
                    @foreach($societies as $index => $society)
                        <div wire:click="selectSociety({{ $society->id }})"
                            class="p-4 rounded-lg border hover:shadow-md transition-shadow cursor-pointer">
                            <div class="flex flex-col gap-3">
                                <p class="text-gray-900 dark:text-white text-base font-bold">{{ $society->society_name }}</p>
                                <p class="text-gray-500 dark:text-white text-sm">
                                    @if($society->address_1){{ $society->address_1 }},@endif
                                    @if($society->city?->name){{ $society->city?->name  }},@endif
                                    @if($society->state?->name){{ $society->state?->name  }},@endif
                                    @if($society->pincode){{ $society->pincode }}@endif</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </aside>
            <div wire:loading.flex wire:target="selectSociety" class="justify-center items-center py-4 px-4">
                <div class="animate-spin rounded-full h-6 w-6 border-2 border-t-transparent border-green-500"></div>
                <span class="ml-2 text-sm text-gray-600">Loading...</span>
            </div>
            <!-- Main -->
            <main class="col-span-2 p-2" wire:loading.remove wire:target="selectSociety">
                @if($selectedSocietyId)
                <header class="mb-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white"> {{ $societyName }}
                        Flats</h1>
                </header>

                <!-- Filters -->
                <div class="flex items-center gap-4 mb-4">
                    @php $startKey=0; @endphp
                    <button class="border items-center justify-center gap-2 rounded-md px-4 py-2 text-xs font-medium cursor-pointer" wire:click="setFilter({{ $selectedSocietyId }},{{ $startKey }})">
                        All
                    </button>
                    @foreach($timelines as $label)
                        <button class="border items-center justify-center gap-2 rounded-md px-4 py-2 text-xs font-medium cursor-pointer" wire:click="setFilter({{ $selectedSocietyId }},{{ $label->id }})">
                            Pending {{ $label->name }}
                        </button>
                    @endforeach
                    <button type="button" class="border items-center justify-center gap-2 rounded-md px-4 py-2 text-xs font-medium cursor-pointer" wire:click="assignShareToApartment({{ $selectedSocietyId }})">Assign Shares</button>
                </div>
                <!-- Loader -->
                <div wire:loading.flex wire:target="setFilter" class="justify-center items-center py-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-2 border-t-transparent border-green-500"></div>
                    <span class="ml-2 text-sm text-gray-600">Loading...</span>
                </div>
                <!-- Flats -->
                <div class="flex flex-col gap-4" wire:loading.remove wire:target="setFilter">
                        @livewire('menus.society-stepper', ['id' => $selectedSocietyId,'key'=>$filterKey],key($selectedSocietyId.'-'.$filterKey))
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
            @if ($step === 1)
                <!-- STEP 1: Basic Share Details Form -->
                <div>
                    <div class="mb-3">
                        <flux:input type="number" :label="__('Total No of Shares :')" wire:model="no_of_shares" />
                    </div>

                    <div class="mb-3">
                        <flux:input type="number" :label="__('Each Share Value :')" wire:model="share_value" />
                    </div>

                    <div class="flex justify-end mt-4">
                        <flux:button variant="primary" wire:click="saveShares">Save & Next</flux:button>
                    </div>
                </div>
            @endif

            @if ($step === 2)
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
            @endif

            <div class="flex justify-end mt-4">
                <button wire:click="closeModal" class="px-3 py-1 border rounded-md text-sm">Close</button>
            </div>
        </div>
    </flux:modal>
    </div>
