<div>
    <div class="w-full">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
                <flux:subheading size="lg" class="mb-3">{{ __('Welcome Super Admin!') }}</flux:subheading>
            </div>
            <div>
                <flux:tooltip content="Create Society">
                    <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click="redirectToCreateSociety"><i class="fa-solid fa-plus"></i></button>
                </flux:tooltip>
            </div>
        </div>
        <flux:separator variant="subtle" />
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-zinc-200 dark:bg-zinc-600 cursor-pointer" wire:click="redirectToSocietyList">
            <div class="flex items-center justify-center text-center gap-2">
                <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-landmark"></i></span> 
                <p class="text-xl font-bold dark:text-white">Societies</p>
            </div>
            <p class=" text-3xl text-center font-bold dark:text-white">{{ $totalSocieties }}</p>
        </div>
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-zinc-200 dark:bg-zinc-600">
            <div class="flex items-center justify-center text-center gap-2">
                <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-building"></i></span> 
                <p class="text-xl font-bold dark:text-white">Apartments</p>
            </div>
            <p class=" text-3xl text-center font-bold dark:text-white">{{ $totalApartments }}</p>
        </div>
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-zinc-200 dark:bg-zinc-600 cursor-pointer" wire:click="redirectToUserList">
            <div class="flex items-center justify-center text-center gap-2">
                <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-users"></i></span> 
                <p class="text-xl font-bold dark:text-white">Users</p>
            </div>
            <p class=" text-3xl text-center font-bold dark:text-white">{{ $totalUsers }}</p>
        </div>
    </div>
</div>
