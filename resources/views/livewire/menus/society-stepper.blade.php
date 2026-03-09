<div>
@if($societyDetails->isEmpty())
    <div class="rounded-lg border shadow-sm p-6 text-center">
        <p class="text-gray-500 dark:text-white">No apartments found for the selected society.</p>
    </div>
@else
@foreach($societyDetails as $details)
    @php
        $statusData = json_decode($details->status, true);
        $tasks = collect($statusData['tasks'] ?? []);

        // Get statuses by task name
        $verifyStatus = $tasks->firstWhere('name', 'Verify Details')['Status'] ?? null;
        $applicationStatus = $tasks->firstWhere('name', 'Application')['Status'] ?? null;
        $verificationStatus = $tasks->firstWhere('name', 'Verification')['Status'] ?? null;
        $generationStatus = $tasks->firstWhere('name', 'Certificate Generated')['Status'] ?? null;
    @endphp
    <div class="rounded-lg border shadow-sm hover:shadow-lg transition-shadow my-4">
        <div class="grid grid-cols-1 md:grid-cols-2 border-b relative">
            <div class="p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl dark:bg-blue-900/20">
                        <i class="fa-solid fa-building text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-blue-900 dark:text-white leading-tight">
                            {{ $details->building_name }} - {{ $details->apartment_number }}
                        </h3>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @if($details->no_of_shares)
                                <span class="bg-gray-50 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300 border border-gray-100 dark:border-gray-600">
                                    {{ $details->no_of_shares }} Shares
                                </span>
                            @endif
                            {{-- @if($details->share_capital_amount)
                                <span class="bg-gray-50 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300 border border-gray-100 dark:border-gray-600">
                                    ₹{{ number_format($details->share_capital_amount) }}
                                </span>
                            @endif --}}
                        </div>
                    </div>
                </div>
                {{-- Owners Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2 border-t border-gray-50 dark:border-gray-700">
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
            @php
                $showCertificateDownloadButton = ( $verifyStatus === 'Approved' && $applicationStatus === 'Approved' && $verificationStatus === 'Approved' && $generationStatus==='Approved');
            @endphp
            <div class="absolute top-0 right-0 flex space-x-2">
                @if($showCertificateDownloadButton)
                <flux:tooltip content="View Certificate">
                    <button class="font-bold px-2 py-1 border rounded-md" onclick="window.open('{{ route('admin.certificate.view', ['id' => $details->id]) }}', '_blank')">
                        <i class="fa-solid fa-certificate text-sm"></i>
                    </button>
                </flux:tooltip>
                @endif

                <flux:tooltip content="Edit Apartment">
                    <button class="font-bold px-2 py-1 border rounded-md" wire:click="fetchOwnersDetail('{{ $details->id }}')">
                        <i class="fa-solid fa-edit text-sm"></i>
                    </button>
                </flux:tooltip>
            </div>

            {{-- <flux:tooltip content="Edit Apartment">
                <button class="font-bold absolute top-0 right-0 px-2 py-1 border rounded-md" wire:click="fetchOwnersDetail('{{ $details->id }}')"><i class="fa-solid fa-edit text-sm"></i></button>
            </flux:tooltip>--}}
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
            {{-- @php
            $allApproved = $this->areAllFourFilesApproved($statusData, [$details->agreementCopy,$details->memberShipForm,$details->allotmentLetter,$details->possessionLetter]);
            $showVerificationButton = ($allApproved && $verifyStatus === 'Approved' && $applicationStatus === 'Approved' && $verificationStatus === 'Pending');
            @endphp
            @if ($showVerificationButton) 
            <div class="flex items-center text-xs justify-center cursor-pointer" wire:click="setDocument('{{ $details->id }}')">Verify Documents</div>
            @endif --}}
            {{-- @php
            $showCertificateGenerateButton = ($verifyStatus === 'Approved' && $applicationStatus === 'Approved' && $verificationStatus === 'Approved' && $generationStatus==='Pending');
            @endphp
            @if($showCertificateGenerateButton)
            <div class="flex items-center text-xs justify-center cursor-pointer" wire:click="generateCertificate('{{ $details->id }}')">Generate Certificate</div>
            @endif --}}
        </div>
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

            <!--------Appendix PDF -------->
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
        <div class="grid grid-cols-1">
            @php
                if($details->byeLawCase && $details->byeLawCase->membership_case=='case_a'){
                    $allApproved = $this->areAllFilesApproved($statusData, [$details->agreementCopy,$details->memberShipForm,$details->allotmentLetter,$details->possessionLetter,$details->byeLawCase->allotmentMembershipLetter, 'Appendix 2', 'Appendix 3']);
                }else if($details->byeLawCase && $details->byeLawCase->membership_case=='case_b'){
                    $allApproved = $this->areAllFilesApproved($statusData, [$details->agreementCopy,$details->memberShipForm,$details->allotmentLetter,$details->possessionLetter,$details->byeLawCase->stampDutyProof,$details->byeLawCase->transferorSignature, 'Appendix 20(1)', 'Appendix 20(2)', 'Appendix 21']);
                }else if($details->byeLawCase && $details->byeLawCase->membership_case=='case_c'){
                    $allApproved = $this->areAllFilesApproved($statusData, [$details->agreementCopy,$details->memberShipForm,$details->allotmentLetter,$details->possessionLetter,$details->byeLawCase->deathCertificate,$details->byeLawCase->nominationRecord, 'Appendix 15']);
                }else if($details->byeLawCase && $details->byeLawCase->membership_case=='case_d'){
                    $allApproved = $this->areAllFilesApproved($statusData, [$details->agreementCopy,$details->memberShipForm,$details->allotmentLetter,$details->possessionLetter,$details->byeLawCase->successionCertificate, 'Appendix 16', 'Appendix 19']);
                }
                $showVerificationButton = ($allApproved && $verifyStatus === 'Approved' && $applicationStatus === 'Approved' && $verificationStatus === 'Pending');
            @endphp
            @if ($showVerificationButton) 
                <button class="rounded-md p-2 text-md font-medium cursor-pointer bg-blue-500" wire:click="setDocument('{{ $details->id }}')">Verify Documents</button>
            @endif
            @php
            $showCertificateGenerateButton = ($verifyStatus === 'Approved' && $applicationStatus === 'Approved' && $verificationStatus === 'Approved' && $generationStatus==='Pending');
            @endphp
            @if($showCertificateGenerateButton)
            <button class="rounded-md p-3 text-md font-medium cursor-pointer bg-green-500" wire:click="generateCertificate('{{ $details->id }}')">Generate Certificate</button>
            @endif

            @if($details->certificate_remark && $details->certificate_status === 'changes_required')    
                <div class="rounded-md p-2 text-md font-medium cursor-pointer bg-blue-200" wire:click="setDocument('{{ $details->id }}')">Certificate Remark : {{ $details->certificate_remark }}</div>
            @endif
        </div>
    </div>
@endforeach
@endif
<!--Modal-->
<flux:modal  wire:model="showDocumentModal" class="!max-w-3xl w-full">
    <div class="space-y-6">
        @php
            if (str_contains($url, 'appendix')) {
                $fileNamePart = basename(parse_url($url, PHP_URL_PATH));
                // Map numeric filenames back to display names for status tracking
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
        <div class="flex items-center justify-between pr-8">
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
        <div class="flex justify-between items-center">
            <div class="flex gap-2">
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
            <flux:modal.close>
                <flux:button variant="ghost">Close</flux:button>
            </flux:modal.close>
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
                <flux:input type="text"  :label="__('Certificate No :')" wire:model="certificate_no" />
                <flux:input type="number"  :label="__('No of Each Share :')" wire:model="individual_no_of_share" />
                {{-- <flux:input type="number"  :label="__('Each Share Amount :')" wire:model="share_capital_amount" /> --}}
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