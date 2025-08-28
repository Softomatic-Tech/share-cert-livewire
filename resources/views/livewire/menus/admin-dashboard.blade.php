
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

    <div class="flex flex-wrap gap-4 p-4">
        @if($pendingApplicationCount>0)
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 cursor-pointer" wire:click="redirectToSocietyDetail(1)">
        @else
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600">
        @endif
        <div class="flex items-center justify-center text-center gap-2">
            <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-gray-600"><i class="fa-solid fa-inbox"></i></span> 
            <p class="text-lg font-medium dark:text-white">Pending Application</p>
        </div>
        <p class=" text-3xl font-bold text-center dark:text-white">{{ $pendingApplicationCount }}</p>
        </div>
        @if($pendingVerificationCount>0)
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 cursor-pointer" wire:click="redirectToSocietyDetail(2)">
        @else
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600">
        @endif
        <div class="flex items-center justify-center text-center gap-2">
            <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-gray-600"><i class="fa-solid fa-list"></i></span> 
            <p class="text-lg font-medium dark:text-white">Pending Verification</p>
        </div>
        <p class="text-3xl font-bold text-center dark:text-white">{{ $pendingVerificationCount }}</p>
        </div>
        @if($rejectedVerificationCount>0)
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 cursor-pointer" wire:click="redirectToSocietyDetail(3)">
        @else
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600">
        @endif
        <div class="flex items-center justify-center text-center gap-2">
            <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-gray-600"><i class="fa-solid fa-circle-xmark"></i></span> 
            <p class="text-lg font-medium dark:text-white">Rejected Verification</p>
        </div>
        <p class="text-3xl font-bold text-center dark:text-white">{{ $rejectedVerificationCount }}</p>
        </div>
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600">
            <div class="flex items-center justify-center text-center gap-2">
                <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-gray-600"><i class="fa-solid fa-certificate"></i></span> 
                <p class="text-lg font-medium dark:text-white">Certificate Issue</p>
            </div>
            <p class="text-3xl font-bold text-center dark:text-white">0</p>
        </div>
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 cursor-pointer" wire:click="redirectToMarkRole">
            <div class="flex items-center justify-center text-center gap-2">
                <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-gray-600"><i class="fa-solid fa-users"></i></span> 
                <p class="text-lg font-medium dark:text-white">User</p>
            </div>
            <p class="text-3xl font-bold text-center dark:text-white">{{ $usersCount }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2">
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
    </div>
</div>
