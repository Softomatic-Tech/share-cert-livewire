    <div class="w-full">
        <div class="flex justify-between">
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        </div>
    
        <div class="grid gap-6 md:grid-cols-3 mt-2">
            <div class="card">
                @if($pendingApplicationCount>0)
                <div class="card-body bg-amber-200 cursor-pointer" wire:click="redirectToSociety(1)">
                @else
                <div class="card-body bg-amber-200">
                @endif
                    <h2 class="text-center font-bold text-xl text-amber-900">PENDING APPLICATION</h2>
                    <p class="font-bold text-2xl text-amber-900">{{ $pendingApplicationCount }}</p>
                </div>
            </div>
            <div class="card">
                @if($pendingVerificationCount>0)
                <div class="card-body bg-red-200 cursor-pointer" wire:click="redirectToSociety(2)">
                @else
                <div class="card-body bg-red-200">
                @endif
                    <h2 class="text-center font-bold text-xl text-red-900">PENDING VERIFICATION</h2>
                    <p class="font-bold text-2xl text-red-900">{{ $pendingVerificationCount }}</p>
                </div>
            </div>
            
            <div class="card">
                @if($rejectedVerificationCount>0)
                <div class="card-body bg-emerald-200 cursor-pointer" wire:click="redirectToSociety(3)">
                @else
                <div class="card-body bg-emerald-200">
                @endif
                    <h2 class="text-center font-bold text-xl text-emerald-900">REJECTED VERIFICATION</h2>
                    <p class="font-bold text-2xl text-emerald-900">{{ $rejectedVerificationCount }}</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body bg-blue-200">
                    <h2 class="text-center font-bold text-xl text-blue-900">CERTIFICATE ISSUE</h2>
                    <p class="font-bold text-2xl text-blue-900">10</p>
                </div>
            </div>
        </div>

        <div class="w-full mb-4 mt-2">
            <div class="py-4">
                <livewire:menus.alerts />
            </div>
            
            <div class="sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <div class="overflow-x-auto">
                        @if($users->isNotEmpty())
                        <table
                        class="min-w-full text-start text-sm font-light text-surface dark:text-white">
                        <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
                            <tr>
                                <th scope="col" class="px-6 py-4">#</th>
                                <th scope="col" class="px-6 py-4">User</th>
                                <th scope="col" class="px-6 py-4">Email</th>
                                <th scope="col" class="px-6 py-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="border-b border-neutral-200 dark:border-white/10">
                                <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $user->id }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $user->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4">{{ $user->email }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if($user->role_id==3)
                                    <button type="button" class="bg-green-500 text-white font-bold py-2 px-4 border border-green-700 rounded" wire:click="markRole({{ $user->id }}, 2)">Mark As Admin</button>
                                    @elseif($user->role_id==2)
                                    <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 border border-amber-700 rounded" wire:click="markRole({{ $user->id }}, 3)">Mark As User</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                        @endif
                    </div>  
                </div>
            </div>
    </div>
