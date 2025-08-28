<div>
    @if(!empty($societyDetails) && $societyDetails->count() > 0)
    <div class="flex flex-col overflow-x-auto">
        <div class="max-h-96 overflow-y-auto"> 
            <table class="min-w-full text-start text-sm font-light text-surface dark:text-white">
                <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
                    <tr>
                        <th scope="col" class="px-4 py-3">#</th>
                        <th scope="col" class="px-4 py-3">Building</th>
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
                @foreach ($societyDetails as $index=>$detail)
                    <tr class="border-b border-neutral-200 dark:border-white/10">
                        <td class="whitespace-nowrap px-4 py-3">{{ $index + 1 }}</td>
                        <td class="whitespace-nowrap px-4 py-3">{{ $detail->building_name }} - {{ $detail->apartment_number }}</td>
                        <td class="whitespace-nowrap px-4 py-3">
                        @if($detail->owner1_name)
                            {{ $detail->owner1_name }}
                            <br />@if($detail->owner1_mobile)<i class="fa-solid fa-phone"></i> {{ $detail->owner1_mobile }} @endif
                            <br /> @if($detail->owner1_email)<i class="fas fa-envelope"></i> {{ $detail->owner1_email }}@endif
                        @endif
                        </td>
                        
                        <td class="whitespace-nowrap px-4 py-3">
                        @if($detail->owner2_name)
                            {{ $detail->owner2_name }} 
                            <br />@if($detail->owner2_mobile)<i class="fa-solid fa-phone"></i> {{ $detail->owner2_mobile }} @endif
                            <br />@if($detail->owner2_email)<i class="fas fa-envelope"></i> {{ $detail->owner2_email }}@endif
                            @endif
                        </td>
                        
                        <td class="whitespace-nowrap px-4 py-3">
                        @if($detail->owner3_name)
                            {{ $detail->owner3_name }} 
                            <br />@if($detail->owner3_mobile)<i class="fa-solid fa-phone"></i> {{ $detail->owner3_mobile }} @endif
                            <br />@if($detail->owner3_email)<i class="fas fa-envelope"></i> {{ $detail->owner3_email }}@endif
                        @endif
                        </td>
                        
                        
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                        @if($detail->agreementCopy)
                            <a href="{{ asset('storage/society_docs/'.$detail->agreementCopy) }}" target="_blank">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        @endif
                        </td>
                        
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                        @if($detail->memberShipForm)
                            <a href="{{ asset('storage/society_docs/'.$detail->memberShipForm) }}" target="_blank">
                            <i class="fa-solid fa-download"></i>
                            </a>
                        @endif
                        </td>
                        
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                        @if($detail->allotmentLetter)
                            <a href="{{ asset('storage/society_docs/'.$detail->allotmentLetter) }}" target="_blank">
                            <i class="fa-solid fa-download"></i>
                        </a>
                        @endif
                        </td>
                        
                        <td class="whitespace-nowrap px-4 py-3 text-center">
                        @if($detail->possessionLetter)
                            <a href="{{ asset('storage/society_docs/'.$detail->possessionLetter) }}" target="_blank">
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
    @else
    <p class="text-center text-gray-500">No records found.</p>
    @endif
</div>
