<x-layouts.app :title="__('User Dashboard')">
    <div class="relative">
        <div class="mt-6 mx-auto w-fit max-w-xl px-4">
            <div class="text-center text-2xl font-semibold text-zinc-800 dark:text-white">You Don't have any apartment registered.</div>

            <div class="text-base text-lg mt-4 text-center text-zinc-500 dark:text-zinc-300">Please <a href="{{ route('menus.register_society') }}" class="underline text-cyan-600">Click Here</a> to register!</div>
        </div>

        
        <div class="grid gap-4 md:grid-cols-3 mt-2">
            @foreach($owners as $societyId => $ownerGroup)
            @php
                $firstOwner = $ownerGroup->first();
                $society = $firstOwner->society;
                $apartment = $firstOwner->apartment;
                $countOwners = $societyOwnerCounts[$societyId] ?? 0;
            @endphp
            <a href="{{ route('menus.issue-certificate',$society->id) }}">
            <div class="card">
                <div class="card-header"><h2 class="text-center font-semibold text-2xl">{{ $society->society_name }}</h2></div>
                <div class="card-body">
                    <p class="font-semibold text-lg">{{ $apartment->building_name }}-{{ $apartment->apartment_number }}</p>
                    <div class="text-sm mt-2">
                        <p><span class="font-semibold">Owner</span> – {{ $firstOwner->owner_name }}</p>
                        <p><span class="font-semibold">Contact</span> {{ $firstOwner->phone }}</p>
                        <p class="mt-2"><span class="font-semibold">Number of Owners:</span> {{ $countOwners }}</p>
                        <p class="mt-2"><span class="font-semibold">Share Certificate</span> - <span class="text-green-600 font-semibold">InProcess</span></p>
                    </div>
                </div>
            </div>
            </a>
            @endforeach
        </div>
        
    </div>
    
</x-layouts.app>
