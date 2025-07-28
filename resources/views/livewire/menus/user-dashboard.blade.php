<div class="relative">
    <div class="mx-auto w-fit max-w-xl px-4">
        <div class="text-xl font-semibold text-zinc-800 dark:text-white">Select society to view Apartment Details</div>
    </div>
    <div class="w-full mt-3">
        <div class="grid gap-6 md:grid-cols-3 my-4">
            <div>
                <label for="society_id">Society:</label>
                <flux:select id="society_id" wire:model.change="selectedSociety" placeholder="Choose Society...">
                    <flux:select.option value="">Choose Society...</flux:select.option>
                    @foreach($societies  as $society)
                        <flux:select.option value="{{ $society->id }}">{{ $society->society_name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <label for="building_id">Building:</label>
                <flux:select wire:model.change="selectedBuilding" id="building_id" placeholder="Choose Building...">
                    <flux:select.option value="">Choose Building...</flux:select.option>
                    @foreach($buildings as $building)
                        <flux:select.option value="{{ $building->id }}">{{ $building->building_name }} ({{ $building->apartment_number }})</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </div>

    <div class="max-h-[500px] overflow-y-auto pr-2">
    @if($selectedSociety || $selectedBuilding)
    @foreach($buildings as $building)
    @if($building->comment)
    <div class="px-4 py-2 my-2 rounded-lg bg-amber-100 border-amber-400 border-2">
        <p class="text-md font-bold">Your Application for {{ $building->building_name }} {{ $building->apartment_number }} at {{ $building->society->society_name }} need attention.</p>
        <p class="text-md font-bold">Please Correct Following-</p>
        <p class="text-sm"> {{ $building->comment }}</p>
    </div>
    @endif
    <div class="w-full mt-4 p-4 rounded-lg shadow-lg border border-gray-200 bg-white dark:bg-gray-800 cursor-pointer" wire:click="verifyDetails({{ $building->id }})">
        <!-- Top Row: Society name (left) and Progress bar (right) -->
        <div class="flex justify-between items-center">
            <!-- Society Name -->
            <div class="text-lg font-bold text-gray-800 bg-yellow-100 px-3 py-1 rounded-md">
                {{ $building->building_name }} {{ $building->apartment_number }}
            </div>
            <!-- Progress bar -->
                @php
                $statusData = json_decode($building->status, true);
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

        <!-- Owners Heading -->
        <div class="p-4 text-gray-700 font-semibold">
            Owners :
        </div>

        <!-- Owners List Inline -->
        <div class="flex justify-around mt-2 text-center">
            @if($building->owner1_name)
            <div>
                <div class="font-medium text-gray-800">{{ $building->owner1_name }}</div>
                @if($building->owner1_mobile)<div class="text-sm text-gray-600">{{ $building->owner1_mobile }}</div>@endif
            </div>
            @endif
            @if($building->owner2_name)
            <div>
                <div class="font-medium text-gray-800">{{ $building->owner2_name }}</div>
                @if($building->owner2_mobile)<div class="text-sm text-gray-600">{{ $building->owner2_mobile }}</div>@endif
            </div>
            @endif
            @if($building->owner3_name)
            <div>
                <div class="font-medium text-gray-800">{{ $building->owner3_name }}</div>
                @if($building->owner3_mobile)<div class="text-sm text-gray-600">{{ $building->owner3_mobile }}</div>@endif
            </div>
            @endif
        </div>
    </div>
    @endforeach
    @endif
    </div>
</div>


