
<div class="w-full">
  <div class="flex justify-between items-center">
      <h1 class="text-xl font-bold">Society List:</h1>
      <div class="w-100">
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

  <div class="group block max-h-150 overflow-y-auto mt-4" aria-disabled="false" data-dui-accordion-container data-dui-accordion-mode="exclusive">
    @foreach ($societies as $society)
    <div 
      class="flex items-center justify-between w-full border-b  text-left font-medium dark:text-white text-stone-800 cursor-pointer transition-colors duration-200 hover:bg-amber-100"
      data-dui-accordion-toggle
      data-dui-accordion-target="#basicAccordion{{ $society->id }}"
      aria-expanded="false" wire:click="toggleAccordion({{ $society->id }})">
        <div class="grid grid-cols-2 gap-4 py-4">
            <div>
                <h3 class="font-bold text-lg">{{ $society->society_name }}</h3>
                <h3>Total flats : {{ $society->total_flats }}</h3>
            </div>

            <div>
                <strong>Address:</strong> 
                    @if($society->address_1){{ $society->address_1 }},@endif
                    @if($society->address_2){{ $society->address_2 }},@endif
                    @if($society->city){{ $society->city }},@endif
                    @if($society->state){{ $society->state }}@endif
                    @if($society->pincode) - {{ $society->pincode }}@endif
            </div>
        </div>
    </div>

    <div id="basicAccordion{{ $society->id }}" class="{{ $openAccordionId === $society->id ? 'block' : 'hidden' }} overflow-hidden transition-all duration-300 border-b border-stone-200 dark:border-stone-700">
      <div class="flex flex-col overflow-x-auto">
        <div class="sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
            <div class="overflow-x-auto">
              @if ($society->details->isNotEmpty())
              <table
                class="min-w-full text-start text-sm font-light text-surface dark:text-white">
                <thead class="border-b border-neutral-200 font-medium dark:border-white/10">
                <tr>
                    <th scope="col" class="px-6 py-4">#</th>
                    <th scope="col" class="px-6 py-4">Building Name</th>
                    <th scope="col" class="px-6 py-4">Apartment No</th>
                    <th scope="col" class="px-6 py-4">Owner1 Details</th>
                    <th scope="col" class="px-6 py-4">Owner2 Details</th>
                    <th scope="col" class="px-6 py-4">Owner3 Details</th>
                    <th scope="col" class="px-6 py-4">Agreement Copy</th>
                    <th scope="col" class="px-6 py-4">MemberShip Form</th>
                    <th scope="col" class="px-6 py-4">Allotment Letter</th>
                    <th scope="col" class="px-6 py-4">Possession Letter</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($society->details as $index => $detail)
                    <tr class="border-b border-neutral-200 dark:border-white/10">
                        <td class="whitespace-nowrap px-6 py-4 font-medium">{{ $index + 1 }}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{ $detail->building_name }}</td>
                        <td class="whitespace-nowrap px-6 py-4">{{ $detail->apartment_number }}</td>
                        <td class="whitespace-nowrap px-6 py-4">
                          {{ $detail->owner1_name }} 
                          <br />@if($detail->owner1_mobile)<i class="fa-solid fa-phone"></i> {{ $detail->owner1_mobile }} @endif
                          <br /> @if($detail->owner1_email)<i class="fas fa-envelope"></i> {{ $detail->owner1_email }}@endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                          {{ $detail->owner2_name }} 
                          <br />@if($detail->owner2_mobile)<i class="fa-solid fa-phone"></i> {{ $detail->owner2_mobile }} @endif
                          <br />@if($detail->owner2_email)<i class="fas fa-envelope"></i> {{ $detail->owner2_email }}@endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                          {{ $detail->owner3_name }} 
                          <br />@if($detail->owner3_mobile)<i class="fa-solid fa-phone"></i> {{ $detail->owner3_mobile }} @endif
                          <br />@if($detail->owner3_email)<i class="fas fa-envelope"></i> {{ $detail->owner3_email }}@endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                          @if($detail->agreementCopy)
                            <a href="{{ asset('storage/' . $detail->agreementCopy) }}" target="_blank" class="text-blue-500 hover:underline"><img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></a>
                          @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                          @if($detail->memberShipForm)
                            <a href="{{ asset('storage/' . $detail->memberShipForm) }}" target="_blank" class="text-blue-500 hover:underline"><img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></a>
                          @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                          @if($detail->allotmentLetter)
                            <a href="{{ asset('storage/' . $detail->allotmentLetter) }}" target="_blank" class="text-blue-500 hover:underline"><img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></a>
                          @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                          @if($detail->possessionLetter)
                            <a href="{{ asset('storage/' . $detail->possessionLetter) }}" target="_blank" class="text-blue-500 hover:underline"><img src="{{ asset('images/document.svg') }}" alt="" class="w-6 h-6"></a>
                          @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
              @endif
            </div>
            <!-- Model for uploading documents -->
            @if($uploadDocId && $docType)
            <div>
              <button class="rounded-md bg-gray-950/5 px-2.5 py-1.5 text-sm font-semibold text-gray-900 hover:bg-gray-950/10">Open dialog</button>

              <div class="relative z-10" aria-labelledby="dialog-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                  <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                      <form wire:submit.prevent="uploadDocument">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                          <div class="sm:flex justify-center sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                              <h3 class="text-base font-semibold text-gray-900" id="dialog-title">Upload {{ ucwords(str_replace('_', ' ', $docType)) }}</h3>
                              <div class="mt-3">
                              <flux:input type="file" id="document" wire:model="document" />
                              @error('document') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                          <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">Upload</button>
                          <button type="button" class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto" wire:click="closeModal">Cancel</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @endif
            <!-- Model for uploading documents -->
          </div>
        </div>
      </div>
    </div>
   
      @endforeach
  </div>
</div>
