<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MentorSeeder::class,
            HelpRequestSeeder::class,
            SessionSeeder::class,
            FeedbackSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✓ Base de données remplie avec succès !');
        $this->command->info('');
        $this->command->info('Identifiants de connexion :');
        $this->command->info('==========================');
        $this->command->info('Mentors :');
        $this->command->info('  john@mentor.com / password');
        $this->command->info('  sarah@mentor.com / password');
        $this->command->info('  mike@mentor.com / password');
        $this->command->info('  lisa@mentor.com / password');
        $this->command->info('');
        $this->command->info('Étudiants :');
        $this->command->info('  alice@student.com / password');
        $this->command->info('  bob@student.com / password');
        $this->command->info('  charlie@student.com / password');
        $this->command->info('  diana@student.com / password');
        $this->command->info('  eve@student.com / password');
        $this->command->info('');
        $this->command->info('Compte de test :');
        $this->command->info('  test@test.com / password');
    }
}
