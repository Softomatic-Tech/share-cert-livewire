
<div>
    <div class="w-full">
        <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading size="lg" class="mb-4">{{ __('Welcome Admin!') }}</flux:subheading>
        </div>
        <div class="flex gap-4">
            <flux:tooltip content="Create Society">
            <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click="redirectToCreateSociety"><i class="fa-solid fa-plus"></i></button>
            </flux:tooltip>
            <flux:tooltip content="Add Apartment To Society">
            <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click="redirectToCreateApartment"><i class="fa-solid fa-building"></i></button>
            </flux:tooltip>
        </div>
    </div>
    <flux:separator variant="subtle" />
    <div class="grid grid-cols-1 md:grid-cols-5 p-2">
        <div class="p-2">
            @if($pendingApplicationCount>0)
            <div class="cursor-pointer bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center" wire:click="redirectToSocietyDetail(1)">
            @else
            <div class="bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center">
            @endif
                <div class="flex items-center justify-center text-center gap-2">
                    <span class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-inbox text-sm"></i></span> 
                    <p class="text-sm font-medium dark:text-white">Pending Application</p>
                </div>
                <p class=" text-2xl font-bold text-center dark:text-white">{{ $pendingApplicationCount }}</p>
            </div>
        </div>
        <div class="p-2">
            @if($pendingVerificationCount>0)
            <div class="cursor-pointer bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center" wire:click="redirectToSocietyDetail(2)">
            @else
            <div class="bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center">
            @endif
                <div class="flex items-center justify-center text-center gap-2">
                    <span class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-list text-sm"></i></span> 
                    <p class="text-sm font-medium dark:text-white">Pending Verification</p>
                </div>
                <p class="text-2xl font-bold text-center dark:text-white">{{ $pendingVerificationCount }}</p>
            </div>
        </div>
        <div class="p-2">
            @if($rejectedVerificationCount>0)
            <div class="cursor-pointer bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center" wire:click="redirectToSocietyDetail(3)">
            @else
            <div class="bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center">
            @endif
                <div class="flex items-center justify-center text-center gap-2">
                    <span class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-circle-xmark text-sm"></i></span> 
                    <p class="text-sm font-medium dark:text-white">Rejected Verification</p>
                </div>
                <p class="text-2xl font-bold text-center dark:text-white">{{ $rejectedVerificationCount }}</p>
            </div>
        </div>
        <div class="p-2">
            <div class="bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center">
                <div class="flex items-center justify-center text-center gap-2">
                    <span class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-certificate text-sm"></i></span> 
                    <p class="text-sm font-medium dark:text-white">Certificate Issue</p>
                </div>
                <p class="text-2xl font-bold text-center dark:text-white">0</p>
            </div>
        </div>
        <div class="p-2">
            <div class="cursor-pointer bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center" wire:click="redirectToMarkRole">
                <div class="flex items-center justify-center text-center gap-2">
                    <span class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-users text-sm"></i></span> 
                    <p class="text-sm font-medium dark:text-white">Mark User Role</p>
                </div>
                <p class="text-2xl font-bold text-center dark:text-white">{{ $usersCount }}</p>
            </div>
        </div>
    </div>
        
    {{-- <div class="grid grid-cols-1 md:grid-cols-2">
        <div class="py-2 w-64">
            <flux:select wire:model.live="selectedOption" placeholder="Choose Society...">
                <flux:select.option value="">Choose Society...</flux:select.option>
                @foreach($society  as $row)
                    <flux:select.option value="{{ $row->id }}">{{ $row->society_name }} , (Total Flats: {{ $row->total_flats }})</flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <div class="p-2">
            <div x-data="{ tab: '' }" class="space-y-4">
                <div class="flex border-b">
                    <button @click="tab = 'pending'" :class="tab === 'pending' ? 'border-amber-500 text-amber-600' : 'text-gray-500'" class="px-4 py-2 border-b-2 font-medium">
                    Pending ({{ $pendingVerificationStatusCount }})
                    </button>
                    <button @click="tab = 'approved'" :class="tab === 'approved' ? 'border-amber-500 text-amber-600' : 'text-gray-500'" class="px-4 py-2 border-b-2 font-medium">
                    Approved ({{ $approvedVerificationStatusCount }})
                    </button>
                    <button @click="tab = 'rejected'" :class="tab === 'rejected' ? 'border-amber-500 text-amber-600' : 'text-gray-500'" class="px-4 py-2 border-b-2 font-medium">
                    Rejected ({{ $rejectedVerificationStatusCount }})
                    </button>
                </div>
                <div x-show="tab === 'pending'" class="p-4">
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Pending'], key("'status-table-'.$selectedOption"))
                </div>
                <div x-show="tab === 'approved'" class="p-4" x-cloak>
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Approved'], key("'status-table-'.$selectedOption"))
                </div>
                <div x-show="tab === 'rejected'" class="p-4" x-cloak>
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Rejected'], key("'status-table-'.$selectedOption"))
                </div>
            </div>
        </div>
    </div> --}}
    
    <div class="card">
        <div class="card-body">
            <div x-data="{ activeTab: ''}" class="w-full p-4">
                <!-- Tabs + Dropdown wrapper -->
                <div class="grid grid-cols-1 md:grid-cols-2 border-b border-gray-300 mb-4">
                    <!-- Dropdown first on mobile, second on desktop -->
                    <div class="order-1 sm:order-2 mb-3 sm:mb-0">
                        <flux:select wire:model.live="selectedOption" placeholder="Choose Society..." placement="top-end">
                            <flux:select.option value="">Choose Society...</flux:select.option>
                            @foreach($society  as $row)
                                <flux:select.option value="{{ $row->id }}">{{ $row->society_name }} , (Total Flats: {{ $row->total_flats }})</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <!-- Tabs second on mobile, first on desktop -->
                    <div class="order-2 sm:order-1 flex space-x-6">
                        <button @click="activeTab = 'pending'"
                        :class="activeTab === 'pending' ? 'text-amber-600 border-b-2 border-amber-600 font-semibold' : 'text-zinc-600 dark:text-white hover:text-amber-600 border-b-2 border-transparent text-[10px] sm:text-sm'"
                        class="py-2 px-4">
                        Pending ({{ $pendingVerificationStatusCount }})
                        </button>
                        <button @click="activeTab = 'approved'"
                        :class="activeTab === 'approved' ? 'text-amber-600 border-b-2 border-amber-600 font-semibold' : 'text-zinc-600 dark:text-white hover:text-amber-600 border-b-2 border-transparent text-[10px] sm:text-sm'"
                        class="py-2 px-4">
                        Approved ({{ $approvedVerificationStatusCount }})
                        </button>
                        <button @click="activeTab = 'rejected'"
                        :class="activeTab === 'rejected' ? 'text-amber-600 border-b-2 border-amber-600 font-semibold' : 'text-zinc-600 dark:text-white hover:text-amber-600 border-b-2 border-transparent text-[10px] sm:text-sm'"
                        class="py-2 px-4">
                        Rejected ({{ $rejectedVerificationStatusCount }})
                        </button>
                    </div>
                </div>  
                <!-- Tab Content -->
                <div>
                <!-- Tab 1 -->
                <div x-show="activeTab === 'pending'" class="bg-stone-200 dark:bg-stone-800 shadow rounded-lg p-4">
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Pending'], key("'status-table-'.$selectedOption"))
                </div>

                <!-- Tab 2 -->
                <div x-show="activeTab === 'approved'" class="bg-stone-200 dark:bg-stone-800 shadow rounded-lg p-4">
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Approved'], key("'status-table-'.$selectedOption"))
                </div>

                <!-- Tab 3 -->
                <div x-show="activeTab === 'rejected'" class="bg-stone-200 dark:bg-stone-800 shadow rounded-lg p-4">
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Rejected'], key("'status-table-'.$selectedOption"))
                </div>
            </div>
        </div>
    </div>
</div>
