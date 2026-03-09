<div>
    <div class="mb-1 w-full">
        <div>
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('user.dashboard') }}">{{ __('Welcome') }} {{ Auth::user()->name }} !</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
    </div>
    <flux:separator variant="subtle" />
    <div class="mb-2">
        <livewire:menus.alerts />
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 min-h-screen">
        <!-- Sidebar -->
        <aside class="col-span-1 border-r p-2">
            <!-- Search Box -->
            <div class="mb-2 flex-shrink-0">
                    <flux:input type="text" placeholder="Search Apartment..." wire:model.live="search" size="sm">
                        <x-slot name="iconTrailing">
                            @if ($search)
                            <flux:button size="sm" variant="subtle" icon="x-mark" class="-mr-1" wire:click="$set('search', '')" />
                            @endif
                            <flux:icon.magnifying-glass variant="outline" class="size-4 text-gray-400" />
                        </x-slot>
                    </flux:input>
                </div>

            <!-- Apartment List with Scrollbar -->
            <div class="flex-1 overflow-y-auto p-1 space-y-3 custom-scrollbar" style="max-height: calc(100vh - 250px);">
                @forelse($apartmentList as $index => $apartment)
                    <div wire:click="selectApartment({{ $apartment->id }})"
                        class="p-2 rounded-lg border hover:shadow-md transition-all cursor-pointer @if($selectedApartmentId == $apartment->id) active-society @endif">
                        <div class="flex flex-col gap-1">
                            <p class="text-gray-900 dark:text-white text-base font-bold truncate">{{ $apartment->building_name }} - {{ $apartment->apartment_number }}</p>
                            <p class="text-gray-500 dark:text-gray-400 text-xs truncate">{{ $apartment->society->society_name }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center border-2 border-dashed border-gray-200 rounded-lg">
                        <p class="text-gray-500 text-sm">No apartments found matching "{{ $search }}"</p>
                    </div>
                @endforelse
            </div>
        </aside>
        <!-- Main -->
        <div wire:loading.flex wire:target="selectApartment" class="justify-center items-center py-4">
            <div class="animate-spin rounded-full h-6 w-6 border-2 border-t-transparent border-green-500"></div>
            <span class="ml-2 text-sm text-gray-600">Loading...</span>
        </div>
        <main class="col-span-2">
            <div class="flex flex-col gap-4" wire:loading.remove wire:target="selectApartment">
                <div class="p-2">
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
                        <div class="flex flex-col gap-4">
                            <div class="rounded-lg border border-gray-200 shadow-sm hover:shadow-lg transition-shadow my-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 border-b border-gray-300 relative">
                                    <div class="p-2">
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2 border-t border-gray-50 dark:border-gray-700">
                                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $details->building_name }} - {{ $details->apartment_number }}
                                                <flux:badge color="blue" icon="building-office" size="md">
                                                        {{ $details->society->society_name }}
                                                    </flux:badge>
                                            </p>
                                            @for($i=1; $i<=3; $i++)
                                                @php $name = "owner{$i}_name"; $phone = "owner{$i}_mobile"; @endphp
                                                @if($details->$name)
                                                <div class="p-2 rounded-lg bg-gray-50/50 dark:bg-gray-700/30">
                                                    <p class="text-[9px] uppercase font-bold text-gray-400 mb-0.5 tracking-wider">Owner {{ $i }}</p>
                                                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300 truncate">{{ $details->$name }}</p>
                                                    <p class="text-[10px] text-gray-500 font-medium">{{ $details->$phone }}</p>
                                                </div>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    {{-- <div class="p-4">
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $details->building_name }} - {{ $details->apartment_number }}
                                            <flux:badge color="purple" size="sm">
                                                    {{ $details->society->society_name }}
                                                </flux:badge>
                                        </p>
                                        @if($details->owner1_mobile)<p class="text-sm text-gray-500 dark:text-white">Owner1 Phone: {{ $details->owner1_mobile }}</p>@endif
                                        @if($details->owner1_email)<p class="text-sm text-gray-500 dark:text-white">Email: {{ $details->owner1_email }}</p>@endif
                                        <p class="mb-1"></p>
                                        @if($details->owner2_mobile)<p class="text-sm text-gray-500 dark:text-white">Owner2 Phone: {{ $details->owner2_mobile }}</p>@endif
                                        @if($details->owner2_email)<p class="text-sm text-gray-500 dark:text-white">Email: {{ $details->owner2_email }}</p>@endif
                                        <p class="mb-1"></p>
                                        @if($details->owner3_mobile)<p class="text-sm text-gray-500 dark:text-white">Owner3 Phone: {{ $details->owner3_mobile }}</p>@endif
                                        @if($details->owner3_email)<p class="text-sm text-gray-500 dark:text-white">Email: {{ $details->owner3_email }}</p>@endif
                                    </div> --}}
                                    <div class="p-2">
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
                                    @php
                                        $statusData = json_decode($details->status, true);
                                        $tasks = collect($statusData['tasks'] ?? []);
                                        $applicationStatus = $tasks->firstWhere('name', 'Application')['Status'] ?? null;
                                    @endphp

                                    @if($applicationStatus !== 'Approved')
                                        <flux:tooltip content="Edit Apartment">
                                            <button class="font-bold absolute top-0 right-0 px-2 py-1 border rounded-md cursor-pointer" wire:click="verifyDetails({{ $details->id }})">
                                                <i class="fa-solid fa-edit text-sm"></i>
                                            </button>
                                        </flux:tooltip>
                                    @else
                                        <flux:tooltip content="View Apartment">
                                            <button class="font-bold absolute top-0 right-0 px-2 py-1 border rounded-md cursor-pointer" wire:click="viewDetails({{ $details->id }})">
                                                <i class="fa-solid fa-eye text-sm text-blue-500"></i>
                                            </button>
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
                                        $statusData = json_decode($details->status, true);
                                        $tasks = collect($statusData['tasks'] ?? []);

                                        // Get statuses by task name
                                        $verifyStatus = $tasks->firstWhere('name', 'Verify Details')['Status'] ?? null;
                                        $applicationStatus = $tasks->firstWhere('name', 'Application')['Status'] ?? null;
                                        $verificationStatus = $tasks->firstWhere('name', 'Verification')['Status'] ?? null;
                                        $generationStatus = $tasks->firstWhere('name', 'Certificate Generated')['Status'] ?? null;
                                    @endphp
                                    @php
                                    $showCertificateDownloadButton = ($verifyStatus === 'Approved' && $applicationStatus === 'Approved' && $verificationStatus === 'Approved' && $generationStatus==='Approved');
                                    @endphp
                                    @if($showCertificateDownloadButton)
                                    <div class="flex items-center text-xs justify-center cursor-pointer"
                                    onclick="window.open('{{ route('menus.certificate.view', ['id' => $details->id]) }}', '_blank')">
                                        <flux:badge color="blue" size="sm">
                                        View Certificate
                                        </flux:badge>
                                    </div>
                                    @endif
                                </div>
                                <!--Bye Laws Section -->
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-2 p-3">
                                    @if($details->byeLawCase && $details->byeLawCase->membership_case=='case_a')
                                        @php $fileUrl = asset('storage/society_docs/' . $details->byeLawCase->allotmentMembershipLetter);
                                        $isApproved =$this->getFileStatus($statusData, $details->byeLawCase->allotmentMembershipLetter);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Allotment Membership Letter
                                        </div>
                                    @endif

                                    @if($details->byeLawCase && $details->byeLawCase->membership_case=='case_b')
                                        @php $fileUrl = asset('storage/society_docs/' . $details->byeLawCase->stampDutyProof);
                                        $isApproved =$this->getFileStatus($statusData, $details->byeLawCase->stampDutyProof);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Stamp Duty Proof
                                        </div>

                                        @php $fileUrl = asset('storage/society_docs/' . $details->byeLawCase->transferorSignature);
                                        $isApproved =$this->getFileStatus($statusData, $details->byeLawCase->transferorSignature);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Transferor Signature
                                        </div>
                                    @endif

                                    @if($details->byeLawCase && $details->byeLawCase->membership_case=='case_c')
                                        @php $fileUrl = asset('storage/society_docs/' . $details->byeLawCase->deathCertificate);
                                        $isApproved =$this->getFileStatus($statusData, $details->byeLawCase->deathCertificate);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Death Certificate
                                        </div>

                                        @php $fileUrl = asset('storage/society_docs/' . $details->byeLawCase->nominationRecord);
                                        $isApproved =$this->getFileStatus($statusData, $details->byeLawCase->nominationRecord);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Nomination Record
                                        </div>
                                    @endif

                                    @if($details->byeLawCase && $details->byeLawCase->membership_case=='case_d')
                                        @php $fileUrl = asset('storage/society_docs/' . $details->byeLawCase->successionCertificate);
                                        $isApproved =$this->getFileStatus($statusData, $details->byeLawCase->successionCertificate);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Succession Certificate
                                        </div>
                                    @endif

                                    @if($details->byeLawCase && $details->byeLawCase->membership_case=='case_a')
                                        @php 
                                            $fileName = 'Appendix 2';
                                            $fileUrl = route('appendix.two', ['byelaws_id' => $details->byeLawCase->id]);
                                            $isApproved = $this->getFileStatus($statusData, $fileName);
                                        @endphp
                                        <div class="flex items-center text-xs  cursor-pointer" wire:click="viewDocument('{{ $details->id }}', '{{ $fileUrl }}', '{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Appendix 2
                                        </div>
                                    
                                        @php 
                                            $fileName = 'Appendix 3';
                                            $fileUrl = route('appendix.three', ['byelaws_id' => $details->byeLawCase->id]);
                                            $isApproved = $this->getFileStatus($statusData, $fileName);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}', '{{ $fileUrl }}', '{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Appendix 3
                                        </div>
                                    @endif
                                    @if($details->byeLawCase &&$details->byeLawCase->membership_case=='case_c')
                                        @php 
                                            $fileName = 'Appendix 15';
                                            $fileUrl = route('appendix.fifteen', ['byelaws_id' => $details->byeLawCase->id]);
                                            $isApproved = $this->getFileStatus($statusData, $fileName);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}', '{{ $fileUrl }}', '{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Appendix 15
                                        </div>
                                    @endif
                                    @if($details->byeLawCase &&$details->byeLawCase->membership_case=='case_d')
                                        @php 
                                            $fileName = 'Appendix 16';
                                            $fileUrl = route('appendix.sixteen', ['byelaws_id' => $details->byeLawCase->id]);
                                            $isApproved = $this->getFileStatus($statusData, $fileName);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}', '{{ $fileUrl }}', '{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Appendix 16
                                        </div>
                                        @php 
                                            $fileName = 'Appendix 19';
                                            $fileUrl = route('appendix.nineteen', ['byelaws_id' => $details->byeLawCase->id]);
                                            $isApproved = $this->getFileStatus($statusData, $fileName);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}', '{{ $fileUrl }}', '{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Appendix 19
                                        </div>
                                    @endif
                                    @if($details->byeLawCase &&$details->byeLawCase->membership_case=='case_b')
                                        @php 
                                            $fileName = 'Appendix 20(1)';
                                            $fileUrl = route('appendix.twenty-part-one', ['byelaws_id' => $details->byeLawCase->id]);
                                            $isApproved = $this->getFileStatus($statusData, $fileName);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}', '{{ $fileUrl }}', '{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Appendix 20(1)
                                        </div>
                                        @php 
                                            $fileName = 'Appendix 20(2)';
                                            $fileUrl = route('appendix.twenty-part-two', ['byelaws_id' => $details->byeLawCase->id]);
                                            $isApproved = $this->getFileStatus($statusData, $fileName);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}', '{{ $fileUrl }}', '{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Appendix 20(2)
                                        </div>
                                        @php 
                                            $fileName = 'Appendix 21';
                                            $fileUrl = route('appendix.twenty-one', ['byelaws_id' => $details->byeLawCase->id]);
                                            $isApproved = $this->getFileStatus($statusData, $fileName);
                                        @endphp
                                        <div class="flex items-center text-xs cursor-pointer" wire:click="viewDocument('{{ $details->id }}', '{{ $fileUrl }}', '{{ $isApproved }}')">
                                            @if($isApproved=='Approved')
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-check text-green-600"></i></span>
                                            @else
                                            <span class="mr-2 flex items-center justify-center"><i class="fa-regular fa-circle-xmark text-red-600"></i></span>
                                            @endif
                                            Appendix 21
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>

    <flux:modal  wire:model="showDocumentModal" class="!max-w-3xl w-full">
        <div class="space-y-6">
            @php
                if (str_contains($url, 'appendix')) {
                    $fileNamePart = basename(parse_url($url, PHP_URL_PATH));
                    $fileNameMap = [
                        'appendix-two' => 'Appendix 2',
                        'appendix-three' => 'Appendix 3',
                        'appendix-fifteen' => 'Appendix 15',
                        'appendix-sixteen' => 'Appendix 16',
                        'appendix-nineteen' => 'Appendix 19',
                        'appendix-twenty-part-one' => 'Appendix 20(1)',
                        'appendix-twenty-part-two' => 'Appendix 20(2)',
                        'appendix-twenty-one' => 'Appendix 21',
                    ];
                    foreach($fileNameMap as $key => $value) {
                        if (str_contains($url, $key)) {
                            $fileName = $value;
                            break;
                        }
                    }
                    $extension = 'pdf';
                } else {
                    $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                    $fileName = trim(basename($url));
                }
            @endphp
            <div class="flex items-center justify-between">
                <flux:heading size="lg">Document View</flux:heading>
                <div class="flex items-center gap-2 mr-10">
                    @if($url)
                        <a href="{{ $url }}" 
                        download="{{ $fileName }}.{{ $extension }}" 
                        target="_blank"
                        class="flex items-center gap-2 bg-blue-500 text-white px-3 py-1.5 rounded-md text-sm font-medium hover:bg-blue-600">
                            <i class="fa-solid fa-download"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Images --}}
            @if(in_array($extension, ['jpg','jpeg','png']))
                <img src="{{ $url }}" alt="preview" class="w-full rounded" />

            {{-- PDF --}}
            @elseif($extension === 'pdf' || str_contains($url, 'appendix'))
                <iframe src="{{ $url }}#toolbar=0" class="w-full h-[70vh]" frameborder="0"></iframe>
            @endif
            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Close</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>