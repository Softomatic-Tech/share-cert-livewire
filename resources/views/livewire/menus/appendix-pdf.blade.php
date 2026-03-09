<x-layouts.app :title="__('Appendix Documents')">
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-2xl">
                Appendix Documents
            </h2>
            <p class="mt-3 max-w-xl mx-auto text-xl text-gray-500 sm:mt-4">
                View and download sample appendix PDF files.
            </p>
        </div>
        <div class="flex justify-start mt-2">
            <flux:button href="{{ route('admin.dashboard') }}" icon="chevron-left">Back to Dashboard</flux:button>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @php
                $appendices = [
                    ['name' => 'Appendix 2', 'subtitle' => 'Bye-Laws No. 19(a)', 'file' => 'appendix-2-bye-lawsno.19(a).pdf'],
                    ['name' => 'Appendix 3', 'subtitle' => 'Bye-Laws No. 19(a)(iv)', 'file' => 'appendix-3-bye-lawsno.19(a)(iv)).pdf'],
                    ['name' => 'Appendix 15', 'subtitle' => 'Bye-Laws No. 34', 'file' => 'appendix-15-bye-lawsno.34.pdf'],
                    ['name' => 'Appendix 16', 'subtitle' => 'Bye-Laws No. 34', 'file' => 'appendix-16-bye-lawsno.34.pdf'],
                    ['name' => 'Appendix 19', 'subtitle' => 'Bye-Laws No. 35', 'file' => 'appendix-19-bye-lawsno.35.pdf'],
                    ['name' => 'Appendix 20(1)', 'subtitle' => 'Bye-Laws No. 38(a)', 'file' => 'appendix-20(1)-bye-lawsno.38(a).pdf'],
                    ['name' => 'Appendix 20(2)', 'subtitle' => 'Bye-Laws No. 38-a', 'file' => 'appendix-20(2)-bye-lawsno.38-a.pdf'],
                    ['name' => 'Appendix 21', 'subtitle' => 'Bye-Laws No. 38(e)(i)', 'file' => 'appendix-21-bye-lawsno.38(e)(i).pdf',
                    'name' => 'Share Certificate Flow Chart', 'subtitle' => '', 'file' => 'share certificate flow chart.jpeg'],
                ];
            @endphp
            @foreach($appendices as $appendix)
            @php
                $extension = strtolower(pathinfo($appendix['file'], PATHINFO_EXTENSION));
                $isPdf = $extension === 'pdf';
            @endphp
            <a href="{{ asset('APPENDIX PDF/' . $appendix['file']) }}" target="_blank"
                class="group relative bg-white p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-500 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col items-center text-center">
                <div class="flex items-center justify-center h-16 w-16 rounded-md bg-red-50 text-red-600 mb-4 group-hover:bg-red-100 transition-colors duration-300">
                    @if($isPdf)
                        <!-- PDF Icon -->
                        {{-- <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg> --}}
                        <svg class="h-10 w-10 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5z"/>
                        <path d="M7 17h2.5a2.5 2.5 0 000-5H7v5zm1.5-1.5v-2h1a1 1 0 010 2h-1zM12 12h1.5a2.5 2.5 0 010 5H12v-5zm1.5 3.5a1 1 0 000-2H13v2h.5zM16 12h3v1.5h-1.5V14H19v1.5h-1.5V17H16v-5z"/>
                        </svg>
                    @else
                        <!-- Image Icon -->
                        <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M8 11l3 3 5-5"/>
                        </svg>
                    @endif
                </div>
                <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors duration-300">
                    {{ $appendix['name'] }}
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $appendix['subtitle'] }}
                </p>
                <div class="mt-4 flex items-center text-sm font-medium text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <span>{{ $isPdf ? 'Open PDF' : 'View Image' }}</span>
                    <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" 
                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" 
                        clip-rule="evenodd"/>
                    </svg>
                </div>
            </a>                
            @endforeach
        </div>
    </div>
</div>
</x-layouts.app>