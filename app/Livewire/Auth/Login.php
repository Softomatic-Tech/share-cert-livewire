<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    // #[Validate('required|string|email')]
    // public string $email = '';
    #[Validate('required|string')]
    public string $authIdentifier = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public string $email = '';
    /**
     * Handle an incoming authentication request.
     */
    public function updatedEmail($value)
    {
        $this->authIdentifier = $value;
    }

    public function updatedAuthIdentifier($value)
    {
        $this->email = $value;
    }

    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        //Determine login type (email or phone)
        $login_type = filter_var($this->authIdentifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if ($login_type === 'phone') {
            $mobile = $this->authIdentifier;

            $existsInSociety = \App\Models\SocietyDetail::where('owner1_mobile', $mobile)
                ->orWhere('owner2_mobile', $mobile)
                ->orWhere('owner3_mobile', $mobile)
                ->exists();

            if (! $existsInSociety) {
                throw ValidationException::withMessages([
                    'authIdentifier' => 'This mobile number is not registered as an owner in any society.',
                ]);
            }
        }
        if (! Auth::attempt([$login_type => $this->authIdentifier, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'authIdentifier' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Get the authenticated user
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'authIdentifier' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->authIdentifier).'|'.request()->ip());
    }
}
