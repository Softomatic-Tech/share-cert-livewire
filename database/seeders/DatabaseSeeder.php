<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $superAdminId = Role::factory()->create(['role' => 'Super Admin'])->id;
        $adminId = Role::factory()->create(['role' => 'Admin']);
        $societyUserId = Role::factory()->create(['role' => 'Society User'])->id;

        // Now create a user and assign the "Society User" role
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'phone' => '1234567890',
            'role_id' => $superAdminId,
            'password' => bcrypt('superadmin'), // if password is required for login
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '7898588550',
            'role_id' => $superAdminId,
            'password' => bcrypt('admin'), // if password is required for login
        ]);
        $this->call(StateCitySeeder::class);
        $this->call(TimelineSeeder::class);
    }
}
