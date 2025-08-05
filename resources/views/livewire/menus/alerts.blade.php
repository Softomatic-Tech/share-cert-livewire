<div
    x-data="{ visible: false, message: '', type: 'success' }"
    x-on:show-error.window="
        message = $event.detail.message;
        type = 'error';
        visible = true;
        setTimeout(() => visible = false, 3000);
    "
    x-on:show-success.window="
        message = $event.detail.message;
        type = 'success';
        visible = true;
        setTimeout(() => visible = false, 3000);
    "
>
    <!-- Error Callout -->
    <template x-if="visible && type === 'error'">
        <flux:callout variant="danger" icon="x-circle" inline>
            <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start" x-text="message"></flux:callout.heading>
            <x-slot name="controls">
                <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
            </x-slot>
        </flux:callout>
    </template>

    <!-- Success Callout -->
    <template x-if="visible && type === 'success'">
        <flux:callout variant="success" icon="check-circle" inline>
            <flux:callout.heading class="flex gap-2 @max-md:flex-col items-start" x-text="message"></flux:callout.heading>
            <x-slot name="controls">
                <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
            </x-slot>
        </flux:callout>
    </template>
</div>

