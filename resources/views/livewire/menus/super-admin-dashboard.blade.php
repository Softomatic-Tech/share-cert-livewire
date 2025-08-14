<div class="w-full">
    <div class="relative mb-2 w-full">
        <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Welcome Super Admin!') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    {{-- <div class="flex flex-col lg:flex-row gap-4 mt-2">
        <!-- Card 1 -->
        <div class="bg-yellow-200 rounded p-4 flex-1 shadow">
        <h2 class="text-sm font-bold uppercase text-black mb-2">Total Societies</h2>
        <p class="text-2xl font-bold text-black">{{ $totalSocieties }}</p>
        </div>
        <!-- Card 2 -->
        <div class="bg-red-200 rounded p-4 flex-1 shadow">
        <h2 class="text-sm font-bold uppercase text-black mb-2">Total Apartments</h2>
        <p class="text-2xl font-bold text-black">{{ $totalApartments }}</p>
        </div>

        <!-- Card 3 -->
        <div class="bg-green-200 rounded p-4 flex-1 shadow">
        <h2 class="text-sm font-bold uppercase text-black mb-2">Total Users</h2>
        <p class="text-2xl font-bold text-black">{{ $totalUsers }}</p>
        </div>
    </div> --}}
    
    <div class="flex flex-wrap gap-4 p-4">
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600">
        <p class="text-base font-medium dark:text-white"><i class="fa-solid fa-landmark"></i> Societies</p>
        <p class=" text-3xl font-bold dark:text-white">{{ $totalSocieties }}</p>
        </div>
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600">
        <p class="text-base font-medium dark:text-white"><i class="fa-solid fa-building"></i> Apartments</p>
        <p class="text-3xl font-bold dark:text-white">{{ $totalApartments }}</p>
        </div>
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600">
        <p class="text-base font-medium dark:text-white"><i class="fa-solid fa-users"></i> Users</p>
        <p class="text-3xl font-bold dark:text-white">{{ $totalUsers }}</p>
        </div>
    </div>
</div>
