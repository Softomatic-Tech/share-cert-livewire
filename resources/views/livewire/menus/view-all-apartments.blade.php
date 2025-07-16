<div class="w-full">
    <div class="flex justify-between">
        <h1 class="text-xl font-bold">Admin Dashboard</h1>
        <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 rounded" wire:click="redirectToCreateApartment">Add Apartment</button>
    </div>

    <div class="max-h-[500px] overflow-y-auto pr-2">
        <div class="p-6 bg-gray-50">
            <div class="grid grid-cols-2 gap-4 font-semibold text-gray-700 border-b pb-2 mb-2">
                <div class="text-center font-extrabold">APARTMENT</div>
                <div class="text-center font-extrabold">STATUS</div>
            </div>

            <!-- Apartment Card -->
            @foreach($societyDetails as $details)
            <div class="grid grid-cols-2 gap-4 py-4 border-b border-gray-300">
                <div class="flex gap-4 items-start">
                    <!-- Icon Box -->
                    <div class="bg-blue-200 p-3 rounded">
                        <i class="fa-solid fa-code"></i>
                    </div>

                    <!-- Apartment + Owners -->
                    <div class="flex-1">
                        <h3 class="font-bold text-lg">{{ $details->building_name }} - {{ $details->apartment_number }}</h3>

                        <!-- Owner List -->
                        <div class="mt-2 space-y-2 text-sm">
                            @if($details->owner1_name)
                            <div>
                                <div class="font-semibold">Owner 1: {{ $details->owner1_name }}</div>
                                @if($details->owner1_mobile)
                                <div class="text-gray-600 flex items-center gap-1">
                                    <i class="fa-solid fa-phone text-blue-500"></i>
                                    <span class="font-bold">{{ $details->owner1_mobile }}</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            @if($details->owner2_name)
                            <div>
                                <div class="font-semibold">Owner 2: {{ $details->owner2_name }}</div>
                                @if($details->owner2_mobile)
                                <div class="text-gray-600 flex items-center gap-1">
                                    <i class="fa-solid fa-phone text-blue-500"></i>
                                    <span class="font-bold">{{ $details->owner2_mobile }}</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            @if($details->owner3_name)
                            <div>
                                <div class="font-semibold">Owner 3: {{ $details->owner3_name }}</div>
                                @if($details->owner3_mobile)
                                <div class="text-gray-600 flex items-center gap-1">
                                    <i class="fa-solid fa-phone text-blue-500"></i>
                                    <span class="font-bold">{{ $details->owner3_mobile }}</span>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>

                        <!-- Documents -->
                        <div class="mt-4 flex flex-wrap gap-4">
                            <button class="flex items-center text-sm gap-1">
                                <span>Doc 1</span>
                                <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6">
                            </button>
                            <button class="flex items-center text-sm gap-1">
                                <span>Doc 2</span>
                                <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6">
                            </button>
                            <button class="flex items-center text-sm gap-1">
                                <span>Doc 3</span>
                                <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6">
                            </button>
                            <button class="flex items-center text-sm gap-1">
                                <span>Doc 4</span>
                                <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6">
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Info -->
                <div class="flex items-center justify-start">
                    <div class="w-full px-10 py-3">
                        <div class="relative flex items-center justify-between w-full">
                            <div class="absolute left-0 top-2/4 h-0.5 w-full -translate-y-2/4 bg-gray-300"></div>
                            <div class="absolute left-0 top-2/4 h-0.5 w-full -translate-y-2/4 bg-gray-900 transition-all duration-500"></div>
                            <div class="relative z-10 grid w-6 h-6 font-bold text-gray-900 transition-all duration-300 bg-gray-300 rounded-full place-items-center">
                                <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                <div class="absolute -top-[3rem] w-max text-center">
                                    <h6 class="block font-sans text-base antialiased font-semibold leading-relaxed tracking-normal text-gray-700">
                                    Applied
                                    </h6>
                                </div>
                            </div>
                            <div class="relative z-10 grid w-6 h-6 font-bold text-gray-900 transition-all duration-300 bg-gray-300 rounded-full place-items-center">
                                <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                <div class="absolute -top-[3rem] w-max text-center">
                                    <h6 class="block font-sans text-base antialiased font-semibold leading-relaxed tracking-normal text-gray-700">
                                    Verification
                                    </h6>
                                </div>
                            </div>
                            
                            <div class="relative z-10 grid w-6 h-6 font-bold text-white transition-all duration-300 bg-gray-900 rounded-full place-items-center">
                                <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                <div class="absolute -top-[3rem] w-max text-center">
                                    <h6 class="block font-sans text-base antialiased font-semibold leading-relaxed tracking-normal text-gray-700">
                                    Waiting</h6>
                                </div>
                            </div>
                            <div class="relative z-10 grid w-6 h-6 font-bold text-gray-900 transition-all duration-300 bg-gray-300 rounded-full place-items-center">
                                <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                <div class="absolute -top-[3rem] w-max text-center">
                                    <h6 class="block font-sans text-base antialiased font-semibold leading-relaxed tracking-normal text-gray-700">
                                    Delivered</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    </div>
</div>