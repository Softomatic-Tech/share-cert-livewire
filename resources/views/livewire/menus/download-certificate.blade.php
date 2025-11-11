<div class="p-2 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6 border border-gray-200">

        <h1 class="text-xl font-bold text-center text-gray-800 mb-4"><u>Certificate Preview</u></h1>
        <h2 class="font-bold">Certificate Status: {{ ucwords($details->certificate_status) }}</h2>
        @if($details->certificate_remark)
        <span class="font-bold">Certificate Remark: </span><span class="text-red-500"><i>"{{ $details->certificate_remark }}"</i></span>
        @endif
        @if(auth()->user()->role->role_id === 3)
        <span class="text-lg">Disclaimer:</span><span> This document is a provisional copy and holds no legal validity. The original certificate will be digitally issued upon approval.</span>
        @endif
        {{-- PDF Preview --}}
        @if($pdfUrl)
            {{-- <iframe> is used to embed the PDF preview in browser --}}
            <iframe src="{{ $pdfUrl }}" class="w-full h-[70vh] border rounded-md"></iframe>
        @else
            <p class="text-center text-gray-500">Generating certificate preview...</p>
        @endif
        @if(auth()->user()->role->role_id === 3)
        {{-- Action Buttons --}}
        @if($details->certificate_status !== 'approved')
        <div class="mt-6 flex justify-center space-x-4">
            {{-- Approve Button --}}
            <button 
                wire:click="approveCertificate"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                Everything is OK
            </button>

            {{-- Needs Changes Button --}}
            <button
                wire:click="showRemarksBox"
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2 rounded-md shadow">
                Need Changes
            </button>
        </div>
        @endif
        {{-- Remark Input Box (appears only when needed) --}}
        @if($showRemarkBox)
            <div id="remark-section"
                x-data
                x-init="$el.scrollIntoView({behavior: 'smooth', block: 'center'}); $el.querySelector('textarea')?.focus()"
                class="mt-6 border-t pt-4">
                
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">
                    Remarks for changes
                </label>
                <textarea wire:model="certificate_remark" 
                    placeholder="Enter remarks for changes..."
                    class="w-full border border-gray-300 rounded-md p-2 focus:ring focus:ring-yellow-300 focus:outline-none"></textarea>

                <div class="mt-3 text-right">
                    <button
                        wire:click="submitRemarks"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                        Submit Remarks
                    </button>
                </div>
            </div>
        @endif
        <div class="mt-2">
        <livewire:menus.alerts />
        </div>
        @endif
    </div>
</div>
