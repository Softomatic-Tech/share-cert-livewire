<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('timelines')->insert([
            ['name' => 'Verify Details', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Application', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Verification', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Certificate Generated', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Certificate Delivered', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
