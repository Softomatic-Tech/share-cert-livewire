<div>
@if($societyDetails->isEmpty())
    <div class="rounded-lg border shadow-sm p-6 text-center">
        <p class="text-gray-500 dark:text-white">No apartments found for the selected society.</p>
    </div>
@else
@foreach($societyDetails as $details)
    <div class="rounded-lg border shadow-sm hover:shadow-lg transition-shadow my-4">
        <div class="grid grid-cols-1 md:grid-cols-2 border-b relative">
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
                $statusData = json_decode($details->status, true);
                $steps = collect($statusData['tasks'])->skip(1)->values();
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
                                } elseif ($i === 3) {
                                    // Step 4
                                    if ($steps[0]['Status'] === 'Approved' && $steps[1]['Status'] === 'Approved' && $steps[2]['Status'] === 'Approved') {
                                        $colors[$i] = $status === 'Pending' ? 'bg-amber-400' : 'bg-green-500';
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
                                        @php
                                            $label = $taskNameMap[$task['name']] ?? $task['name'];
                                        @endphp
                                        <span class="absolute -bottom-8 start-0 whitespace-wrap text-[10px] {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'text-stone-500 font-normal' : 'text-stone-800 font-extrabold'}} dark:text-white">{{ $label }}
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
            <flux:tooltip content="Edit Apartment">
                <button class="font-bold absolute top-0 right-0 px-2 py-1 border rounded-md" wire:click="fetchOwnersDetail('{{ $details->id }}')"><i class="fa-solid fa-edit text-sm"></i></button>
            </flux:tooltip>
        </div>
        <!--Documents Section (below the grid) -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-2 p-2">
            @php
                $isApproved=false;
            @endphp
            @if($details->agreementCopy)
                @php $fileUrl = asset('storage/society_docs/' . $details->agreementCopy);
                $isApproved =$this->getFileStatus($statusData, $details->agreementCopy);
                @endphp
            <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                @if($isApproved=='Approved')
                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                @else
                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                @endif
                Copy of Agreement
            </div>
            @else
            <div class="flex items-center text-xs">
                Copy of Agreement
            </div>
            @endif
            @if($details->memberShipForm)
            @php $fileUrl = asset('storage/society_docs/' . $details->memberShipForm);
            $isApproved =$this->getFileStatus($statusData, $details->memberShipForm);
            @endphp
            <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                @if($isApproved=='Approved')
                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                @else
                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                @endif
                Membership Form
            </div>
            @else
            <div class="flex items-center text-xs">
                Membership Form
            </div>
            @endif
            @if($details->allotmentLetter)
            @php $fileUrl = asset('storage/society_docs/' . $details->allotmentLetter);
            $isApproved =$this->getFileStatus($statusData, $details->allotmentLetter);
            @endphp
            <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                @if($isApproved=='Approved')
                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                @else
                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                @endif
                Allotment Letter
            </div>
            @else
            <div class="flex items-center text-xs">
                Allotment Letter
            </div>
            @endif
            @if($details->possessionLetter)
            @php $fileUrl = asset('storage/society_docs/' . $details->possessionLetter); 
            $isApproved =$this->getFileStatus($statusData, $details->possessionLetter);
            @endphp
            <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                @if($isApproved=='Approved')
                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                @else
                <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                @endif
                Possession Letter
            </div>
            @else
            <div class="flex items-center text-xs">
                Possession Letter
            </div>
            @endif
            @php
            $allApproved = $this->areAllFourFilesApproved($statusData, [$details->agreementCopy,$details->memberShipForm,$details->allotmentLetter,$details->possessionLetter]);
            @endphp
            @if ($allApproved) 
            <div class="flex items-center text-xs justify-center cursor-pointer" wire:click="setDocument('{{ $details->id }}')">Verify Documents</div>
            @endif
        </div>
    </div>
@endforeach
@endif
<!--Modal-->
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
            <div class="flex justify-between">
                @if($checkApproved=='Approved')
                <flux:modal.close>
                    <flux:button x-on:click="$wire.updateFileStatus('{{ $detailId }}','{{ $fileName }}','Rejected')" variant="danger">Reject</flux:button>
                    </flux:modal.close>
                @endif
                @if($checkApproved=='Rejected')
                <flux:modal.close>
                    <flux:button variant="primary" x-on:click="$wire.updateFileStatus('{{ $detailId }}','{{ $fileName }}','Approved')">Approve</flux:button>
                    </flux:modal.close>
                @endif
                @if($checkApproved=='')
                    <flux:modal.close>
                        <flux:button variant="primary" x-on:click="$wire.updateFileStatus('{{ $detailId }}','{{ $fileName }}','Approved')">Approve</flux:button>
                        <flux:button x-on:click="$wire.updateFileStatus('{{ $detailId }}','{{ $fileName }}','Rejected')" variant="danger">Reject</flux:button>
                    </flux:modal.close>
                @endif
            </div>
    </div>
</flux:modal>

<flux:modal wire:model="verificationModal" class="!max-w-2xl w-full">
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
                <flux:button variant="primary" x-on:click="$wire.approveDetail('{{ $detailId }}')">Approve</flux:button>
            </flux:modal.close>
            <flux:button wire:click="setRejecting" variant="filled">Reject</flux:button>
        </div>

        @if($isRejecting)
            <div class="flex justify-end mt-2">
                <flux:modal.close>
                    <flux:button x-on:click="$wire.rejectDetail('{{ $detailId }}')" variant="danger">Confirm Rejection</flux:button>
                </flux:modal.close>
            </div>
        @endif
    </div>
</flux:modal>

<flux:modal wire:model="editOwnersModal" class="!max-w-3xl w-full">
    <div class="space-y-6">
        <div class="text-lg font-bold">
            <flux:heading size="lg">Edit Owner Details</flux:heading>
        </div>
        <form wire:submit.prevent="updateOwnersDetail">
            <div><flux:input type="hidden"  wire:model="apartment_id" /></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                <flux:input type="text" :label="__('Building Name :')" wire:model="building_name" />
                <flux:input type="text" :label="__('Apartment Number :')" wire:model="apartment_number" />
                <flux:input type="text" :label="__('Owner 1 Name :')" wire:model="owner1_name" />
                <flux:input type="text" :label="__('Owner 1 Email :')" wire:model="owner1_email" />
                <flux:input type="text" :label="__('Owner 1 Mobile :')" wire:model="owner1_mobile" />
                <flux:input type="text" :label="__('Owner 2 Name :')" wire:model="owner2_name" />
                <flux:input type="text" :label="__('Owner 2 Email :')" wire:model="owner2_email" />
                <flux:input type="text" :label="__('Owner 2 Mobile :')" wire:model="owner2_mobile" />
                <flux:input type="text" :label="__('Owner 3 Name :')" wire:model="owner3_name" />
                <flux:input type="text" :label="__('Owner 3 Email :')" wire:model="owner3_email" />
                <flux:input type="text" :label="__('Owner 3 Mobile :')" wire:model="owner3_mobile" />
            </div>
            <div class="flex justify-end mt-4">
                <flux:button variant="primary" type="submit">{{ __('Update') }}</flux:button>
            </div>
        </form>
    </div>
</flux:modal>
</div>