
<div>
    <div class="w-full">
        <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading size="lg" class="mb-4">{{ __('Welcome Admin!') }}</flux:subheading>
        </div>
        <div class="flex gap-4">
            <flux:tooltip content="Create Society">
            <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click="redirectToCreateSociety"><i class="fa-solid fa-plus"></i></button>
            </flux:tooltip>
            <flux:tooltip content="Add Apartment To Society">
            <button type="button" class="bg-amber-500 text-white font-bold py-2 px-4 rounded cursor-pointer" wire:click="redirectToCreateApartment"><i class="fa-solid fa-building"></i></button>
            </flux:tooltip>
        </div>
    </div>
    <flux:separator variant="subtle" />
    <div class="grid grid-cols-1 md:grid-cols-5 p-2">
        <div class="p-2">
            <div class="bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center">
                <div class="flex items-center justify-center text-center gap-2">
                    <span class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-inbox text-sm"></i></span> 
                    <p class="text-sm font-medium dark:text-white">Pending Application</p>
                </div>
                <p class=" text-2xl font-bold text-center dark:text-white">{{ $pendingApplicationCount }}</p>
            </div>
        </div>
        <div class="p-2">
            <div class="bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center">
                <div class="flex items-center justify-center text-center gap-2">
                    <span class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-list text-sm"></i></span> 
                    <p class="text-sm font-medium dark:text-white">Pending Verification</p>
                </div>
                <p class="text-2xl font-bold text-center dark:text-white">{{ $pendingVerificationCount }}</p>
            </div>
        </div>
        <div class="p-2">
            @if($rejectedVerificationCount>0)
            <div class="cursor-pointer bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center" wire:click="redirectToSocietyDetail(3)">
            @else
            <div class="bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center">
            @endif
                <div class="flex items-center justify-center text-center gap-2">
                    <span class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-circle-xmark text-sm"></i></span> 
                    <p class="text-sm font-medium dark:text-white">Rejected Verification</p>
                </div>
                <p class="text-2xl font-bold text-center dark:text-white">{{ $rejectedVerificationCount }}</p>
            </div>
        </div>
        <div class="p-2">
            <div class="bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center">
                <div class="flex items-center justify-center text-center gap-2">
                    <span class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-certificate text-sm"></i></span> 
                    <p class="text-sm font-medium dark:text-white">Certificate Issue</p>
                </div>
                <p class="text-2xl font-bold text-center dark:text-white">0</p>
            </div>
        </div>
        <div class="p-2">
            <div class="cursor-pointer bg-zinc-200 dark:bg-zinc-600 rounded-xl p-4 text-center" wire:click="markRoleByAdmin">
                <div class="flex items-center justify-center text-center gap-2">
                    <span class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-zinc-600"><i class="fa-solid fa-users text-sm"></i></span> 
                    <p class="text-sm font-medium dark:text-white">Mark User Role</p>
                </div>
                <p class="text-2xl font-bold text-center dark:text-white">{{ $usersCount }}</p>
            </div>
        </div>
    </div>
        
    {{-- <div class="grid grid-cols-1 md:grid-cols-2">
        <div class="py-2 w-64">
            <flux:select wire:model.live="selectedOption" placeholder="Choose Society...">
                <flux:select.option value="">Choose Society...</flux:select.option>
                @foreach($society  as $row)
                    <flux:select.option value="{{ $row->id }}">{{ $row->society_name }} , (Total Flats: {{ $row->total_flats }})</flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <div class="p-2">
            <div x-data="{ tab: '' }" class="space-y-4">
                <div class="flex border-b">
                    <button @click="tab = 'pending'" :class="tab === 'pending' ? 'border-amber-500 text-amber-600' : 'text-gray-500'" class="px-4 py-2 border-b-2 font-medium">
                    Pending ({{ $pendingVerificationStatusCount }})
                    </button>
                    <button @click="tab = 'approved'" :class="tab === 'approved' ? 'border-amber-500 text-amber-600' : 'text-gray-500'" class="px-4 py-2 border-b-2 font-medium">
                    Approved ({{ $approvedVerificationStatusCount }})
                    </button>
                    <button @click="tab = 'rejected'" :class="tab === 'rejected' ? 'border-amber-500 text-amber-600' : 'text-gray-500'" class="px-4 py-2 border-b-2 font-medium">
                    Rejected ({{ $rejectedVerificationStatusCount }})
                    </button>
                </div>
                <div x-show="tab === 'pending'" class="p-4">
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Pending'], key("'status-table-'.$selectedOption"))
                </div>
                <div x-show="tab === 'approved'" class="p-4" x-cloak>
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Approved'], key("'status-table-'.$selectedOption"))
                </div>
                <div x-show="tab === 'rejected'" class="p-4" x-cloak>
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Rejected'], key("'status-table-'.$selectedOption"))
                </div>
            </div>
        </div>
    </div> --}}
    
    {{-- <div class="card">
        <div class="card-body">
            <div x-data="{ activeTab: ''}" class="w-full p-4">
                <!-- Tabs + Dropdown wrapper -->
                <div class="grid grid-cols-1 md:grid-cols-2 border-b border-gray-300 mb-4">
                    <!-- Dropdown first on mobile, second on desktop -->
                    <div class="order-1 sm:order-2 mb-3 sm:mb-0">
                        <flux:select wire:model.live="selectedOption" placeholder="Choose Society..." placement="top-end">
                            <flux:select.option value="">Choose Society...</flux:select.option>
                            @foreach($society  as $row)
                                <flux:select.option value="{{ $row->id }}">{{ $row->society_name }} , (Total Flats: {{ $row->total_flats }})</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    <!-- Tabs second on mobile, first on desktop -->
                    <div class="order-2 sm:order-1 flex space-x-6">
                        <button @click="activeTab = 'pending'"
                        :class="activeTab === 'pending' ? 'text-amber-600 border-b-2 border-amber-600 font-semibold' : 'text-zinc-600 dark:text-white hover:text-amber-600 border-b-2 border-transparent text-[10px] sm:text-sm'"
                        class="py-2 px-4">
                        Pending ({{ $pendingVerificationStatusCount }})
                        </button>
                        <button @click="activeTab = 'approved'"
                        :class="activeTab === 'approved' ? 'text-amber-600 border-b-2 border-amber-600 font-semibold' : 'text-zinc-600 dark:text-white hover:text-amber-600 border-b-2 border-transparent text-[10px] sm:text-sm'"
                        class="py-2 px-4">
                        Approved ({{ $approvedVerificationStatusCount }})
                        </button>
                        <button @click="activeTab = 'rejected'"
                        :class="activeTab === 'rejected' ? 'text-amber-600 border-b-2 border-amber-600 font-semibold' : 'text-zinc-600 dark:text-white hover:text-amber-600 border-b-2 border-transparent text-[10px] sm:text-sm'"
                        class="py-2 px-4">
                        Rejected ({{ $rejectedVerificationStatusCount }})
                        </button>
                    </div>
                </div>  
                <!-- Tab Content -->
                <div>
                <!-- Tab 1 -->
                <div x-show="activeTab === 'pending'" class="bg-stone-200 dark:bg-stone-800 shadow rounded-lg p-4">
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Pending'], key("'status-table-'.$selectedOption"))
                </div>

                <!-- Tab 2 -->
                <div x-show="activeTab === 'approved'" class="bg-stone-200 dark:bg-stone-800 shadow rounded-lg p-4">
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Approved'], key("'status-table-'.$selectedOption"))
                </div>

                <!-- Tab 3 -->
                <div x-show="activeTab === 'rejected'" class="bg-stone-200 dark:bg-stone-800 shadow rounded-lg p-4">
                    @livewire('menus.status-table',  ['societyId' => $selectedOption, 'statusType' => 'Rejected'], key("'status-table-'.$selectedOption"))
                </div>
            </div>
        </div>
    </div> --}}

    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-3 gap-4 h-90">
                {{-- Left Sidebar (1/3) --}}
                <div class="col-span-1 border-r border-gray-300 bg-gray-50 dark:bg-gray-800 p-1 overflow-y-auto">
                    <h2 class="text-lg font-semibold mb-2">Societies</h2>
                    <ul class="space-y-2">
                        @foreach($society as $row)
                            <li>
                                <button 
                                    wire:click="selectSociety({{ $row->id }})"
                                    class="w-full text-left px-2 py-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700">
                                    {{ $row->society_name }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Right Content (2/3) --}}
                <div class="col-span-2 p-4 overflow-y-auto">
                    <div class="mb-2">
                        <livewire:menus.alerts />
                    </div>
                    @if($societyDetail)
                    @foreach($societyDetail as $details)
                    <div class="grid grid-cols-1 md:grid-cols-3  border-b border-gray-300">
                        <!-- Left Column -->
                        <div class="p-4 rounded md:col-span-2">
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
                                @php
                                    $statusData = json_decode($details->status, true);
                                    $isApproved=false;
                                @endphp
                                @if($details->agreementCopy)
                                    @php $fileUrl = asset('storage/society_docs/' . $details->agreementCopy);
                                    $isApproved =$this->getFileStatus($statusData, $details->agreementCopy);
                                    @endphp
                                    <button class="inline-flex items-center justify-center rounded-full bg-stone-400 text-white px-1 py-1 text-[10px] font-medium dark:bg-gray-700 dark:hover:bg-gray-600" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">Copy of Agreement</button>
                                @endif
                                @if($details->memberShipForm)
                                    @php $fileUrl = asset('storage/society_docs/' . $details->memberShipForm);
                                    $isApproved =$this->getFileStatus($statusData, $details->memberShipForm);
                                    @endphp
                                    <button class="inline-flex items-center justify-center rounded-full bg-stone-400 text-white px-1 py-1 text-[10px] font-medium dark:bg-gray-700 dark:hover:bg-gray-600" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">Membership Form</button>
                                @endif
                                @if($details->allotmentLetter)
                                    @php $fileUrl = asset('storage/society_docs/' . $details->allotmentLetter);
                                    $isApproved =$this->getFileStatus($statusData, $details->allotmentLetter);
                                    @endphp
                                    <button class="inline-flex items-center justify-center rounded-full bg-stone-400 text-white px-1 py-1 text-[10px] font-medium dark:bg-gray-700 dark:hover:bg-gray-600" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">Allotment Letter</button> 
                                @endif
                                @if($details->possessionLetter)
                                    @php $fileUrl = asset('storage/society_docs/' . $details->possessionLetter); 
                                    $isApproved =$this->getFileStatus($statusData, $details->possessionLetter);
                                    @endphp
                                    <button class="inline-flex items-center justify-center rounded-full bg-stone-400 text-white px-1 py-1 text-[10px] font-medium dark:bg-gray-700 dark:hover:bg-gray-600" wire:click="viewDocument('{{ $details->id }}','{{ $fileUrl }}','{{ $isApproved }}')">Possession Letter</button> 
                                @endif 
                                <div>
                                    @php
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
                        <div class="p-4 rounded md:col-span-1">
                            <flux:tooltip content="Edit Apartment">
                                <button class="bg-amber-500 text-white font-bold py-1 px-2 rounded float-end" wire:click="fetchOwnersDetail('{{ $details->id }}')"><i class="fa-solid fa-edit text-sm"></i></button>
                                </flux:tooltip>
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
                                                    <span class="relative grid h-8 w-8 place-items-center rounded-full {{ in_array($task['Status'], ['Pending', 'Rejected']) ? 'bg-stone-400' : 'bg-amber-400'}}">
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
                    @endif
                </div>
            </div>

        </div>
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
                        @if($checkApproved=='')
                            <flux:modal.close>
                                <flux:button variant="primary" x-on:click="$wire.updateFileStatus('{{ $detailId }}','{{ $fileName }}','Approved')">Approve</flux:button>
                                <flux:button x-on:click="$wire.updateFileStatus('{{ $detailId }}','{{ $fileName }}','Rejected')" variant="danger">Reject</flux:button>
                            </flux:modal.close>
                        @endif
                    </div>
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
</div>
