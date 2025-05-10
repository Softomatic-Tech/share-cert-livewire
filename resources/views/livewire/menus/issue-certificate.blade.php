<section class="w-full">
    <!-- Society Card -->
    <div class="mt-8 bg-white border border-gray-300 rounded-xl shadow p-6">
        <h2 class="text-2xl text-center font-semibold">{{ $society->society_name }}</h2>
        @foreach($society->apartments as $apartment)
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-semibold">{{ $apartment->building_name }}-{{ $apartment->apartment_number }}</h2>
                <p class="font-semibold">Owner</p>
                @foreach($apartment->owners as $owner)
                <ul class="text-sm mt-1 space-y-1">
                    <li>– {{ $owner->owner_name }}</li>
                </ul>
                @endforeach
            </div>
            <div class="text-right">
                <p class="font-semibold">Contact</p>
                @foreach($apartment->owners as $owner)
                <ul class="text-sm mt-1 space-y-1">
                    <li>– {{ $owner->phone }}</li>
                </ul>
                @endforeach
            </div>
        </div>
        @endforeach
        <!-- Status Line -->
        <div class="mb-4">
            <p class="font-semibold">Share Certificate - <span class="text-green-600">Verification</span></p>
        </div>

        <!-- Step Tracker -->
        <div class="flex justify-between items-center relative">
            <!-- Line -->
            <div class="absolute left-0 right-0 h-1 bg-cyan-500 mb-4 z-0"></div>

            <!-- Steps -->
            <div class="relative">
            <div class="w-6 h-6 bg-emerald-500 rounded-full ml-0 border-2 border-black"></div>
            <p class="text-sm mt-1">Apply</p>
            </div>
            <div class="relative">
            <div class="w-6 h-6 bg-green-500 rounded-full mx-auto border border-black"></div>
            <p class="text-sm mt-1">Verification</p>
            </div>
            <div class="relative">
            <div class="w-6 h-6 bg-orange-500 rounded-full mx-auto border border-black"></div>
            <p class="text-sm mt-1">Submitted</p>
            </div>
            <div class="relative">
            <div class="w-6 h-6 bg-white border border-black rounded-full ml-4"></div>
            <p class="text-sm mt-1">Issued</p>
            </div>
        </div>

        <!-- Message -->
        <div class="mt-6 text-center font-semibold text-gray-700">
            Your Application Is Verified and accepted. Share Certificate Will be issued soon
        </div>
    </div>
</section>
