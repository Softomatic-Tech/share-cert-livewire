<div>
    @if($success)
    <flux:callout variant="success" icon="check-circle" inline x-data="{ visible: true }" x-show="visible" x-init="setTimeout(() => visible = false, 3000)">
        <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start">{{ $success }}</flux:callout.heading>
        <x-slot name="controls">
            <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
        </x-slot>
    </flux:callout>
    @endif
    @if($error)
    <flux:callout variant="danger" icon="x-circle" inline x-data="{ visible: true }" x-show="visible" x-init="setTimeout(() => visible = false, 3000)">
        <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start">{{ $error }}</flux:callout.heading>
        <x-slot name="controls">
            <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
        </x-slot>
    </flux:callout>
    @endif
</div>
