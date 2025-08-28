<div>
    <div class="w-full mb-1">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="#">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="#">Mark Role</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>
    <div class="mb-2">
        <livewire:menus.alerts />
    </div>
    <div class="shadow border rounded-lg p-4 flex flex-col max-w-full w-full md:w-[800px] mt-4">
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
                <td class="px-4 py-2 border text-right">{{ $user->phone }}</td>
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