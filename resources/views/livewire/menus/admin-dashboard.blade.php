
<div>
    <div class="w-full">
        <div class="flex justify-between items-center">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="#">Admin Dashboard</flux:breadcrumbs.item>
            </flux:breadcrumbs>
            <div class="flex gap-4">
                <flux:tooltip content="Create Society">
                <button type="button" class="border font-bold py-1 px-2 rounded cursor-pointer" wire:click="redirectToCreateSociety"><i class="fa-solid fa-plus"></i></button>
                </flux:tooltip>
                <flux:tooltip content="Add Apartment To Society">
                <button type="button" class="border font-bold py-1 px-2 rounded cursor-pointer" wire:click="redirectToCreateApartment"><i class="fa-solid fa-building"></i></button>
                </flux:tooltip>
            </div>
        </div>
    <flux:separator variant="subtle" />
    <div class="mb-2">
        <livewire:menus.alerts />
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 min-h-screen">
    <!-- Sidebar -->
    <aside class="bg-white border-r border-gray-200 flex flex-col md:flex-row p-2 space-y-4">
        <h2 class="text-xl font-bold text-gray-800">SOCIETIES</h2>
        <div class="space-y-4">
            @foreach($societies as $index => $society)
                <div wire:click="selectSociety({{ $society->id }})"
                    class="p-4 rounded-lg border border-gray-200 bg-white hover:shadow-md transition-shadow cursor-pointer">
                    <div class="flex flex-col gap-3">
                        <p class="text-gray-900 text-base font-bold">{{ $society->society_name }}</p>
                        <p class="text-gray-500 text-sm">
                            @if($society->address_1){{ $society->address_1 }},@endif
                            @if($society->city?->name){{ $society->city?->name  }},@endif
                            @if($society->state?->name){{ $society->state?->name  }},@endif
                            @if($society->pincode){{ $society->pincode }}@endif</p>
                    </div>
                </div>
            @endforeach
        </div>
    </aside>

    <!-- Main -->
    <main class="col-span-2 p-2">
        @if($selectedSocietyId)
        <header class="mb-2">
            <h1 class="text-2xl font-bold text-gray-900"> {{ $societyName }}
                Flats</h1>
        </header>

        <!-- Filters -->
        <div class="flex items-center gap-4 mb-4">
            @foreach(['all' => 'All','Application' => 'Application Pending','Verification' => 'Verification Pending','Certificate Generated' => 'Certificate Generated','Certificate Delivered' => 'Certificate Delivered'] as $key => $label)
                <button class="border items-center justify-center gap-2 rounded-md px-4 py-2 text-xs font-medium cursor-pointer" wire:click="setFilter('{{ $selectedSocietyId }}','{{ $key }}')">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- Flats -->
        <div class="flex flex-col gap-4">
                @livewire('menus.society-stepper', ['id' => $selectedSocietyId,'key'=>$filterKey],key($selectedSocietyId.'-'.$filterKey))
        </div>
        @endif
    </main>
</div>
