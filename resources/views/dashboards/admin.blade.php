<x-layouts.app :title="__('Admin Dashboard')">
    <div class="w-full">
        <div class="flex justify-between">
            <h1 class="text-xl font-bold">Admin Dashboard</h1>
        </div>
    </div>
    <div class="w-full">
        <div class="w-full mb-4 mt-2">
            @if(session()->has('success'))
            <div class="px-3 py-3 mb-4 rounded-lg bg-green-500">
                <div>{{ session('success') }}</div>
            </div>
            @endif
            @if(session()->has('error'))
            <div class="px-3 py-3 mb-4 rounded-lg bg-red-500">
                <div>{{ session('error') }}</div>
            </div>
            @endif
            <div class="border p-4">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-3 py-3">Id</th>
                            <th scope="col" class="px-3 py-3">Name</th>
                            <th scope="col" class="px-3 py-3">Email</th>
                            <th scope="col" class="px-3 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($users->isNotEmpty())
                        @foreach($users as $user)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-3 py-3">{{ $user->id }}</td>
                            <td class="px-3 py-3">{{ $user->name }}</td>
                            <td class="px-3 py-3">{{ $user->email }}</td>
                            <td>
                                @if($user->role_id==3)
                                <form action="{{ route('users.markRole', $user->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="role_id" value="2"> <!-- for Admin -->
                                    <button type="submit" class="bg-green-500 text-white font-bold py-2 px-4 border border-blue-700 rounded">Mark As Admin</button>
                                </form>
                                @elseif($user->role_id==2)
                                <form action="{{ route('users.markRole', $user->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="role_id" value="3"> <!-- for User -->
                                    <button type="submit" class="bg-amber-500 text-white font-bold py-2 px-4 border border-blue-700 rounded">Mark As User</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="px-3 py-3 text-center text-gray-500">No Users Found</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
