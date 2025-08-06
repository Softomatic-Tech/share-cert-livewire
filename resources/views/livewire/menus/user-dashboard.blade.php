<div class="w-full">
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold">Verify Society Details:</h1>
        <div class="w-100">
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

    <div class="max-h-[500px] overflow-y-auto pr-2">
        <div class="mb-2">
            <livewire:menus.alerts />
        </div>
        <div class="p-6 bg-gray-50">
            <div class="grid grid-cols-2 gap-4 font-semibold text-gray-700 border-b pb-2 mb-2">
                <div class="text-center font-extrabold">APARTMENT</div>
                <div class="text-center font-extrabold">STATUS</div>
            </div>

            <!-- Apartment Card -->
            @foreach($societyDetail as $details)
                    @php
                    $statusData = json_decode($details->status, true);
                    $tasks = collect($statusData['tasks']);
                    $verification = $tasks->firstWhere('name', 'Verification');
                    @endphp
                @if ($verification && $verification['Status'] === 'Approved')
                <div class="grid grid-cols-2 gap-4 py-4 border-b border-gray-300">
                @else
                    @if($details->comment)
                    <div class="px-4 py-2 my-2 rounded-lg bg-amber-100 border-amber-400 border-2">
                        <p class="text-md font-bold">Your Application for {{ $details->details_name }} {{ $details->apartment_number }} at {{ $details->society->society_name }} need attention.</p>
                        <p class="text-md font-bold">Please Correct Following-</p>
                        <p class="text-sm"> {{ $details->comment }}</p>
                    </div>
                    @endif
                <div class="grid grid-cols-2 gap-4 py-4 border-b border-gray-300 cursor-pointer" wire:click="verifyDetails({{ $details->id }})">
                @endif
                    <div class="flex gap-4 items-start">
                        <!-- Apartment + Owners -->
                        <div class="flex-1">
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
                                        <i class="fa-solid fa-phone text-amber-500"></i>
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
                                        <i class="fa-solid fa-phone text-amber-500"></i>
                                        <span class="font-bold">{{ $details->owner3_mobile }}</span>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>

                            <!-- Documents -->
                            <div class="mt-4 flex overflow-x-auto gap-2 whitespace-nowrap">
                                @if($details->agreementCopy)
                                <div class="border-2 border-gray-300 px-2 py-2 rounded-md">
                                        <a href="{{ asset('storage/society_docs/' . $details->agreementCopy) }}" target="_blank">
                                        <span class="font-bold text-sm">Copy of Agreement</span>
                                        </a>
                                </div> 
                                @endif
                                @if($details->memberShipForm)
                                <div class="border-2 border-gray-300 px-2 py-2 rounded-md">
                                    <a href="{{ asset('storage/society_docs/' . $details->memberShipForm) }}" target="_blank">
                                        <span class="font-bold text-sm">Membership Form</span>
                                    </a>
                                </div>       
                                @endif
                                @if($details->allotmentLetter)
                                <div class="border-2 border-gray-300 px-2 py-2 rounded-md">
                                    <a href="{{ asset('storage/society_docs/' . $details->allotmentLetter) }}" target="_blank">
                                        <span class="font-bold text-sm">Allotment Letter</span>
                                    </a>
                                </div>        
                                @endif
                                @if($details->possessionLetter)
                                <div class="border-2 border-gray-300 px-2 py-2 rounded-md">
                                    <a href="{{ asset('storage/society_docs/' . $details->possessionLetter) }}" target="_blank">
                                        <span class="font-bold text-sm">Possession Letter</span>
                                    </a>
                                </div>
                                @endif 
                            </div>
                        </div>
                    </div>
                    <!-- Status Info -->
                    @php
                    $statusData = json_decode($details->status, true);
                    @endphp
                    @if(isset($statusData['tasks']))
                    <div data-dui-stepper-container data-dui-initial-step="1" class="w-full max-w-lg justify-end">
                        <div class="flex w-full items-center justify-between">
                            @foreach(collect($statusData['tasks'])->take(3) as $task)
                            <div aria-disabled="false" data-dui-step class="group w-full flex items-center">
                                <div class="relative">
                                    <span class="relative grid h-10 w-10 place-items-center rounded-full {{ $task['Status'] == 'Pending' ? 'bg-stone-400' : ' bg-amber-400' }}">
                                    <i class="fa-solid fa-check text-white"></i>
                                    </span>
                                    <span class="absolute -bottom-6 start-0 whitespace-nowrap text-sm {{ $task['Status'] != 'Pending' ? 'text-stone-800 font-extrabold' : 'text-stone-500 font-normal' }}">{{ $task['name'] }}</span>
                                </div>
                                @if(!$loop->last)
                                <div class="flex-1 h-1 {{ $task['Status'] == 'Pending' ? 'bg-stone-400' : ' bg-amber-400' }}"></div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>


