<?php

use App\Livewire\Auth\Register;
use Livewire\Livewire;
use App\Models\Role;
use App\Models\SecurityQuestion;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $this->seed();

    $security_question = SecurityQuestion::firstOrCreate(
        ['question' => 'What is your favorite color?'],
        ['created_at' => now(), 'updated_at' => now()]
    );
    $response = Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', null)
        ->set('phone', '9876543210')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('security_question_id', $security_question->id)
        ->set('security_answer', 'Red')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});