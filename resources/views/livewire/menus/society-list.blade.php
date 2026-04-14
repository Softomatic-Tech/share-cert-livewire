<div class="w-full">
    <div class="relative mb-2 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div>
                <flux:breadcrumbs>
                    <flux:breadcrumbs.item href="#">Super Admin</flux:breadcrumbs.item>
                    <flux:breadcrumbs.item href="#">Society Details</flux:breadcrumbs.item>
                </flux:breadcrumbs>
            </div>
            <div>
                <div class="flex justify-between items-center mt-2">
                    <flux:input type="text" placeholder="Search Society..." size="md" wire:model.live="search">
                        <x-slot name="iconTrailing">
                            @if ($search)
                                <flux:button size="sm" variant="subtle" icon="x-mark" class="-mr-1"
                                    wire:click="$set('search', '')" />
                            @endif
                        </x-slot>
                    </flux:input>
                </div>
            </div>
        </div>
    </div>
    <flux:separator variant="subtle" />

    <div class="group block max-h-150 overflow-y-auto mt-4" aria-disabled="false" data-dui-accordion-container
        data-dui-accordion-mode="exclusive">
        @foreach ($societies as $society)
            <div class="flex items-center justify-between w-full border-b text-left font-medium dark:text-white text-stone-800 cursor-pointer transition-colors duration-200 hover:bg-gray-200"
                data-dui-accordion-toggle data-dui-accordion-target="#basicAccordion{{ $society->id }}"
                aria-expanded="false" wire:click="toggleAccordion({{ $society->id }})">
                <div class="p-4 text-left align-top w-full">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h3 class="font-bold text-lg text-blue-900 dark:text-blue-300 mr-2">{{ $society->society_name }}
                        </h3>
                        <div class="inline-flex items-center rounded-md bg-white border border-gray-300 px-2 py-0.5 text-xs font-mono text-gray-700 shadow-sm"
                            title="Registration Number">
                            <flux:icon.identification variant="outline" class="size-3 mr-1 text-gray-400 mt-0.5" />
                            {{ $society->registration_no ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <flux:badge color="amber" size="sm">Total flats: {{ $society->total_flats }}</flux:badge>

                        <span
                            class="inline-flex items-center gap-1 rounded-sm px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ strtolower($society->is_list_of_signed_member_available) === 'yes' ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-600' }}">
                            <flux:icon.users variant="mini" class="size-3" />
                            Signed Mbrs: {{ $society->is_list_of_signed_member_available ?? 'No' }}
                        </span>

                        <span
                            class="inline-flex items-center gap-1 rounded-sm px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ strtolower($society->is_byelaws_available) === 'yes' ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-600' }}">
                            <flux:icon.document-text variant="mini" class="size-3" />
                            Byelaws: {{ $society->is_byelaws_available ?? 'No' }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 flex items-start gap-1 mt-1">
                        <flux:icon.map-pin variant="outline" class="size-4 shrink-0 text-gray-400 mt-0.5" />
                        <span>
                            @if ($society->address_1)
                                {{ $society->address_1 }},
                            @endif
                            @if ($society->address_2)
                                {{ $society->address_2 }},
                            @endif
                            @if ($society->city?->name)
                                {{ $society->city->name }},
                            @endif
                            @if ($society->state?->name)
                                {{ $society->state->name }}
                            @endif
                            @if ($society->pincode)
                                - {{ $society->pincode }}
                            @endif
                        </span>
                    </p>
                </div>
            </div>

            <div id="basicAccordion{{ $society->id }}"
                class="{{ $openAccordionId === $society->id ? 'block' : 'hidden' }} overflow-hidden transition-all duration-300 border-b border-stone-200 dark:border-stone-700">
                @if ($society->details->isEmpty())
                    <div
                        class="p-8 flex flex-col items-center justify-center text-gray-500 bg-gray-50 dark:bg-gray-800/50">
                        <flux:icon.building-office-2 variant="outline" class="size-12 mb-3 opacity-50" />
                        <p class="text-lg font-medium text-gray-600 dark:text-gray-300">No details available</p>
                        <p class="text-sm mt-1">There are no apartment entries uploaded for this society yet.</p>
                    </div>
                @else
                    <div class="flex flex-col overflow-x-auto">
                        <div class="max-h-96 overflow-y-auto">
                            <table class="min-w-full text-start text-sm font-light text-surface dark:text-white">
                                <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">#</th>
                                        <th scope="col" class="px-4 py-3">Building</th>
                                        <th scope="col" class="px-6 py-4">Certificate No</th>
                                        <th scope="col" class="px-6 py-4">No of Shares</th>
                                        <th scope="col" class="px-6 py-4">Share Value</th>
                                        <th scope="col" class="px-6 py-4">Did you purchase the apartment before
                                            the society was registered?</th>
                                        <th scope="col" class="px-6 py-4">Did you sign at the time of the
                                            society registration?</th>
                                        <th scope="col" class="px-6 py-4">Did the previous owner sign the
                                            registration documents?</th>
                                        <th scope="col" class="px-6 py-4">Has the flat transfer-related fee
                                            been paid to the Society?</th>
                                        <th scope="col" class="px-6 py-4">Have physical documents been
                                            submitted to the society?</th>
                                        <th scope="col" class="px-4 py-3">Owner 1 Details</th>
                                        <th scope="col" class="px-4 py-3">Owner 2 Details</th>
                                        <th scope="col" class="px-4 py-3">Owner 3 Details</th>
                                        <th scope="col" class="px-4 py-3">Xerox Copy Of Agreement</th>
                                        <th scope="col" class="px-4 py-3">MemberShip Form</th>
                                        <th scope="col" class="px-4 py-3">Parking Allotment Letter</th>
                                        <th scope="col" class="px-4 py-3">Possession Letter</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($society->details as $index => $detail)
                                        <tr class="border-b border-neutral-200 dark:border-white/10">
                                            <td class="whitespace-nowrap px-4 py-3">{{ $index + 1 }}</td>
                                            <td class="whitespace-nowrap px-4 py-3">{{ $detail->building_name }} -
                                                {{ $detail->apartment_number }}</td>
                                            <td class="whitespace-nowrap px-4 py-3">{{ $detail->certificate_no }}</td>
                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ $detail->society->no_of_shares }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3">{{ $detail->society->share_value }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ $detail->did_you_purchase_the_apartment_before_the_society_was_registered }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ $detail->did_you_sign_at_the_time_of_the_society_registration }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ $detail->did_the_previous_owner_sign_the_registration_documents }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ $detail->has_the_flat_transfer_related_fee_been_paid_to_the_society }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ $detail->have_physical_documents_been_submitted_to_the_society }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3">
                                                @if ($detail->owner1_name)
                                                    {{ $detail->owner1_name }}
                                                    <br />
                                                    @if ($detail->owner1_mobile)
                                                        <i class="fa-solid fa-phone"></i> {{ $detail->owner1_mobile }}
                                                    @endif
                                                    <br />
                                                    @if ($detail->owner1_email)
                                                        <i class="fas fa-envelope"></i> {{ $detail->owner1_email }}
                                                    @endif
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-4 py-3">
                                                @if ($detail->owner2_name)
                                                    {{ $detail->owner2_name }}
                                                    <br />
                                                    @if ($detail->owner2_mobile)
                                                        <i class="fa-solid fa-phone"></i> {{ $detail->owner2_mobile }}
                                                    @endif
                                                    <br />
                                                    @if ($detail->owner2_email)
                                                        <i class="fas fa-envelope"></i> {{ $detail->owner2_email }}
                                                    @endif
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-4 py-3">
                                                @if ($detail->owner3_name)
                                                    {{ $detail->owner3_name }}
                                                    <br />
                                                    @if ($detail->owner3_mobile)
                                                        <i class="fa-solid fa-phone"></i> {{ $detail->owner3_mobile }}
                                                    @endif
                                                    <br />
                                                    @if ($detail->owner3_email)
                                                        <i class="fas fa-envelope"></i> {{ $detail->owner3_email }}
                                                    @endif
                                                @endif
                                            </td>


                                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                                @if ($detail->agreementCopy)
                                                    <a href="{{ asset('storage/society_docs/' . $detail->agreementCopy) }}"
                                                        target="_blank">
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                                @if ($detail->memberShipForm)
                                                    <a href="{{ asset('storage/society_docs/' . $detail->memberShipForm) }}"
                                                        target="_blank">
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                                @if ($detail->allotmentLetter)
                                                    <a href="{{ asset('storage/society_docs/' . $detail->allotmentLetter) }}"
                                                        target="_blank">
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                                @if ($detail->possessionLetter)
                                                    <a href="{{ asset('storage/society_docs/' . $detail->possessionLetter) }}"
                                                        target="_blank">
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
