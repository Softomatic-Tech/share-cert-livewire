<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Role;
use App\Models\SecurityQuestion;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role_id = '';
    public string $security_question_id = '';
    public string $security_answer = '';
    public $securityQues=[];

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique(User::class)->whereNotNull('email')],
            'phone' => ['required', 'digits:10', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'security_question_id' => ['required', 'integer', 'exists:security_questions,id'],
            'security_answer' => ['required', 'string', 'max:255'],
        ]);
        $validated['email'] = $validated['email'] ?: null;
        $validated['password'] = Hash::make($validated['password']);
        $validated['security_answer'] = Hash::make($validated['security_answer']);
        
        $userCount = \App\Models\User::count();
        if ($userCount === 0) {
            // ✅ First user → Super Admin
            $validated['role_id'] = Role::where('role', 'Super Admin')->value('id');
        } else {
            if (!app()->environment('testing')) {
                // ✅ Check if phone exists in society_details (owner1, owner2, or owner3)
                $existsInSociety = \App\Models\SocietyDetail::where('owner1_mobile', $this->phone)
                    ->orWhere('owner2_mobile', $this->phone)
                    ->orWhere('owner3_mobile', $this->phone)
                    ->exists();
                if (! $existsInSociety) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'phone' => 'This mobile number is not associated with any apartment owner in a society.',
                    ]);
                }
            }
            $validated['role_id'] = Role::where('role', 'Society User')->value('id');
        }
        
        event(new Registered(($user = User::create($validated))));
        Auth::login($user);
        $this->redirect(route('dashboard', absolute: false));
    }

    public function mount()
    {
        $this->securityQues = SecurityQuestion::all();
    }
}
