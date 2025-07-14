
<div class="group block w-full" aria-disabled="false" data-dui-accordion-container data-dui-accordion-mode="exclusive">
  @foreach($societies as $society)
  <div 
    class="flex items-center justify-between w-full py-5 text-left font-medium dark:text-white text-stone-800 cursor-pointer"
    data-dui-accordion-toggle
    data-dui-accordion-target="#basicAccordion{{ $society->id }}"
    aria-expanded="false" wire:click="toggleAccordion({{ $society->id }})">
    <p class="font-semibold text-xl">{{ $society->society_name }}</p>
    <svg data-dui-accordion-icon width="1.5em" height="1.5em" strokeWidth="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" color="currentColor" class="h-4 w-4 rotate-180">
      <path d="M6 9L12 15L18 9" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round"></path>
    </svg>
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
                  <th scope="col" class="px-6 py-4">Owner 1 Details</th>
                  <th scope="col" class="px-6 py-4">Owner 2 Details</th>
                  <th scope="col" class="px-6 py-4">Owner 3 Details</th>
                  <th scope="col" class="px-6 py-4">Agreement Copy</th>
                  <th scope="col" class="px-6 py-4">MemberShip Form</th>
                  <th scope="col" class="px-6 py-4">Allotment Letter</th>
                  <th scope="col" class="px-6 py-4">Possession Letter</th>
                  <th scope="col" class="px-6 py-4">Need Clarification</th>
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
                          <a href="{{ asset('storage/' . $detail->agreementCopy) }}" target="_blank" class="text-blue-500 hover:underline">View</a>
                          <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">Verify</button>

                          <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full">Reject</button>
                        @else
                          <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" wire:click="openUploadModal({{ $detail->id }}, 'agreementCopy')">Upload</button>
                        @endif
                      </td>
                      <td class="whitespace-nowrap px-6 py-4">
                        @if($detail->memberShipForm)
                          <a href="{{ asset('storage/' . $detail->memberShipForm) }}" target="_blank" class="text-blue-500 hover:underline">View</a>
                          <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">Verify</button>

                          <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full">Reject</button>
                        @else
                          <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" wire:click="openUploadModal({{ $detail->id }}, 'memberShipForm')">Upload</button>
                        @endif
                      </td>
                      <td class="whitespace-nowrap px-6 py-4">
                        @if($detail->allotmentLetter)
                          <a href="{{ asset('storage/' . $detail->allotmentLetter) }}" target="_blank" class="text-blue-500 hover:underline">View</a>
                          <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">Verify</button>

                          <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full">Reject</button>
                        @else
                          <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" wire:click="openUploadModal({{ $detail->id }}, 'allotmentLetter')">Upload</button>
                        @endif
                      </td>
                      <td class="whitespace-nowrap px-6 py-4">
                        @if($detail->possessionLetter)
                          <a href="{{ asset('storage/' . $detail->possessionLetter) }}" target="_blank" class="text-blue-500 hover:underline">View</a>
                          <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">Verify</button>

                          <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full">Reject</button>
                        @else
                          <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" wire:click="openUploadModal({{ $detail->id }}, 'possessionLetter')">Upload</button>
                        @endif
                      </td>
                      <td></td>
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
