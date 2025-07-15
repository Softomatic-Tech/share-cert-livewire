<div class="relative">
    <div class="mx-auto w-fit max-w-xl px-4">
        <div class="text-center text-2xl font-semibold text-zinc-800 dark:text-white">Select society to view Apartment Details</div>

        {{-- <div class="text-base text-lg mt-4 text-center text-zinc-500 dark:text-zinc-300">Please <a href="{{ route('menus.register_society') }}" class="underline text-cyan-600">Click Here</a> to register!</div> --}}
    </div>
    <div class="w-full mt-3">
        <div class="grid gap-6 md:grid-cols-3">
            <div>
                <label for="society_id">Society Name</label>
                <flux:select id="society_id" wire:model.change="selectedSociety" placeholder="Choose Society...">
                    <flux:select.option value="">Choose Society...</flux:select.option>
                    @foreach($societies  as $society)
                        <flux:select.option value="{{ $society->id }}">{{ $society->society_name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <label for="building_id">Building Name</label>
                <flux:select wire:model.change="selectedBuilding" id="building_id" placeholder="Choose Building...">
                    <flux:select.option value="">Choose Building...</flux:select.option>
                    @foreach($buildings as $building)
                        <flux:select.option value="{{ $building->id }}">{{ $building->building_name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </div>
    
    @if(!empty($taskStatus['tasks']))
    <div class="mt-4 space-y-4">
        <h2 class="text-xl font-semibold">Task Status for Apartment: {{ $taskStatus['apartmentID'] }}</h2>

        @foreach ($taskStatus['tasks'] as $task)
            <div class="border p-4 rounded shadow-sm bg-white">
                <h3 class="text-lg font-bold">{{ $task['name'] }}</h3>
                <p>Status: <span class="text-blue-600 font-medium">{{ $task['Status'] }}</span></p>
                @isset($task['responsibilityOf'])
                    <p>Responsibility: {{ $task['responsibilityOf'] }}</p>
                @endisset
                <p>Updated By: {{ $task['updatedBy'] ?? 'N/A' }}</p>
                <p>Updated At: {{ $task['updateDateTime'] }}</p>

                @if (isset($task['subtask']))
                    <div class="mt-3 ml-4 border-l-2 pl-4">
                        <h4 class="font-semibold">Subtasks</h4>
                        @foreach ($task['subtask'] as $sub)
                            <div class="mt-2">
                                <p>• <strong>{{ $sub['name'] }}</strong> - {{ $sub['Status'] }}</p>
                                <p class="text-sm text-gray-500">Updated by {{ $sub['updatedBy'] }} at {{ $sub['updateDateTime'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif

</div>


