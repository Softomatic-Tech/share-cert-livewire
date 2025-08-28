<x-layouts.app.header :title="$title ?? null">
    {{-- <flux:main> --}}
        <div class="my-2 mx-6">
        {{ $slot }}
        </div>
    {{-- </flux:main> --}}
</x-layouts.app.header>
