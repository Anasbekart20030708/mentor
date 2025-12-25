<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MentorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assuming you already have users in your 'users' table
        $users = DB::table('users')->pluck('id');

        foreach ($users as $userId) {
            DB::table('mentors')->insert([
                'user_id' => $userId,
                'modules' => json_encode([
                    'PHP',
                    'Laravel',
                    'React'
                ]), // Example modules
                'average_rating' => rand(0, 50) / 10, // random rating between 0.0 and 5.0
                'total_sessions' => rand(0, 100), // random number of sessions
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
