<div class="w-full">
    <div class="relative mb-2 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2">
        <div>
            <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('Welcome') }} {{ Auth::user()->name }} !</flux:subheading>
        </div>
        <div>
            <div class="flex justify-between items-center mt-2">
                <flux:input type="text" placeholder="Search Society..." size="md" wire:model.live="search">
                    <x-slot name="iconTrailing">
                        @if ($search)
                        <flux:button size="sm" variant="subtle" icon="x-mark" class="-mr-1" wire:click="$set('search', '')" />
                        @endif
                    </x-slot>
                </flux:input> 
            </div>
        </div>
        </div>
    </div>
    <flux:separator variant="subtle" />

    <div class="max-h-[500px] overflow-y-auto pr-2">
        <div class="mb-2">
            <livewire:menus.alerts />
        </div>
        @foreach($societyDetail as $details)
            @php
            $statusData = json_decode($details->status, true);
            $tasks = collect($statusData['tasks']);
            $verification = $tasks->firstWhere('name', 'Verification');
            $needsAttention = !$verification || $verification['Status'] !== 'Approved';
            @endphp
            <div class="mt-4 {{ $needsAttention ? 'cursor-pointer' : '' }}" @if($needsAttention) wire:click="verifyDetails({{ $details->id }})" @endif>
                @if($needsAttention && $details->comment)
                <div class="px-4 py-2 my-2 rounded-lg bg-amber-100 border-amber-400 border-2">
                    <p class="text-md font-bold dark:text-black">Your Application for {{ $details->details_name }} {{ $details->apartment_number }} at {{ $details->society->society_name }} need attention.</p>
                    <p class="text-md font-bold dark:text-black">Please Correct Following-</p>
                    <p class="text-sm dark:text-black"> {{ $details->comment }}</p>
                </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 border-b border-gray-300">
                    <!-- Left Column -->
                    <div class="p-4 rounded">
                        <!-- Apartment + Owners -->
                        <h3 class="font-bold text-xl">{{ $details->society->society_name }}</h3>
                        <h3 class="font-bold text-amber-600 text-lg">{{ $details->building_name }} - {{ $details->apartment_number }}</h3>

                        <!-- Owner List -->
                        <div class="mt-2 space-y-2 text-sm">
                            @if($details->owner1_name)
                            <div>
                                <div class="font-semibold">Owner 1: {{ $details->owner1_name }}</div>
                                @if($details->owner1_mobile)
                                <div class="text-gray-600 flex items-center gap-1">
                                    <i class="fa-solid fa-phone text-amber-500"></i>
                                    <span class="font-bold dark:text-zinc-500">{{ $details->owner1_mobile }}</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            @if($details->owner2_name)
                            <div>
                                <div class="font-semibold">Owner 2: {{ $details->owner2_name }}</div>
                                @if($details->owner2_mobile)
                                <div class="text-gray-600 flex items-center gap-1">
                                    <i class="fa-solid fa-phone text-amber-500"></i>
                                    <span class="font-bold dark:text-zinc-500">{{ $details->owner2_mobile }}</span>
                                </div>
                                @endif
                            </div>
                            @endif

                            @if($details->owner3_name)
                            <div>
                                <div class="font-semibold">Owner 3: {{ $details->owner3_name }}</div>
                                @if($details->owner3_mobile)
                                <div class="text-gray-600 flex items-center gap-1">
                                    <i class="fa-solid fa-phone text-amber-500"></i>
                                    <span class="font-bold dark:text-zinc-500">{{ $details->owner3_mobile }}</span>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>

                        <!-- Documents -->
                        <div class="mt-4 flex overflow-x-auto gap-2 whitespace-nowrap">
                            @if($details->agreementCopy)
                            <div class="z-1 max-sm:hidden inline-block rounded-4xl bg-black px-4 py-2 text-sm/6 font-semibold text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                                    <a href="{{ asset('storage/society_docs/' . $details->agreementCopy) }}" target="_blank">
                                    <span class="font-bold text-sm">Copy of Agreement</span>
                                    </a>
                            </div> 
                            @endif
                            @if($details->memberShipForm)
                            <div class="z-1 max-sm:hidden inline-block rounded-4xl bg-black px-4 py-2 text-sm/6 font-semibold text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                                <a href="{{ asset('storage/society_docs/' . $details->memberShipForm) }}" target="_blank">
                                    <span class="font-bold text-sm">Membership Form</span>
                                </a>
                            </div>       
                            @endif
                            @if($details->allotmentLetter)
                            <div class="z-1 max-sm:hidden inline-block rounded-4xl bg-black px-4 py-2 text-sm/6 font-semibold text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                                <a href="{{ asset('storage/society_docs/' . $details->allotmentLetter) }}" target="_blank">
                                    <span class="font-bold text-sm">Allotment Letter</span>
                                </a>
                            </div>        
                            @endif
                            @if($details->possessionLetter)
                            <div class="z-1 max-sm:hidden inline-block rounded-4xl bg-black px-4 py-2 text-sm/6 font-semibold text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                                <a href="{{ asset('storage/society_docs/' . $details->possessionLetter) }}" target="_blank">
                                    <span class="font-bold text-sm">Possession Letter</span>
                                </a>
                            </div>
                            @endif 
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="p-4 rounded">
                        @php
                            $statusData = json_decode($details->status, true);
                            @endphp
                            @if(isset($statusData['tasks']) && count($statusData['tasks']) > 0)
                            <div data-dui-stepper-container data-dui-initial-step="1" class="w-full">
                                <div class="pb-10 sm:pb-6">
                                    <div class="flex w-full max-w-3xl items-center justify-between">
                                        @foreach(collect($statusData['tasks'])->take(3) as $task)
                                        <div aria-disabled="false" data-dui-step class="group w-full flex items-center">
                                            <div class="relative">
                                                <span class="relative grid h-10 w-10 place-items-center rounded-full {{ $task['Status'] == 'Pending' ? 'bg-stone-400 dark:text-white' : ' bg-amber-400 dark:text-white' }}">
                                                <i class="fa-solid fa-check text-white"></i>
                                                </span>
                                                <span class="absolute -bottom-6 start-0 whitespace-nowrap text-[10px] sm:text-xs {{ $task['Status'] != 'Pending' ? 'text-stone-800 font-extrabold dark:text-white' : 'text-stone-500 font-normal dark:text-white' }}">{{ $task['name'] }}</span>
                                            </div>
                                            @if(!$loop->last)
                                            <div class="flex-1 h-1 {{ $task['Status'] == 'Pending' ? 'bg-stone-400 dark:text-white' : ' bg-amber-400 dark:text-white' }}"></div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>


