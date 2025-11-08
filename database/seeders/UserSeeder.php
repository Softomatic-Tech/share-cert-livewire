<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminId = Role::factory()->create(['role' => 'Super Admin'])->id;
        $adminId = Role::factory()->create(['role' => 'Admin']);

        // Now create a user and assign the "Society User" role
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'phone' => '1234567890',
            'role_id' => $superAdminId,
            'password' => bcrypt('superadmin'), // if password is required for login
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'phone' => '9087654321',
            'role_id' => $adminId,
            'password' => bcrypt('admin'), // if password is required for login
        ]);
    }
}
