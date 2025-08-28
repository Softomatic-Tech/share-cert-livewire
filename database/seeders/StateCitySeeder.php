<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\City;

class StateCitySeeder extends Seeder
{
    public function run(): void
    {
        $json = file_get_contents(database_path('data/states_cities.json'));
        $data = json_decode($json, true);

        $stateId = 1; // starting state_id

        foreach ($data as $stateName => $cities) {
            // Insert state
            $state = State::create([
                'id'   => $stateId,          // keep fixed ID
                'name' => $stateName,
                'code' => strtoupper(substr($stateName, 0, 3)), // example: AND, GOA
            ]);

            // Insert cities for this state
            foreach ($cities as $cityName) {
                City::create([
                    'name'     => $cityName,
                    'state_id' => $state->id,
                ]);
            }

            $stateId++;
        }
    }
}
