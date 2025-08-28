<div>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="#">Super Admin</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="#">User List</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    <flux:separator variant="subtle" />
    <div class="shadow border rounded-lg p-4 flex flex-col max-w-full w-full md:w-[800px] mt-4">
        <div class="overflow-y-auto max-h-[300px]">
        <table class="min-w-full table-auto border text-sm text-left">
            <thead class="min-w-full text-start text-sm font-light text-surface dark:text-white">
            <tr>
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Name</th>
                <th class="px-4 py-2 border">Phone</th>
            </tr>
            </thead>
            <tbody>
            @php $i=1; @endphp
            @foreach($users as $index => $user)
            <tr>
                <td class="px-4 py-2 border">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                <td class="px-4 py-2 border">{{ $user->name }}</td>
                <td class="px-4 py-2 border text-center">{{ $user->phone }}</td>
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