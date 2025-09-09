<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SecurityQuestion;

class SecurityQuestions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $questions = [
            'What was the name of your first pet?',
            'What is your mother’s maiden name?',
            'What was the name of your first school?',
            'What city were you born in?',
            'What is your favorite book?',
            'What was the make of your first car?',
        ];

        foreach ($questions as $question) {
            SecurityQuestion::create(['question' => $question]);
        }
    }
}
