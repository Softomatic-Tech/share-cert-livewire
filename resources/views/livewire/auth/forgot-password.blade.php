 <div class="flex flex-col gap-6">
    <x-auth-header :title="__('Forgot password')" :description="__('Enter your mobile to change password')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    {{-- <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email Address')"
            type="email"
            required
            autofocus
            placeholder="email@example.com"
        />

        <flux:button variant="primary" type="submit" class="w-full">{{ __('Email password reset link') }}</flux:button>
    </form> --}}

    <div>
    <!-- Step 1: Enter Mobile -->
    @if($step === 1)
        <form wire:submit.prevent="findUser" class="space-y-4">
            <flux:input type="text" wire:model="mobile" placeholder="Enter Mobile No"/>
            @error('mobile') <p class="text-red-500">{{ $message }}</p> @enderror
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Next') }}</flux:button>
        </form>
    @endif

    <!-- Step 2: Security Question -->
    @if($step === 2)
        <p class="font-semibold mb-2">{{ $securityQuestion }}</p>
        <form wire:submit.prevent="verifyAnswer" class="space-y-4">
            <flux:input type="text" wire:model="answer" :placeholder="__('Your Answer')"/>
            @error('answer') <p class="text-red-500">{{ $message }}</p> @enderror
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Verify') }}</flux:button>
        </form>
    @endif

    <!-- Step 3: Reset Password -->
    @if($step === 3)
        <form wire:submit.prevent="resetPassword" class="space-y-4">
            <flux:input type="password" wire:model="password" :placeholder="__('Password')"/>
            <flux:input type="password" wire:model="password_confirmation" :placeholder="__('Confirm Password')"/>
            @error('password') <p class="text-red-500">{{ $message }}</p> @enderror
            <flux:button variant="primary" type="submit" class="w-full">Reset Password</flux:button>
        </form>
    @endif
</div>


    <div class="space-x-1 text-center text-sm text-zinc-400">
        {{ __('Or, return to') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('log in') }}</flux:link>
    </div>
</div>
