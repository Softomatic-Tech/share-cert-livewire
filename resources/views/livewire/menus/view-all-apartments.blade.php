<div class="w-full">
    <div class="py-4">
        <livewire:menus.alerts />
    </div>
    <div class="flex justify-between items-center mt-2">
        <h1 class="text-xl font-bold">View Apartments</h1>
        <div class="flex gap-4">
        <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 rounded" wire:click="redirectToCreateApartment">Add Apartment</button>
        <button type="button" class="bg-stone-500 text-white font-bold py-2 px-4 rounded" x-on:click="window.history.back()">Back</button>
        </div>
    </div>

   
    <div class="max-h-[500px] overflow-y-auto pr-2">
        <div class="p-6 bg-gray-50">
            <div class="grid grid-cols-2 gap-4 font-semibold text-gray-700 border-b pb-2 mb-2">
                <div class="text-center font-extrabold">APARTMENT</div>
                <div class="text-center font-extrabold">STATUS</div>
            </div>

            <!-- Apartment Card -->
            @foreach($societyDetails as $details)
            <div class="grid grid-cols-2 gap-4 py-4 border-b border-gray-300">
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
                        <div class="mt-4 flex flex-nowrap gap-4">
                            @if($details->agreementCopy)
                            <div class="border-1 border-amber-500 rounded p-1">
                                <flux:tooltip content="Copy of Agreement">
                                    <a href="{{ asset('storage/' . $details->agreementCopy) }}" target="_blank"><img src="{{ asset('images/document.svg') }}" alt="Copy of Agreement" class="w-6 h-6"></a>
                                </flux:tooltip>
                                {{-- <flux:modal.trigger name="documentModal">
                                    <flux:button x-on:click="$wire.setDocument('Copy of Agreement','{{ $details->agreementCopy }}','{{ $details->id }}')">Copy of Agreement <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></flux:button>
                                </flux:modal.trigger>--}}
                            </div> 
                            @endif
                            @if($details->membershipForm)
                            <div class="border-1 border-amber-500 rounded p-1">
                                <flux:tooltip content="Membership Form">
                                    <a href="{{ asset('storage/' . $details->membershipForm) }}" target="_blank"><img src="{{ asset('images/document.svg') }}" alt="Membership Form" class="w-6 h-6"></a>
                                </flux:tooltip>
                                {{-- <flux:modal.trigger name="documentModal">
                                    <flux:button x-on:click="$wire.setDocument('Membership Form','{{ $details->membershipForm }}','{{ $details->id }}')">Membership Form <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></flux:button>
                                </flux:modal.trigger>--}}
                            </div>       
                            @endif
                            @if($details->allotmentLetter)
                            <div class="border-1 border-amber-500 rounded p-1">
                                <flux:tooltip content="Allotment Letter">
                                    <a href="{{ asset('storage/' . $details->allotmentLetter) }}" target="_blank"><img src="{{ asset('images/document.svg') }}" alt="Allotment Letter" class="w-6 h-6"></a>
                                </flux:tooltip>
                                {{-- <flux:modal.trigger name="documentModal">
                                    <flux:button x-on:click="$wire.setDocument('Allotment Letter','{{ $details->allotmentLetter }}','{{ $details->id }}')">Allotment Letter <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></flux:button>
                                </flux:modal.trigger>--}}
                            </div>       
                            @endif
                            @if($details->possessionLetter)
                            <div class="border-1 border-amber-500 rounded p-1">
                                <flux:tooltip content="Possession Letter">
                                    <a href="{{ asset('storage/' . $details->possessionLetter) }}" target="_blank"><img src="{{ asset('images/document.svg') }}" alt=" Possession Letter" class="w-6 h-6"></a>
                                </flux:tooltip>
                                {{-- <flux:modal.trigger name="documentModal">
                                    <flux:button x-on:click="$wire.setDocument('Possession Letter','{{ $details->possessionLetter }}','{{ $details->id }}')">Possession Letter <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></flux:button>
                                </flux:modal.trigger>--}}
                            </div>
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
                                    <flux:modal.trigger name="documentModal">
                                        <flux:button variant="primary" x-on:click="$wire.setDocument('{{ $details->id }}')">Need Info</flux:button>
                                    </flux:modal.trigger>
                                @endif
                                @endif
                            </div> 
                        </div>
                    </div>
                </div>
                <!-- Status Info -->
                @php
                $statusData = json_decode($details->status, true);
                @endphp
                @if(isset($statusData['tasks']))
                <div data-dui-stepper-container data-dui-initial-step="1" class="w-full">
                    <div class="flex 
                    w-full items-center justify-between">
                        @foreach(collect($statusData['tasks'])->skip(1) as $task)
                        <div aria-disabled="false" data-dui-step class="group w-full flex items-center">
                            <div class="relative">
                                <span class="relative grid h-10 w-10 place-items-center rounded-full {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'bg-stone-400' : 'bg-amber-400'}}">
                                <i class="fa-solid fa-check text-white"></i>
                                </span>
                                @if($task['name']=='Application')
                                <span class="absolute -bottom-6 start-0 whitespace-nowrap text-sm {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'text-stone-500 font-normal' : 'text-stone-800 font-extrabold'}}">Applied</span>
                                @elseif($task['name']=='Verification')
                                <span class="absolute -bottom-6 start-0 whitespace-nowrap text-sm {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'text-stone-500 font-normal' : 'text-stone-800 font-extrabold'}}">Verification</span>
                                @elseif($task['name']=='Certificate Generated')
                                <span class="absolute -bottom-6 start-0 whitespace-nowrap text-sm {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'text-stone-500 font-normal' : 'text-stone-800 font-extrabold'}}">Waiting</span>
                                @elseif($task['name']=='Certificate Delivered')
                                <span class="absolute -bottom-6 start-0 whitespace-nowrap text-sm {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'text-stone-500 font-normal' : 'text-stone-800 font-extrabold'}}">Delivered</span>
                                @endif
                            </div>
                            @if(!$loop->last)
                            <div class="flex-1 h-1 {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'bg-stone-400' : 'bg-amber-400'}}"></div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    
        <!--Modal-->
        <flux:modal name="documentModal" class="md:w-96">
            <div class="space-y-6">
                <div >
                    <flux:heading size="lg">Document Approval</flux:heading>
                    <flux:text class="mt-2">
                        <a href="{{ asset('storage/society_docs/'. $documentName) }}" target="_blank" class="text-blue-600 underline">{{ $title }}</a>   
                    </flux:text>
                </div>

                {{-- Comment Field (only when rejecting) --}}
                @if($isRejecting)
                    <flux:textarea type="text" wire:model="comment" placeholder="Enter reason for rejection..." value="{{ $comment }}"/>
                    @error('comment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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