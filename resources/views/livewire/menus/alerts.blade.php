<div x-data="{ visible: false, message: '', msgType: '{{ $type }}' }"
    x-on:show-error.window="
        if (msgType === 'error' || msgType === null) {
            message = $event.detail.message;
            visible = true;
            setTimeout(() => visible = false, 5000);
        }
    "
    x-on:show-success.window="
        if (msgType === 'success' || msgType === null) {
            message = $event.detail.message;
            visible = true;
            setTimeout(() => visible = false, 5000);
        }
    ">
    <!-- Error Callout -->
    <template x-if="visible && msgType === 'error'">
        <flux:callout variant="danger" icon="x-circle" inline>
            <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start" x-text="message"></flux:callout.heading>
            <x-slot name="controls">
                <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
            </x-slot>
        </flux:callout>
    </template>

    <!-- Success Callout -->
    <template x-if="visible && msgType === 'success'">
        <flux:callout variant="success" icon="check-circle" inline>
            <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start" x-text="message">
            </flux:callout.heading>
            <x-slot name="controls">
                <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
            </x-slot>
        </flux:callout>
    </template>
</div>
