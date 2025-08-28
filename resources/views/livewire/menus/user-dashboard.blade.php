<div>
    <div class="mb-1 w-full">
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
        <div class="p-6 bg-gray-50">
            @foreach($societyDetail as $details)
            @php
            $statusData = json_decode($details->status, true);
            $tasks = collect($statusData['tasks']);
            $verification = $tasks->firstWhere('name', 'Verification');
            $needsAttention = !$verification || $verification['Status'] !== 'Approved';
            @endphp
            <div class=" {{ $needsAttention ? 'cursor-pointer' : '' }}" @if($needsAttention) wire:click="verifyDetails({{ $details->id }})" @endif>
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
                        <h3 class="font-bold text-xl">{{ $details->building_name }} - {{ $details->apartment_number }} <flux:badge color="amber" class="ml-2">{{ $details->society->society_name }}</flux:badge></h3>

                        <!-- Owner List -->
                        <div class="mt-1">
                            <table class="min-w-full table-fixed text-sm text-left dark:text-white">
                                <thead>
                                <tr>
                                    <th class="px-1 py-1 w-1/3">@if($details->owner1_name)Owner 1 @endif</th>
                                    <th class="px-1 py-1 w-1/3">@if($details->owner2_name)Owner 2 @endif</th>
                                    <th class="px-1 py-1 w-1/3">@if($details->owner3_name)Owner 3 @endif</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="px-1 py-1">
                                        @if($details->owner1_name)
                                        {{ $details->owner1_name }}<br />
                                        @if($details->owner1_mobile)<flux:badge size="sm">{{ $details->owner1_mobile }}</flux:badge>@endif
                                        @endif
                                    </td>
                                    <td class="px-1 py-1">
                                        @if($details->owner2_name)
                                        {{ $details->owner2_name }}<br />
                                        @if($details->owner2_mobile)<flux:badge size="sm">{{ $details->owner2_mobile }}</flux:badge>@endif
                                        @endif
                                    </td>
                                    <td class="px-1 py-1">
                                        @if($details->owner3_name)
                                        {{ $details->owner3_name }}<br />
                                        @if($details->owner3_mobile)<flux:badge size="sm">{{ $details->owner3_mobile }}</flux:badge>@endif
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Documents -->
                        <div class="mt-2 flex overflow-x-auto gap-2 whitespace-nowrap">
                            @if($details->agreementCopy)
                                <div class="z-1 max-sm:hidden inline-flex items-center justify-center rounded-full bg-black px-2 py-2 text-xs font-medium text-white dark:bg-gray-700 dark:hover:bg-gray-600">
                                    <a href="{{ asset('storage/society_docs/' . $details->agreementCopy) }}" target="_blank" class="flex items-center justify-center">
                                        <span class="font-semibold">Copy of Agreement</span>
                                    </a>
                                </div>
                            @endif
                            @if($details->memberShipForm)
                                <div class="z-1 max-sm:hidden inline-flex items-center justify-center rounded-full bg-black px-2 py-2 text-xs font-medium text-white dark:bg-gray-700 dark:hover:bg-gray-600">
                                    <a href="{{ asset('storage/society_docs/' . $details->memberShipForm) }}" target="_blank" class="flex items-center justify-center">
                                        <span class="font-semibold">Membership Form</span>
                                    </a>
                                </div>   
                            @endif
                            @if($details->allotmentLetter)
                                <div class="z-1 max-sm:hidden inline-flex items-center justify-center rounded-full bg-black px-2 py-2 text-xs font-medium text-white dark:bg-gray-700 dark:hover:bg-gray-600">
                                    <a href="{{ asset('storage/society_docs/' . $details->allotmentLetter) }}" target="_blank" class="flex items-center justify-center">
                                        <span class="font-semibold">Allotment Letter</span>
                                    </a>
                                </div>      
                            @endif
                            @if($details->possessionLetter)
                                <div class="z-1 max-sm:hidden inline-flex items-center justify-center rounded-full bg-black px-2 py-2 text-xs font-medium text-white dark:bg-gray-700 dark:hover:bg-gray-600">
                                    <a href="{{ asset('storage/society_docs/' . $details->possessionLetter) }}" target="_blank" class="flex items-center justify-center">
                                        <span class="font-semibold">Possession Letter</span>
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
                            <div class="w-full flex flex-col gap-4">
                                <div data-dui-stepper-container data-dui-initial-step="1" class="w-full mb-6">
                                    <div class="flex items-center justify-between">
                                        @foreach(collect($statusData['tasks'])->take(3) as $task)
                                        <div aria-disabled="false" data-dui-step class="group w-full flex items-center">
                                            <div class="relative">
                                                <span class="relative grid h-10 w-10 place-items-center rounded-full {{ $task['Status'] == 'Pending' ? 'bg-stone-400 dark:text-white' : 'bg-amber-400 dark:text-white' }}">
                                                <i class="fa-solid fa-check text-white"></i>
                                                </span>
                                                <span class="absolute -bottom-6 start-0 whitespace-nowrap text-[10px] sm:text-xs {{ $task['Status'] != 'Pending' ? 'text-stone-800 font-extrabold dark:text-white' : 'text-stone-500 font-normal dark:text-white' }}">{{ $task['name'] }}</span>
                                            </div>
                                            @if(!$loop->last)
                                            <div class="flex-1 h-0.5 {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'bg-stone-400' : 'bg-amber-400'}}"></div>
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
</div>


