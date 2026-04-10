<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use App\Models\SocietyDetail;
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

        $login_type = filter_var($this->authIdentifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if ($login_type === 'phone') {
            $mobile = $this->authIdentifier;
            $user = User::with('role')->where('phone', $mobile)->first();

            if ($user && in_array($user->role->role, ['Super Admin', 'Admin'], true)) {
                $this->attemptWebLogin($login_type);
                return;
            }

            if ($this->attemptSocietyOwnerLogin($mobile)) {
                return;
            }
        }

        $this->attemptWebLogin($login_type);
    }

    protected function attemptWebLogin(string $login_type): void
    {
        Log::info('Attempting login for identifier: ' . $this->authIdentifier . ' using type: ' . $login_type);
        if (! Auth::attempt([$login_type => $this->authIdentifier, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'authIdentifier' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();
        Log::info('Login successful for identifier: ' . $this->authIdentifier);
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    protected function attemptSocietyOwnerLogin(string $mobile): bool
    {
        Log::info('Attempting society owner login for mobile: ' . $mobile);

        // First, check if a User record already exists for this mobile as Society User
        $existingUser = User::where('phone', $mobile)->first();
        if ($existingUser && $existingUser->role->role === 'Society User') {
            Log::info('User record exists for mobile: ' . $mobile . ', falling back to users table auth');
            return false; // Let attemptWebLogin handle it
        }

        $societyDetail = SocietyDetail::where('owner1_mobile', $mobile)
            ->orWhere('owner2_mobile', $mobile)
            ->orWhere('owner3_mobile', $mobile)
            ->first();

        if (! $societyDetail) {
            return false;
        }

        $expectedPassword = null;
        if ($societyDetail && $societyDetail->status) {
            $statusData = json_decode($societyDetail->status, true);
            if (is_string($statusData)) {
                $statusData = json_decode($statusData, true);
            }

            $expectedPassword = $statusData['password'] ?? null;
        }
        if (! $expectedPassword || ! hash_equals((string) $expectedPassword, (string) $this->password)) {
            return false;
        }

        if ($societyDetail->owner1_mobile === $mobile) {
            $name = $societyDetail->owner1_name;
            $email = $societyDetail->owner1_email;
        } elseif ($societyDetail->owner2_mobile === $mobile) {
            $name = $societyDetail->owner2_name;
            $email = $societyDetail->owner2_email;
        } else {
            $name = $societyDetail->owner3_name;
            $email = $societyDetail->owner3_email;
        }
        // Fix: convert empty email to NULL
        $email = !empty($email) ? $email : null;

        // Get role once (optimization)
        $roleId = Role::where('role', 'Society User')->value('id');

        $user = User::updateOrCreate(
            ['phone' => $mobile],
            [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($this->password),
                'role_id' => $roleId,
            ]
        );

        if ($user->role_id !== $roleId) {
            $user->role_id = $roleId;
            $user->save();
        }
        if (! Hash::check($this->password, $user->password)) {
            $user->password = Hash::make($this->password);
            $user->save();
        }

        Auth::login($user, $this->remember);
        RateLimiter::clear($this->throttleKey());
        Session::regenerate();
        $this->redirectIntended(default: route('user.dashboard', absolute: false), navigate: true);
        return true;
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
        return Str::transliterate(Str::lower($this->authIdentifier) . '|' . request()->ip());
    }
}
