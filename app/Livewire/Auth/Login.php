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

            log::info('Login attempt for mobile: ' . $mobile . ', found user: ' . ($user ? 'yes' : 'no'));
            log::info('User role: ' . ($user && $user->role ? $user->role->role : 'N/A'));
            if ($user && in_array($user->role->role, ['Super Admin', 'Admin'], true)) {
                $this->attemptWebLogin($login_type);
                return;
            }

            if ($this->attemptSocietyOwnerLogin($mobile)) {
                return;
            }
        }
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
        log::info('Existing user check for mobile: ' . $mobile . ', found user: ' . ($existingUser ? 'yes' : 'no') . ', role: ' . ($existingUser && $existingUser->role ? $existingUser->role->role : 'N/A'));
        if ($existingUser && $existingUser->role->role === 'Society User') {
            Log::info('User record exists for mobile: ' . $mobile . ', falling back to users table auth');
            return false; // Let attemptWebLogin handle it
        }

        $societyDetail = SocietyDetail::where('owner1_mobile', $mobile)
            ->orWhere('owner2_mobile', $mobile)
            ->orWhere('owner3_mobile', $mobile)
            ->first();

        log::info('SocietyDetail check for mobile: ' . $mobile . ', found record: ' . ($societyDetail ? 'yes' : 'no'));
        if (! $societyDetail) {
            return false;
        }

        $expectedPassword = null;
        if ($societyDetail && $societyDetail->status) {
            $statusData = json_decode($societyDetail->status, true);
            if (is_string($statusData)) {
                $statusData = json_decode($statusData, true);
            }

            log::info('Decoded status data for society detail ID: ' . $societyDetail->id . ', data: ' . json_encode($statusData));
            $expectedPassword = $statusData['password'] ?? null;
        }
        log::info('Expected password for mobile: ' . $mobile . ' is ' . ($expectedPassword ? 'set' : 'not set'));
        if (! $expectedPassword || ! hash_equals((string) $expectedPassword, (string) $this->password)) {
            log::info('Password mismatch for mobile: ' . $mobile . ', expected: ' . ($expectedPassword ?? 'NULL') . ', provided: ' . $this->password);
            return false;
        }

        log::info('Password match successful for mobile: ' . $mobile . ', proceeding with user creation/update');
        if ($societyDetail->owner1_mobile === $mobile) {
            log::info('Mobile matches owner1 for society detail ID: ' . $societyDetail->id);
            $name = $societyDetail->owner1_name;
            $email = $societyDetail->owner1_email;
        } elseif ($societyDetail->owner2_mobile === $mobile) {
            log::info('Mobile matches owner2 for society detail ID: ' . $societyDetail->id);
            $name = $societyDetail->owner2_name;
            $email = $societyDetail->owner2_email;
        } else {
            log::info('Mobile matches owner3 for society detail ID: ' . $societyDetail->id);
            $name = $societyDetail->owner3_name;
            $email = $societyDetail->owner3_email;
        }
        // Fix: convert empty email to NULL
        $email = !empty($email) ? $email : null;

        log::info('Determined name: ' . $name . ', email: ' . ($email ?? 'NULL') . ' for mobile: ' . $mobile);
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

        log::info('User record created/updated for mobile: ' . $mobile . ', user ID: ' . $user->id);
        if ($user->role_id !== $roleId) {
            $user->role_id = $roleId;
            $user->save();
        }
        log::info('Ensured role assignment for user ID: ' . $user->id . ', role ID: ' . $roleId);
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
