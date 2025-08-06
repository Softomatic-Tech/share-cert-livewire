<section class="w-full">
    <div class="relative w-full">
        <flux:heading size="xl" level="1">{{ __('Create Society') }}</flux:heading>
        <flux:separator variant="subtle" />

        <div class="rounded-lg shadow-lg p-6">
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
            <div class="py-4">
                <livewire:menus.alerts />
            </div>
            <!-- Verify Details -->
            @if($currentStep == 1)
                <div class="step-one">
                    <div class="card">
                        <div class="card-header">Step 1: Verify Details</div>
                        <div class="card-body">
                            <form wire:submit.prevent="nextStep">
                                    <div class="flex space-x-4 my-2">
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Society Name')" wire:model="society_name" />
                                    </div>
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Total Flats')" wire:model="total_flats" />
                                    </div>
                                    </div>
                                    <div class="flex space-x-4 my-2">
                                    <div class="flex-1">
                                        <flux:input type="text"  :label="__('Address Line 1')" wire:model="address_1" />
                                    </div>
                                    <div class="flex-1">
                                        <flux:input type="text"  :label="__('Address Line 2')" wire:model="address_2" />
                                    </div>
                                    </div>
                                    <div class="flex space-x-4 my-2">
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Pincode')" wire:model="pincode" />
                                    </div>
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('City')" wire:model="city" />
                                    </div>
                                    </div>
                                    <div class="flex space-x-4 my-2">
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('State')" wire:model="state" />
                                    </div>
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Building Name')" wire:model="building_name" />
                                    </div>
                                    </div>
                                    <div class="flex space-x-4 my-2">
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Apartment Number')" wire:model="apartment_number" />
                                    </div>
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Owner 1 Name')" wire:model="owner1_name" />
                                    </div>
                                    </div>
                                    <div class="flex space-x-4 my-2">
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Owner 1 Email')" wire:model="owner1_email" />
                                    </div>
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Owner 1 Mobile')" wire:model="owner1_mobile" />
                                    </div>
                                    </div>
                                    <div class="flex space-x-4 my-2">
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Owner 2 Name')" wire:model="owner2_name" />
                                    </div>
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Owner 2 Email')" wire:model="owner1_email" />
                                    </div>
                                    </div>
                                    <div class="flex space-x-4 my-2">
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Owner 2 Mobile')" class="form-control" wire:model="owner1_mobile" />
                                    </div>
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Owner 3 Name')" class="form-control" wire:model="owner3_name" />
                                    </div>
                                    </div>
                                    <div class="flex space-x-4 my-2">
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Owner 3 Email')" wire:model="owner3_email" />
                                    </div>
                                    <div class="flex-1">
                                        <flux:input type="text" :label="__('Owner 3 Mobile')" wire:model="owner3_mobile" />
                                    </div>
                                    </div>
                                    {{-- <div class="my-2">
                                        <flux:button variant="primary" type="submit">{{ __('Update') }}</flux:button>
                                    </div> --}}
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
                        <div class="card-header">Step 2: Upload Documents</div>
                            <div class="card-body">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-2">
                                    <!-- Form 1 -->
                                    <form wire:submit.prevent="uploadAgreementCopy" class="p-4 border rounded-md">
                                        <label class="block font-semibold mb-2">Xerox Copy Of Agreement</label>
                                        <div class="flex items-center gap-4">
                                            <flux:input type="file" wire:model="agreementCopy" />
                                            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="agreementCopy, uploadAgreementCopy">{{ __('Upload') }}</flux:button>
                                        </div>
                                        @error('agreementCopy') <span class="text-red-500">{{ $message }}</span> @enderror
                                        @if($agreementCopy)
                                        <div class="flex items-center gap-4 mt-2">
                                            <a href="{{ asset('storage/society_docs/'.$agreementCopy) }}" target="_blank" class="text-blue-600 underline">
                                                View Agreement Copy
                                            </a>
                                        </div>
                                        @endif
                                    </form>
                                    
                                    <!-- Form 2 -->
                                    <form wire:submit.prevent="uploadMemberShipForm" class="p-4 border rounded-md">
                                        <label class="block font-semibold mb-2">MemberShip Form</label>
                                        <div class="flex items-center gap-4">
                                        <flux:input type="file" wire:model="memberShipForm" />
                                        <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="memberShipForm, uploadmemberShipForm">{{ __('Upload') }}</flux:button>
                                        </div>
                                        @error('memberShipForm') <span class="text-red-500">{{ $message }}</span> @enderror
                                        @if($memberShipForm)
                                        <div class="flex items-center gap-4 mt-2">
                                            <a href="{{ asset('storage/society_docs/'.$memberShipForm) }}" target="_blank" class="text-blue-600 underline">
                                                View MemberShip Form
                                            </a>
                                        </div>
                                        @endif
                                    </form>

                                    <!-- Form 3 -->
                                    <form wire:submit.prevent="uploadAllotmentLetter" class="p-4 border rounded-md">
                                        <label class="block font-semibold mb-2">Parking Allotment Letter</label>
                                        <div class="flex items-center gap-4">
                                        <flux:input type="file" wire:model="allotmentLetter" />
                                        <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="allotmentLetter, uploadAllotmentLetter">{{ __('Upload') }}</flux:button>
                                        </div>
                                        @error('allotmentLetter') <span class="text-red-500">{{ $message }}</span> @enderror
                                        @if($allotmentLetter)
                                        <div class="flex items-center gap-4 mt-2">
                                            <a href="{{ asset('storage/society_docs/'.$allotmentLetter) }}" target="_blank" class="text-blue-600 underline">
                                                View Allotment Letter
                                            </a>
                                        </div>
                                        @endif
                                    </form>

                                    <!-- Form 4 -->
                                    <form wire:submit.prevent="uploadPossessionLetter" class="p-4 border rounded-md">
                                        <label class="block font-semibold mb-2">Possession Letter</label>
                                        <div class="flex items-center gap-4">
                                        <flux:input type="file" wire:model="possessionLetter" />
                                        <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="possessionLetter, uploadPossessionLetter">{{ __('Upload') }}</flux:button>
                                        </div>
                                        @error('possessionLetter') <span class="text-red-500">{{ $message }}</span> @enderror
                                        @if($possessionLetter)
                                        <div class="flex items-center gap-4 mt-2">
                                            <a href="{{ asset('storage/society_docs/'.$possessionLetter) }}" target="_blank" class="text-blue-600 underline">
                                                View Possession Letter
                                            </a>
                                        </div>
                                        @endif
                                    </form>
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
                        <div class="card-header">Step 3: Verification</div>
                        <div class="p-4 border rounded">
                            <h2 class="text-lg font-semibold">Society Details</h2>
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
                            <div class="flex flex-col">
                                <div class="sm:-mx-6 lg:-mx-8">
                                    <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                                        <table class="min-w-full text-start text-sm font-light text-surface dark:text-white">
                                            <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
                                                <tr>
                                                    <th scope="col" class="px-6 py-4">Building</th>
                                                    @if($owner1_name)<th scope="col" class="px-6 py-4">Owner 1 Details</th>@endif
                                                    @if($owner2_name)<th scope="col" class="px-6 py-4">Owner 2 Details</th>@endif
                                                    @if($owner3_name)<th scope="col" class="px-6 py-4">Owner 3 Details</th>@endif
                                                    @if($agreementCopy)<th scope="col" class="px-6 py-4">Xerox Copy Of Agreement</th>@endif
                                                    @if($memberShipForm)<th scope="col" class="px-6 py-4">MemberShip Form</th>@endif
                                                    @if($allotmentLetter)<th scope="col" class="px-6 py-4">Parking Allotment Letter</th>@endif
                                                    @if($possessionLetter)<th scope="col" class="px-6 py-4">Possession Letter</th>@endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="border-b border-neutral-200 dark:border-white/10">
                                                    <td class="whitespace-nowrap px-6 py-4">{{ $building_name }} -{{ $apartment_number }}</td>
                                                    @if($owner1_name)
                                                    <td class="whitespace-nowrap px-6 py-4">
                                                        {{ $owner1_name }}
                                                        <br />
                                                        @if($owner1_mobile)<i class="fa-solid fa-phone"></i> {{ $owner1_mobile }}@endif
                                                        <br /> 
                                                        @if($owner1_email)<i class="fas fa-envelope"></i> {{ $owner1_email }}@endif
                                                    </td>
                                                    @endif
                                                    @if($owner2_name)
                                                    <td class="whitespace-nowrap px-6 py-4">
                                                        {{ $owner2_name }}
                                                        <br />
                                                        @if($owner2_mobile)<i class="fa-solid fa-phone"></i> {{ $owner2_mobile }}@endif
                                                        <br /> 
                                                        @if($owner2_email)<i class="fas fa-envelope"></i> {{ $owner2_email }}@endif
                                                    </td>
                                                    @endif
                                                    @if($owner3_name)
                                                    <td class="whitespace-nowrap px-6 py-4">
                                                        {{ $owner3_name }}
                                                        <br />
                                                        @if($owner3_mobile)<i class="fa-solid fa-phone"></i> {{ $owner3_mobile }}@endif
                                                        <br /> 
                                                        @if($owner3_email)<i class="fas fa-envelope"></i> {{ $owner3_email }}@endif
                                                    </td>
                                                    @endif
                                                    @if($agreementCopy)
                                                    <td class="whitespace-nowrap px-6 py-4">
                                                        <a href="{{ asset('storage/society_docs/'.$agreementCopy) }}" target="_blank" class="text-blue-600 underline"><img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></a></td>
                                                    @endif
                                                    @if($memberShipForm)
                                                    <td class="whitespace-nowrap px-6 py-4"><a href="{{ asset('storage/society_docs/'.$memberShipForm) }}" target="_blank" class="text-blue-600 underline"><img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></a></td>
                                                    @endif
                                                    @if($memberShipForm)
                                                    <td class="whitespace-nowrap px-6 py-4"><a href="{{ asset('storage/society_docs/'.$allotmentLetter) }}" target="_blank" class="text-blue-600 underline"><img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></a></td>
                                                    @endif
                                                    @if($possessionLetter)
                                                    <td class="whitespace-nowrap px-6 py-4"><a href="{{ asset('storage/society_docs/'.$possessionLetter) }}" target="_blank" class="text-blue-600 underline"><img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></a></td>
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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