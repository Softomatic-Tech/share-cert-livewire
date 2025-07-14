<x-layouts.app :title="__('User Dashboard')">
    <div class="relative">
        <div class="mt-6 mx-auto w-fit max-w-xl px-4">
            <div class="text-center text-2xl font-semibold text-zinc-800 dark:text-white">You Don't have any apartment registered.</div>

            <div class="text-base text-lg mt-4 text-center text-zinc-500 dark:text-zinc-300">Please <a href="{{ route('menus.register_society') }}" class="underline text-cyan-600">Click Here</a> to register!</div>
        </div>

        
        <div class="grid gap-4 md:grid-cols-3 mt-2">
            @foreach($details as $detail)
            <a href="{{ route('menus.issue-certificate',$detail->id) }}">
            <div class="card">
                <div class="card-header"><h2 class="text-center font-semibold text-2xl">{{ $detail->society->society_name }}</h2></div>
                <div class="card-body">
                    <p class="font-semibold text-lg">{{$detail->building_name}} - {{$detail->apartment_number}}</p>
                    <div class="text-sm mt-2">
                        <p><span class="font-semibold">Owner</span> – {{$detail->owner1_name}}</p>
                        <p><span class="font-semibold">Contact</span> – {{$detail->owner1_mobile}}</p>
                        <p class="mt-2"><span class="font-semibold">Number of Owners:</span> – {{ $detail->owner_count }}</p>
                        <p class="mt-2"><span class="font-semibold">Share Certificate</span> - <span class="text-green-600 font-semibold">InProcess</span></p>
                    </div>
                </div>
            </div>
            </a>
            @endforeach
        </div>
        
    </div>
    
</x-layouts.app>
