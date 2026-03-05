<x-layouts.app.header :title="$title ?? null">
    {{-- <flux:main> --}}
        <div class="m-2 p-2">
        {{ $slot }}
        </div>
    {{-- </flux:main> --}}
</x-layouts.app.header>
