<div>
    <div class="w-full mb-1">
        <div class="grid grid-cols-1 md:grid-cols-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="#">Admin</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="#">Society Details</flux:breadcrumbs.item>
        </flux:breadcrumbs>
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
        <div class="p-6">
            @foreach($societyDetail as $details)
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
                            @php $fileUrl = asset('storage/society_docs/' . $details->agreementCopy); @endphp
                            <button class="inline-flex items-center justify-center rounded-full bg-stone-400 text-white px-2 py-2 text-xs font-medium dark:bg-gray-700 dark:hover:bg-gray-600" wire:click="viewDocument('{{ $fileUrl }}')">Copy of Agreement</button>
                        @endif
                        @if($details->memberShipForm)
                            @php $fileUrl = asset('storage/society_docs/' . $details->memberShipForm); @endphp
                            <button class="inline-flex items-center justify-center rounded-full bg-stone-400 text-white px-2 py-2 text-xs font-medium dark:bg-gray-700 dark:hover:bg-gray-600" wire:click="viewDocument('{{ $fileUrl }}')">Membership Form</button>
                        @endif
                        @if($details->allotmentLetter)
                            @php $fileUrl = asset('storage/society_docs/' . $details->allotmentLetter); @endphp
                            <button class="inline-flex items-center justify-center rounded-full bg-stone-400 text-white px-2 py-2 text-xs font-medium dark:bg-gray-700 dark:hover:bg-gray-600" wire:click="viewDocument('{{ $fileUrl }}')">Allotment Letter</button> 
                        @endif
                        @if($details->possessionLetter)
                            @php $fileUrl = asset('storage/society_docs/' . $details->possessionLetter); @endphp
                            <button class="inline-flex items-center justify-center rounded-full bg-stone-400 text-white px-2 py-2 text-xs font-medium dark:bg-gray-700 dark:hover:bg-gray-600" wire:click="viewDocument('{{ $fileUrl }}')">Possession Letter</button> 
                        @endif 
                        <div>
                            @php
                                $statusData = json_decode($details->status, true);
                                $tasks = collect($statusData['tasks']);
                                $verifyDetails = $tasks->firstWhere('name', 'Verify Details');
                                $application = $tasks->firstWhere('name', 'Application');
                                $verification = $tasks->firstWhere('name', 'Verification');
                            @endphp
                            @if (
                                $verifyDetails && $verifyDetails['Status'] === 'Applied' &&
                                $application && $application['Status'] === 'Applied' &&
                                $verification && $verification['Status'] === 'Pending'
                            )
                            @if($details->agreementCopy)
                                <flux:modal.trigger name="verificationModal">
                                    <flux:button variant="primary" x-on:click="$wire.setDocument('{{ $details->id }}')">Verify</flux:button>
                                </flux:modal.trigger>
                            @endif
                            @endif
                        </div> 
                    </div>
                </div>

                <!-- Right Column -->
                <div class="p-4 rounded">
                    @php
                    $statusData = json_decode($details->status, true);
                    @endphp
                    @if(isset($statusData['tasks']))
                        @php
                            $step1 = $statusData['tasks'][1];
                            $step2 = $statusData['tasks'][2]; 
                            $note = '';

                            if ($step1['Status'] === 'Pending') {
                                if ($step2 && $step2['Status'] === 'Pending') {
                                    $note = 'Application is pending and needs to be verified.';
                                }elseif ($step2 && $step2['Status'] === 'Rejected') {
                                    $note = 'Application is pending and needs to be reviewed.';
                                }
                            } elseif ($step1['Status'] === 'Applied') {
                                if ($step2 && $step2['Status'] === 'Pending') {
                                    $note = 'Admin Verification needs to be updated.';
                                } elseif ($step2 && $step2['Status'] === 'Approved') {
                                    $note = 'All steps are done.';
                                }
                            }
                        @endphp
                        
                        <div class="w-full flex flex-col gap-4">
                            <div>
                                <h2 class="text-sm font-bold mb-2">{{ $note }}</h2>
                            </div>

                            <div data-dui-stepper-container data-dui-initial-step="1" class="w-full mb-6">
                                <div class="flex items-center justify-between">
                                    @foreach(collect($statusData['tasks'])->skip(1) as $task)
                                    <div aria-disabled="false" data-dui-step class="group w-full flex items-center">
                                        <div class="relative">
                                            <span class="relative grid h-10 w-10 place-items-center rounded-full {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'bg-stone-400' : 'bg-amber-400'}}">
                                            <i class="fa-solid fa-check text-white"></i>
                                            </span>
                                            @php
                                                $label = match($task['name']) {
                                                    'Application' => 'Application',
                                                    'Verification' => 'Verification',
                                                    'Certificate Generated' => 'Waiting',
                                                    'Certificate Delivered' => 'Delivered',
                                                    default => $task['name']
                                                };
                                            @endphp
                                            <span class="absolute -bottom-6 start-0 whitespace-nowrap text-[10px] sm:text-xs {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'text-stone-500 font-normal' : 'text-stone-800 font-extrabold'}}">{{ $label }}
                                            </span>
                                        </div>
                                        @if(!$loop->last)
                                        <div class="flex-1 h-1 {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'bg-stone-400' : 'bg-amber-400'}}"></div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    
        <!--Modal-->
        <flux:modal  wire:model="showDocumentModal" class="!max-w-7xl w-full">
            <div class="space-y-6">
                <div class="text-lg font-bold">
                    <flux:heading size="lg">Document View</flux:heading>
                </div>

                @if(!$fileUrl)
                {{-- Loader --}}
                <div class="flex justify-center items-center h-[70vh]">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-stone-400 border-t-transparent"></div>
                    <span class="ml-3 text-stone-600 dark:text-stone-300">Loading document...</span>
                </div>
                @else
                    @php
                        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                    @endphp

                    {{-- Images --}}
                    @if(in_array($extension, ['jpg','jpeg','png','gif','webp']))
                        <img src="{{ $url }}" alt="preview" class="w-full rounded" />

                    {{-- PDF --}}
                    @elseif($extension === 'pdf')
                        <iframe src="{{ $url }}#toolbar=0" class="w-full h-[70vh]" frameborder="0"></iframe>

                    {{-- Office documents & others --}}
                    @elseif(in_array($extension, ['doc','docx']))
                        <div class="flex flex-col items-center">
                            <img src="{{ asset('images/icons/word.svg') }}" alt="Word file" class="w-24 h-24 mb-2">
                            <a href="{{ $url }}" class="underline">Click To Download File</a>
                        </div>

                    @elseif(in_array($extension, ['xls','xlsx']))
                        <div class="flex flex-col items-center">
                            <img src="{{ asset('images/icons/excel.svg') }}" alt="Excel file" class="w-24 h-24 mb-2">
                            <a href="{{ $url }}" class="underline">Click To Download File</a>
                        </div>

                    {{-- Default for zip/others --}}
                    @else
                        <div class="flex flex-col items-center">
                            <img src="/images/icons/file.png" alt="File" class="w-24 h-24 mb-2">
                            <a href="{{ $url }}" target="_blank" class="underline">Download File</a>
                        </div>
                    @endif
                @endif
            </div>
        </flux:modal>

        <flux:modal name="verificationModal" class="md:w-96">
            <div class="space-y-6">
                <div class="text-lg font-bold">
                    <flux:heading size="lg">Document Approval</flux:heading>
                </div>

                {{-- Comment Field (only when rejecting) --}}
                @if($isRejecting)
                    <flux:textarea type="text" wire:model="comment" placeholder="Enter reason for rejection..." value="{{ $comment }}"/>
                    @error('comment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                @else
                    <p class="text-sm font-normal">{{ $text }}</p>
                @endif

                <div class="flex justify-between">
                    <flux:modal.close>
                        <flux:button variant="primary" x-on:click="$wire.approveDocument('{{ $detailId }}')">Approve</flux:button>
                    </flux:modal.close>
                    <flux:button wire:click="setRejecting" variant="filled">Reject</flux:button>
                </div>

                @if($isRejecting)
                    <div class="flex justify-end mt-2">
                        <flux:modal.close>
                            <flux:button x-on:click="$wire.rejectDocument('{{ $detailId }}')" variant="danger">Confirm Rejection</flux:button>
                        </flux:modal.close>
                    </div>
                @endif
            </div>
        </flux:modal>
    </div>
</div>