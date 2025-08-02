<div class="w-full">
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold">View Societies</h1>
        <div class="flex gap-4">
            <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 rounded" wire:click="redirectToCreateSociety">Add Society</button>
            <button type="button" class="bg-stone-500 text-white font-bold py-2 px-4 rounded" x-on:click="window.history.back()">Back</button>
        </div>
    </div>

    @foreach($societyDetail as $detail)
    <div class="grid grid-cols-2 gap-4 py-4 border-b border-gray-300 cursor-pointer" wire:click="redirectToApartment({{ $detail->society_id }},{{ $societyStatus }})">
        <div>
            <h3 class="font-bold text-lg">{{ $detail->society->society_name }}</h3>
            <h3>Total flats : {{ $detail->society->total_flats }}</h3>
        </div>

        <div>
            <strong>Address:</strong> 
                @if($detail->society->address_1){{ $detail->society->address_1 }},@endif
                @if($detail->society->address_2){{ $detail->society->address_2 }},@endif
                @if($detail->society->city){{ $detail->society->city }},@endif
                @if($detail->society->state){{ $detail->society->state }}@endif
                @if($detail->society->pincode) - {{ $detail->society->pincode }}@endif
        </div>
    </div>
    @endforeach
</div>