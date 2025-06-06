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

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        //Determine login type (email,username or phone)
        $login_type='username';
        if(filter_var($this->authIdentifier,FILTER_VALIDATE_EMAIL)){
            $login_type='email';
        }elseif(preg_match('/^\d{10}$/',$this->authIdentifier)){
            $login_type='phone';
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
        $user = Auth::user();
        // Redirect based on user role
        match ($user->role->role) {
            'Super Admin' => $this->redirectIntended(route('superadmin.dashboard')),
            'Admin' => $this->redirectIntended(route('admin.dashboard')),
            'Society User' => $this->redirectIntended(route('user.dashboard')),
        };
        // $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
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
