<?php

use App\Livewire\Auth\Register;
use Livewire\Livewire;
use App\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $role = Role::firstOrCreate(
        ['role' => 'Society User'],
        ['created_at' => now(), 'updated_at' => now()]
    );
    $response = Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('phone', '9876543210')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('role_id', $role->id)
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});