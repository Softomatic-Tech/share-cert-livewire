<div>
    <div class="mb-1 w-full">
            <div class="flex justify-between items-center">
                <div>
                    <flux:breadcrumbs>
                        <flux:breadcrumbs.item href="#">{{ __('Welcome') }} {{ Auth::user()->name }} !</flux:breadcrumbs.item>
                    </flux:breadcrumbs>
                </div>
                <div>
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
    <flux:separator variant="subtle" />

    <div class="max-h-[500px] overflow-y-auto pr-2">
        <div class="mb-2">
            <livewire:menus.alerts />
        </div>
        <div class="p-6">
            @foreach($societyDetail as $details)
            @php
            $statusData = json_decode($details->status, true);
            $tasks = collect($statusData['tasks']);
            $verification = $tasks->firstWhere('name', 'Verification');
            $needsAttention = !$verification || $verification['Status'] !== 'Approved';
            @endphp
            <div>
                @if($needsAttention && $details->comment)
                <div class="px-4 py-2 my-2 rounded-lg border-amber-400 border-2">
                    <p class="text-md font-bold dark:text-white">Your Application for {{ $details->details_name }} {{ $details->apartment_number }} at {{ $details->society->society_name }} need attention.</p>
                    <p class="text-md font-bold dark:text-white">Please Correct Following-</p>
                    <p class="text-sm dark:text-white"> {{ $details->comment }}</p>
                </div>
                @endif
                <header class="mb-2">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white"> {{ $details->society->society_name }}
                        Flats</h1>
                </header>

                <div class="flex flex-col gap-4">
                    <div class="rounded-lg border border-gray-200 shadow-sm hover:shadow-lg transition-shadow my-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 border-b border-gray-300 relative">
                            <div class="p-4">
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $details->building_name }} - {{ $details->apartment_number }}</p>
                                @if($details->owner1_mobile)<p class="text-sm text-gray-500 dark:text-white">Owner1 Phone: {{ $details->owner1_mobile }}</p>@endif
                                @if($details->owner1_email)<p class="text-sm text-gray-500 dark:text-white">Email: {{ $details->owner1_email }}</p>@endif
                                <p class="mb-1"></p>
                                @if($details->owner2_mobile)<p class="text-sm text-gray-500 dark:text-white">Owner2 Phone: {{ $details->owner2_mobile }}</p>@endif
                                @if($details->owner2_email)<p class="text-sm text-gray-500 dark:text-white">Email: {{ $details->owner2_email }}</p>@endif
                                <p class="mb-1"></p>
                                @if($details->owner3_mobile)<p class="text-sm text-gray-500 dark:text-white">Owner3 Phone: {{ $details->owner3_mobile }}</p>@endif
                                @if($details->owner3_email)<p class="text-sm text-gray-500 dark:text-white">Email: {{ $details->owner3_email }}</p>@endif
                            </div>
                            <div class="p-4">
                                @php
                                $steps = collect($statusData['tasks'])->take(3)->values();
                                // Global checks
                                $allPending = $steps->every(fn($s) => ($s['Status'] ?? '') === 'Pending');
                                $allApproved = $steps->every(fn($s) => ($s['Status'] ?? '') === 'Approved');
                                $colors = [];
                                $icons=[];
                                @endphp
                                @if(isset($steps) && $steps->isNotEmpty())
                                    @php
                                        if ($allPending) {
                                            // All Pending → gray
                                            $colors = array_fill(0, $steps->count(), 'bg-stone-400');
                                            $icons = array_fill(0, $steps->count(), 'fa-circle');
                                        } elseif ($allApproved) {
                                            // All Approved → green
                                            $colors = array_fill(0, $steps->count(), 'bg-green-500');
                                            $icons = array_fill(0, $steps->count(), 'fa-check');
                                        } else {
                                            // Sequential logic
                                            foreach ($steps as $i => $task) {
                                                $status = $task['Status'] ?? 'Pending';
                                                //If rejected, always red + xmark
                                                if ($status === 'Rejected') {
                                                    $colors[$i] = 'bg-red-500';
                                                    $icons[$i] = 'fa-xmark';
                                                    continue; // skip further checks for this step
                                                }
                                                if ($i === 0) {
                                                    // Step 1
                                                    $colors[$i] = $status === 'Pending' ? 'bg-amber-400' : 'bg-green-500';
                                                    $icons[$i] = $status === 'Pending' ? 'fa-circle' : 'fa-check';
                                                } elseif ($i === 1) {
                                                    // Step 2
                                                    if ($steps[0]['Status'] === 'Approved') {
                                                        $colors[$i] = $status === 'Pending' ? 'bg-amber-400' : 'bg-green-500';
                                                        $icons[$i] = $status === 'Pending' ? 'fa-circle' : 'fa-check';
                                                    } else {
                                                        $colors[$i] = 'bg-stone-400';
                                                        $icons[$i] = 'fa-circle';
                                                    }
                                                } elseif ($i === 2) {
                                                    // Step 3
                                                    if ($steps[0]['Status'] === 'Approved' && $steps[1]['Status'] === 'Approved') {
                                                        $colors[$i] = $status === 'Pending' ? 'bg-blue-500' : 'bg-green-500';
                                                        $icons[$i] = $status === 'Pending' ? 'fa-circle' : 'fa-check';
                                                    } else {
                                                        $colors[$i] = 'bg-stone-400';
                                                        $icons[$i] = 'fa-circle';
                                                    }
                                                }else{
                                                    $colors[$i] = 'bg-stone-400';
                                                    $icons[$i] = 'fa-circle';
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="w-full">
                                        <div data-dui-stepper-container data-dui-initial-step="1" class="w-full mb-4">
                                            <div class="flex items-center justify-between">
                                                @foreach($steps as $i => $task)
                                                <div aria-disabled="false" data-dui-step class="group w-full flex items-center">
                                                    <div class="relative">
                                                        <span class="relative grid h-8 w-8 place-items-center rounded-full {{ $colors[$i] }}">
                                                        <i class="fa-solid {{ $icons[$i] }} text-white"></i>
                                                        </span>
                                                        <span class="absolute -bottom-6 start-0 whitespace-nowrap text-[10px] sm:text-xs {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'text-stone-500 font-normal' : 'text-stone-800 font-extrabold'}} dark:text-white">{{ $task['name'] }}
                                                        </span>
                                                    </div>
                                                    @if(!$loop->last)
                                                    <div class="flex-1 h-1 bg-gray-400 {{ $colors[$i] }}"></div>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($needsAttention)
                            <flux:tooltip content="Edit Apartment">
                                <button class="font-bold absolute top-2 right-4 px-2 py-1 border rounded-md cursor-pointer" wire:click="verifyDetails({{ $details->id }})"><i class="fa-solid fa-edit text-sm"></i></button>
                            </flux:tooltip>
                            @endif
                        </div>
                        <!--Documents Section (below the grid) -->
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-2 p-3">
                            @php
                                $isApproved=false;
                            @endphp
                            @if($details->agreementCopy)
                                @php $fileUrl = asset('storage/society_docs/' . $details->agreementCopy);
                                $isApproved =$this->getFileStatus($statusData, $details->agreementCopy);
                                @endphp
                            <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                Copy of Agreement
                            </div>
                            @else
                            <div class="flex items-center text-xs">
                                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                Copy of Agreement
                            </div>
                            @endif
                            @if($details->memberShipForm)
                            @php $fileUrl = asset('storage/society_docs/' . $details->memberShipForm);
                            $isApproved =$this->getFileStatus($statusData, $details->memberShipForm);
                            @endphp
                            <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                Membership Form
                            </div>
                            @else
                            <div class="flex items-center text-xs">
                                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                Membership Form
                            </div>
                            @endif
                            @if($details->allotmentLetter)
                            @php $fileUrl = asset('storage/society_docs/' . $details->allotmentLetter);
                            $isApproved =$this->getFileStatus($statusData, $details->allotmentLetter);
                            @endphp
                            <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                Allotment Letter
                            </div>
                            @else
                            <div class="flex items-center text-xs">
                                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                Allotment Letter
                            </div>
                            @endif
                            @if($details->possessionLetter)
                            @php $fileUrl = asset('storage/society_docs/' . $details->possessionLetter); 
                            $isApproved =$this->getFileStatus($statusData, $details->possessionLetter);
                            @endphp
                            <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                Possession Letter
                            </div>
                            @else
                            <div class="flex items-center text-xs">
                                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                Possession Letter
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

<flux:modal  wire:model="showDocumentModal" class="!max-w-3xl w-full">
    <div class="space-y-6">
        <div class="text-lg font-bold">
            <flux:heading size="lg">Document View</flux:heading>
        </div>
        @php
            $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
            $fileName = trim(basename($url));
        @endphp

        {{-- Images --}}
        @if(in_array($extension, ['jpg','jpeg','png']))
            <img src="{{ $url }}" alt="preview" class="w-full rounded" />

        {{-- PDF --}}
        @elseif($extension === 'pdf')
            <iframe src="{{ $url }}#toolbar=0" class="w-full h-[70vh]" frameborder="0"></iframe>
        @endif
    </div>
</flux:modal>
</div>