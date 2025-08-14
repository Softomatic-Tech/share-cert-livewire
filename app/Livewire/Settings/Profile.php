<?php

namespace App\Livewire\Settings;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    public $name = '';

    public $email = '';

    public $phone = '';
    public $user;

    /**
     * Mount the component.
     */
    public function mount(UserService $userService)
    {
        $this->user = $userService->getAuthenticatedUser();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        // $this->name = Auth::user()->name;
        // $this->email = Auth::user()->email;
        // $this->phone = Auth::user()->phone;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = $this->user->find(Auth::id());
        // $user = Auth::user();
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'nullable',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->whereNotNull('email')->ignore($user->id),
            ],
            'phone' => ['required', 'digits:10', 'unique:'.User::class],
        ]);

        if (empty($validated['email']) && empty($validated['phone'])) {
            $this->dispatch('show-error', message:  "Either email or phone is required!");
        }
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        // $this->dispatch('profile-updated', name: $user->name);
        $this->dispatch('show-success', message:  "Profile updated successfully!");
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = User::find(Auth::id());
        // $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}
