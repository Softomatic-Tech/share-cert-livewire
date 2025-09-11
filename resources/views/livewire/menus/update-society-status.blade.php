<section>
    <div class="w-full">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="#">User</flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="#">Verify Society Details</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:separator variant="subtle" />

        <div class="rounded-lg shadow-lg py-4 px-6">
            <div class="stepwizard">
                <div class="stepwizard-step">
                    <button type="button">1</button>
                    <p>Verify Details</p>
                </div>
                <div class="stepwizard-step">
                    <button type="button">2</button>
                    <p>Upload Documents</p>
                </div>
                <div class="stepwizard-step">
                    <button type="button">3</button>
                    <p>Verify And Submit</p>
                </div>
            </div>
            <div class="mb-2">
                <livewire:menus.alerts />
            </div>
            <!-- Verify Details -->
            @if($currentStep == 1)
                <div class="step-one">
                    <div class="card">
                        <div class="card-header font-bold">Step 1: Verify Details</div>
                        <div class="card-body">
                            <form wire:submit.prevent="nextStep">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-2">
                                    <flux:input type="text" :label="__('Society Name :')" wire:model="society_name" />
                                    <flux:input type="text" :label="__('Total Flats :')" wire:model="total_flats" />
                                    <flux:input type="text"  :label="__('Address Line 1 :')" wire:model="address_1" />
                                    <flux:input type="text"  :label="__('Address Line 2 :')" wire:model="address_2" />
                                    <flux:select wire:model.live="state_id" placeholder="Choose State..." :label="__('State')">
                                        <flux:select.option value="">Choose State...</flux:select.option>
                                        @foreach($states  as $st)
                                            <flux:select.option value="{{ $st->id }}">{{ $st->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:select wire:model="city_id" placeholder="Choose City..." :label="__('City')">
                                        <flux:select.option value="">Choose City...</flux:select.option>
                                        @foreach($cities  as $ct)
                                            <flux:select.option value="{{ $ct->id }}">{{ $ct->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:input type="text" :label="__('Pincode')" wire:model="pincode" />
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
                                    <flux:button variant="primary" type="button" wire:click="nextStep">{{ __('Next') }}</flux:button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Step 2: Upload Documents -->
            @if($currentStep == 2)
                <div class="step-two">
                    <div class="card">
                        <div class="card-header font-bold">Step 2: Upload Documents</div>
                            <div class="card-body">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                                    <!-- Form 1 -->
                                    <form wire:submit.prevent="uploadAgreementCopy" class="p-4 border rounded-md shadow-sm">
                                        @if(! in_array($agreementCopy, $approvedFiles))
                                        <label class="block font-semibold mb-2">Xerox Copy Of Agreement</label>
                                        <div class="w-full mb-4" wire:key="file-input-{{ $fileKey }}">
                                            <flux:input type="file" wire:model="newAgreementCopy" class="border border-gray-300 rounded px-2 py-1 w-full" />
                                            @error('newAgreementCopy') <span class="text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="p-4">
                                                @if ($agreementCopy && $agreementUploaded)
                                                <a href="{{ asset('storage/society_docs/'.$agreementCopy) }}" 
                                                    target="_blank"  class="inline-flex items-center px-4 py-2 bg-amber-100 text-amber-700 font-semibold rounded-md hover:bg-amber-200 transition duration-200">
                                                        IMPORT FILE <i class="fa-solid fa-file-import"></i>
                                                </a>
                                                @endif 
                                            </div>
                                            
                                            <div class="p-4">
                                                @if ($newAgreementCopy)
                                                <flux:button variant="filled" type="submit" class="w-full" wire:loading.attr="disabled" wire:target="newAgreementCopy, uploadAgreementCopy">{{ __('UPLOAD FILE') }} <i class="fa-solid fa-upload"></i></flux:button>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </form>
                                    
                                    <!-- Form 2 -->
                                    @if(! in_array($memberShipForm, $approvedFiles))
                                    <form wire:submit.prevent="uploadMemberShipForm" class="p-4 border rounded-md  shadow-sm">
                                        <label class="block font-semibold mb-2">MemberShip Form</label>
                                        <div class="w-full mb-4" wire:key="file-input-{{ $fileKey }}">
                                            <flux:input type="file" wire:model="newMemberShipForm" class="border border-gray-300 rounded px-2 py-1 w-full" />
                                            @error('newMemberShipForm') <span class="text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                    
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="p-4">
                                            @if ($memberShipForm && $membershipUploaded)
                                                <a href="{{ asset('storage/society_docs/'.$memberShipForm) }}" 
                                                    target="_blank"  class="inline-flex items-center px-4 py-2 bg-amber-100 text-amber-700 font-semibold rounded-md hover:bg-amber-200 transition duration-200">
                                                        IMPORT FILE <i class="fa-solid fa-file-import"></i> 
                                                </a>
                                            @endif
                                            </div>
                                            <div class="p-4">
                                            @if ($newMemberShipForm)
                                                <flux:button variant="filled" type="submit" class="w-full" wire:loading.attr="disabled" wire:target="newMemberShipForm, uploadMemberShipForm">{{ __('UPLOAD FILE') }} <i class="fa-solid fa-upload"></i></flux:button>
                                            @endif
                                            </div>
                                        </div>
                                    </form>
                                    @endif
                                    <!-- Form 3 -->
                                    @if(! in_array($allotmentLetter, $approvedFiles))
                                    <form wire:submit.prevent="uploadAllotmentLetter" class="p-4 border rounded-md  shadow-sm">
                                        <label class="block font-semibold mb-2">Parking Allotment Letter</label>
                                        <div class="w-full mb-4" wire:key="file-input-{{ $fileKey }}">
                                            <flux:input type="file" wire:model="newAllotmentLetter" class="border border-gray-300 rounded px-2 py-1 w-full" />
                                            @error('newAllotmentLetter') <span class="text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                    
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="p-4">
                                                @if($allotmentLetter && $allotmentUploaded)
                                                <a href="{{ asset('storage/society_docs/'.$allotmentLetter) }}" 
                                                    target="_blank"  class="inline-flex items-center px-4 py-2 bg-amber-100 text-amber-700 font-semibold rounded-md hover:bg-amber-200 transition duration-200">
                                                        IMPORT FILE <i class="fa-solid fa-file-import"></i> 
                                                </a>
                                            @endif
                                            </div>
                                            <div class="p-4">
                                                @if ($newAllotmentLetter)
                                                <flux:button variant="filled" type="submit" class="w-full" wire:loading.attr="disabled" wire:target="newAllotmentLetter, uploadAllotmentLetter">{{ __('UPLOAD FILE') }} <i class="fa-solid fa-upload"></i></flux:button>
                                            @endif
                                            </div>
                                        </div>
                                    </form>
                                    @endif

                                    <!-- Form 4 -->
                                    @if(! in_array($possessionLetter, $approvedFiles))
                                    <form wire:submit.prevent="uploadPossessionLetter" class="p-4 border rounded-md  shadow-sm">
                                        <label class="block font-semibold mb-2">Possession Letter</label>
                                        <div class="w-full mb-4" wire:key="file-input-{{ $fileKey }}">
                                            <flux:input type="file" wire:model="newPossessionLetter" class="border border-gray-300 rounded px-2 py-1 w-full" />
                                            @error('newPossessionLetter') <span class="text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="p-4">
                                            @if( $possessionLetter && $possessionUploaded)
                                                <a href="{{ asset('storage/society_docs/'.$possessionLetter) }}" 
                                                    target="_blank"  class="inline-flex items-center px-4 py-2 bg-amber-100 text-amber-700 font-semibold rounded-md hover:bg-amber-200 transition duration-200">
                                                        IMPORT FILE <i class="fa-solid fa-file-import"></i> 
                                                </a>
                                            @endif
                                            </div>
                                            <div class="p-4">
                                            @if ($newPossessionLetter)
                                                <flux:button variant="filled" type="submit" class="w-full" wire:loading.attr="disabled" wire:target="newPossessionLetter, uploadPossessionLetter">{{ __('UPLOAD FILE') }} <i class="fa-solid fa-upload"></i></flux:button>
                                            @endif
                                            </div>
                                        </div>
                                    </form>
                                    @endif
                                </div>

                                <div class="flex justify-end mt-4">
                                    <flux:button variant="filled" class="mr-2" type="button" wire:click="prevStep">{{ __('Back') }}</flux:button>
                                    <flux:button variant="primary" type="button" wire:click="nextStep">{{ __('Next') }}</flux:button>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            @endif
            <!-- Step 3: Verification -->
            @if($currentStep == 3)
                <div class="step-three">
                    <div class="card">
                        <div class="card-header font-bold">Step 3: Verification</div>
                        <div class="p-4 border rounded">
                            <h2 class="text-lg font-semibold">Society Details:</h2>
                            <p><strong>Society Name:</strong> {{ $society_name }}</p>
                            <p><strong>Total Flats:</strong> {{ $total_flats }}</p>
                            <p><strong>Address:</strong> 
                                @if($address_1){{ $address_1 }},@endif
                                @if($address_2){{ $address_2 }},@endif
                                @if($city){{ $city }},@endif
                                @if($state){{ $state }}@endif
                                @if($pincode) - {{ $pincode }}@endif
                            </p>
                        
                        </div>
                        <div class="card-body">
                            <div class="overflow-x-auto w-full">
                                <table class="min-w-full text-start text-sm font-light text-surface dark:text-white">
                                    <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
                                        <tr>
                                            <th scope="col" class="px-4 py-3">Building</th>
                                            @if($owner1_name)<th scope="col" class="px-4 py-3">Owner 1 Details</th>@endif
                                            @if($owner2_name)<th scope="col" class="px-4 py-3">Owner 2 Details</th>@endif
                                            @if($owner3_name)<th scope="col" class="px-4 py-3">Owner 3 Details</th>@endif
                                            @if($agreementCopy)<th scope="col" class="px-4 py-3">Xerox Copy Of Agreement</th>@endif
                                            @if($memberShipForm)<th scope="col" class="px-4 py-3">MemberShip Form</th>@endif
                                            @if($allotmentLetter)<th scope="col" class="px-4 py-3">Parking Allotment Letter</th>@endif
                                            @if($possessionLetter)<th scope="col" class="px-4 py-3">Possession Letter</th>@endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-neutral-200 dark:border-white/10">
                                            <td class="whitespace-nowrap px-4 py-3">{{ $building_name }} -{{ $apartment_number }}</td>
                                            @if($owner1_name)
                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ $owner1_name }}
                                                <br />
                                                @if($owner1_mobile)<i class="fa-solid fa-phone"></i> {{ $owner1_mobile }}@endif
                                                <br /> 
                                                @if($owner1_email)<i class="fas fa-envelope"></i> {{ $owner1_email }}@endif
                                            </td>
                                            @endif
                                            @if($owner2_name)
                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ $owner2_name }}
                                                <br />
                                                @if($owner2_mobile)<i class="fa-solid fa-phone"></i> {{ $owner2_mobile }}@endif
                                                <br /> 
                                                @if($owner2_email)<i class="fas fa-envelope"></i> {{ $owner2_email }}@endif
                                            </td>
                                            @endif
                                            @if($owner3_name)
                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ $owner3_name }}
                                                <br />
                                                @if($owner3_mobile)<i class="fa-solid fa-phone"></i> {{ $owner3_mobile }}@endif
                                                <br /> 
                                                @if($owner3_email)<i class="fas fa-envelope"></i> {{ $owner3_email }}@endif
                                            </td>
                                            @endif
                                            @if($agreementCopy)
                                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                                <a href="{{ asset('storage/society_docs/'.$agreementCopy) }}" target="_blank">
                                                    {{-- <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"> --}}
                                                    <i class="fa-solid fa-download"></i>
                                                </a></td>
                                            @endif
                                            @if($memberShipForm)
                                            <td class="whitespace-nowrap px-4 py-3 text-center"><a href="{{ asset('storage/society_docs/'.$memberShipForm) }}" target="_blank">
                                                {{-- <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"> --}}
                                                <i class="fa-solid fa-download"></i>
                                            </a></td>
                                            @endif
                                            @if($memberShipForm)
                                            <td class="whitespace-nowrap px-4 py-3 text-center"><a href="{{ asset('storage/society_docs/'.$allotmentLetter) }}" target="_blank">
                                                {{-- <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"> --}}
                                                <i class="fa-solid fa-download"></i>
                                            </a></td>
                                            @endif
                                            @if($possessionLetter)
                                            <td class="whitespace-nowrap px-4 py-3 text-center"><a href="{{ asset('storage/society_docs/'.$possessionLetter) }}" target="_blank">
                                                {{-- <img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"> --}}
                                                <i class="fa-solid fa-download"></i>
                                            </a></td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-end mt-4">
                                <flux:button variant="filled" class="mr-2" type="button" wire:click="prevStep">{{ __('Back') }}</flux:button>
                                <flux:button variant="primary" type="button" wire:click="done">{{ __('Done') }}</flux:button>
                            </div>
                        </div>
                        
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>