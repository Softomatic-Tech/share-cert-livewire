<div class="w-full">
    <div class="flex justify-between">
        <h1 class="text-xl font-bold">Admin Dashboard</h1>
        <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 rounded" wire:click="redirectToCreateSociety">Add Society</button>
    </div>

    <div class="grid gap-4 md:grid-cols-3 mt-2">
        @foreach($societies as $society)
        <div class="card">
            <div class="card-body" wire:click="redirectToSocietyPage">
                <h2 class="text-center font-semibold text-xl">{{ $society->society_name }}</h2>
            </div>
        </div>
        @endforeach
    </div>
</div>