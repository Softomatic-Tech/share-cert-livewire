<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.auth')]
class ForgotPassword extends Component
{
    public string $email = '';
    public $step = 1;
    public $mobile;
    public $answer;
    public $password;
    public $password_confirmation;
    public $user;
    public $securityQuestion;

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('A reset link will be sent if the account exists.'));
    }

    public function findUser()
    {
        $this->validate(['mobile' => 'required|digits:10']);

        $this->user = User::where('phone', $this->mobile)->first();
        if (!$this->user) {
            session()->flash('status', __('This mobile number is not registered.'));
            return;
        }
        $this->securityQuestion = optional($this->user->securityQuestion)->question;
        if (!$this->securityQuestion) {
            session()->flash('status', __('No security question found for this user.'));
            return;
        }
        $this->step = 2;
    }

    public function verifyAnswer()
    {
        $this->validate(['answer' => 'required']);

        if (!Hash::check($this->answer, $this->user->security_answer)) {
            session()->flash('status', __('Your answer is incorrect. Please try again.'));
            return;
        }

        $this->step = 3;
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        $this->user->update(['password' => Hash::make($this->password)]);

        session()->flash('success', 'Password reset successfully.');
        return redirect()->route('login');
    }
}
