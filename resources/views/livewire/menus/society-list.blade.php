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
                      <flux:button size="sm" variant="subtle" icon="x-mark" class="-mr-1" wire:click="$set('search', '')" />
                      @endif
                  </x-slot>
              </flux:input> 
          </div>
      </div>
    </div>
  </div>
  <flux:separator variant="subtle" />

  <div class="group block max-h-150 overflow-y-auto mt-4" aria-disabled="false" data-dui-accordion-container data-dui-accordion-mode="exclusive">
    @foreach ($societies as $society)
      <div 
        class="flex items-center justify-between w-full border-b text-left font-medium dark:text-white text-stone-800 cursor-pointer transition-colors duration-200 hover:bg-gray-200"
        data-dui-accordion-toggle
        data-dui-accordion-target="#basicAccordion{{ $society->id }}"
        aria-expanded="false" wire:click="toggleAccordion({{ $society->id }})">
          <div class="p-4 text-left align-top">
              <h3 class="font-bold text-lg">{{ $society->society_name }}  
                <flux:badge color="amber" class="ml-2">Total flats : {{ $society->total_flats }}</flux:badge></h3>
              <p>
              @if($society->address_1){{ $society->address_1 }},@endif
              @if($society->address_2){{ $society->address_2 }},@endif
              @if($society->city->name){{ $society->city->name }},@endif
              @if($society->state->name){{ $society->state->name }}@endif
              @if($society->pincode) - {{ $society->pincode }}@endif</p>
          </div>
      </div>

      <div id="basicAccordion{{ $society->id }}" class="{{ $openAccordionId === $society->id ? 'block' : 'hidden' }} overflow-hidden transition-all duration-300 border-b border-stone-200 dark:border-stone-700">
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
                @foreach ($society->details as $index => $detail)
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
      </div>
    @endforeach
  </div>
</div>
