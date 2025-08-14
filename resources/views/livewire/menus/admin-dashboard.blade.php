
<div class="w-full">
    <div class="relative mb-2 w-full">
        <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('Welcome Admin!') }}</flux:subheading>
        </div>
        <div class="flex gap-4">
            <flux:tooltip content="Add Society">
            <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click="redirectToCreateSociety"><i class="fa-solid fa-plus"></i></button>
            </flux:tooltip>
            <flux:tooltip content="Add Apartment To Society">
            <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click="redirectToCreateApartment"><i class="fa-solid fa-building"></i></button>
            </flux:tooltip>
        </div>
    </div>
    <flux:separator variant="subtle" />

    {{-- <div class="flex flex-col lg:flex-row gap-4 mt-2">
        <!-- Card 1 -->
        @if($pendingApplicationCount>0)
        <div class="bg-yellow-200 rounded p-4 flex-1 shadow cursor-pointer" wire:click="redirectToSocietyDetail(1)">
        @else
        <div class="bg-yellow-200 rounded p-4 flex-1 shadow">
        @endif
        <h2 class="text-sm font-bold uppercase text-black mb-2">Pending Application</h2>
        <p class="text-2xl font-bold text-black">{{ $pendingApplicationCount }}</p>
        </div>
        <!-- Card 2 -->
        @if($pendingVerificationCount>0)
        <div class="bg-red-200 rounded p-4 flex-1 shadow cursor-pointer" wire:click="redirectToSocietyDetail(2)">
        @else
        <div class="bg-red-200 rounded p-4 flex-1 shadow">
        @endif
        <h2 class="text-sm font-bold uppercase text-black mb-2">Pending Verification</h2>
        <p class="text-2xl font-bold text-black">{{ $pendingVerificationCount }}</p>
        </div>

        <!-- Card 3 -->
        @if($rejectedVerificationCount>0)
        <div class="bg-green-200 rounded p-4 flex-1 shadow cursor-pointer" wire:click="redirectToSocietyDetail(3)">
        @else
        <div class="bg-green-200 rounded p-4 flex-1 shadow">
        @endif  
        <h2 class="text-sm font-bold uppercase text-black mb-2">Rejected Verification</h2>
        <p class="text-2xl font-bold text-black">{{ $rejectedVerificationCount }}</p>
        </div>

        <!-- Card 4 -->
        <div class="bg-blue-200 rounded p-4 flex-1 shadow">
        <h2 class="text-sm font-bold uppercase text-black mb-2">Certificate Issue</h2>
        <p class="text-2xl font-bold text-black">{{ $pendingApplicationCount }}</p>
        </div>
    </div> --}}

    <div class="flex flex-wrap gap-4 p-4">
        @if($pendingVerificationCount>0)
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600 cursor-pointer" wire:click="redirectToSocietyDetail(1)">
        @else
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600">
        @endif
        <div class="flex items-center gap-2">
            <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-gray-600"><i class="fa-solid fa-inbox"></i></span> 
            <p class="text-base font-medium dark:text-white">Pending Application</p>
        </div>
        <p class=" text-3xl font-bold dark:text-white">{{ $pendingApplicationCount }}</p>
        </div>
        @if($pendingVerificationCount>0)
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600 cursor-pointer" wire:click="redirectToSocietyDetail(2)">
        @else
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600">
        @endif
        <div class="flex items-center gap-2">
            <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-gray-600"><i class="fa-solid fa-list"></i></span> 
            <p class="text-base font-medium dark:text-white">Pending Verification</p>
        </div>
        <p class="text-3xl font-bold dark:text-white">{{ $pendingVerificationCount }}</p>
        </div>
        @if($rejectedVerificationCount>0)
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600 cursor-pointer" wire:click="redirectToSocietyDetail(3)">
        @else
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600">
        @endif
        <div class="flex items-center gap-2">
            <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-gray-600"><i class="fa-solid fa-circle-xmark"></i></span> 
            <p class="text-base font-medium dark:text-white">Rejected Verification</p>
        </div>
        <p class="text-3xl font-bold dark:text-white">{{ $rejectedVerificationCount }}</p>
        </div>
        <div class="flex min-w-[158px] flex-1 flex-col gap-2 rounded-xl p-6 bg-gray-200 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600">
            <div class="flex items-center gap-2">
                <span class="w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-gray-600"><i class="fa-solid fa-certificate"></i></span> 
                <p class="text-base font-medium dark:text-white">Certificate Issue</p>
            </div>
            <p class="text-3xl font-bold dark:text-white">{{ $pendingApplicationCount }}</p>
        </div>
    </div>

    <div class="py-4">
        <livewire:menus.alerts />
    </div>
    
    {{-- <div class="sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
            <h1 class="text-xl font-bold">User Administration :</h1>
            <div class="overflow-x-auto">
                <table
                class="min-w-full text-start text-sm font-light text-surface dark:text-white">
                <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
                    <tr>
                        <th scope="col" class="px-6 py-2">#</th>
                        <th scope="col" class="px-6 py-2">User</th>
                        <th scope="col" class="px-6 py-2">Mobile No</th>
                        <th scope="col" class="px-6 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                    @foreach($users as $user)
                    <tr class="border-b border-neutral-200 dark:border-white/10">
                        <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $i++ }}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{ $user->name }}</td>
                        <td class="whitespace-nowrap px-6 py-4">@if($user->phone){{ $user->phone }}@endif</td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($user->role_id==3)
                            <flux:tooltip content="Mark As Admin">
                            <button type="button" class="bg-green-500 font-bold py-2 px-4 border  rounded" wire:click="markRole({{ $user->id }}, 2)"><i class="fa-solid fa-user-tie"></i></button>
                            </flux:tooltip>
                            @elseif($user->role_id==2)
                            <flux:tooltip content="Mark As user">
                            <button type="button" class="bg-amber-500 font-bold py-2 px-4 border rounded" wire:click="markRole({{ $user->id }}, 3)"><i class="fa-solid fa-users"></i></button>
                            </flux:tooltip>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    {{ $users->links() }}
                </tbody>
                </table>
            </div>  
        </div>
    </div> --}}

    <div class="shadow rounded-lg p-4 flex flex-col max-w-full w-full md:w-[800px] mt-4">
        <div class="overflow-y-auto max-h-[300px]">
        <table class="min-w-full table-auto border text-sm text-left">
            <thead class="min-w-full text-start text-sm font-light text-surface dark:text-white">
            <tr>
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Name</th>
                <th class="px-4 py-2 border">Phone</th>
                <th class="px-4 py-2 border">Action</th>
            </tr>
            </thead>
            <tbody>
            @php $i=1; @endphp
            @foreach($users as $index => $user)
            <tr>
                <td class="px-4 py-2 border">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                <td class="px-4 py-2 border">{{ $user->name }}</td>
                <td class="px-4 py-2 border">{{ $user->phone }}</td>
                <td class="px-4 py-2 border text-center">
                 @if($user->role_id==3)
                    <flux:tooltip content="Mark As Admin">
                    <button type="button" class="bg-gray-300 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600 font-bold py-2 px-4 border  rounded" wire:click="markRole({{ $user->id }}, 2)"><i class="fa-solid fa-user-tie"></i></button>
                    </flux:tooltip>
                    @elseif($user->role_id==2)
                    <flux:tooltip content="Mark As user">
                    <button type="button" class="bg-gray-300 dark:bg-gray-600 hover:bg-zinc-100 dark:hover:bg-zinc-600 font-bold py-2 px-4 border rounded" wire:click="markRole({{ $user->id }}, 3)"><i class="fa-solid fa-users"></i></button>
                    </flux:tooltip>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        <div class="m-2">
            {{ $users->links() }}
        </div>
    </div>
</div>
