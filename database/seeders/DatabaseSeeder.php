<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superAdminId = Role::factory()->create(['role' => 'Super Admin']);
        $adminId = Role::factory()->create(['role' => 'Admin']);
        $societyUserId = Role::factory()->create(['role' => 'Society User'])->id;

        // Now create a user and assign the "Society User" role
        User::factory()->create([
            'name' => 'Test Society User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'role_id' => $societyUserId,
            'password' => bcrypt('password'), // if password is required for login
        ]);
    }
}
