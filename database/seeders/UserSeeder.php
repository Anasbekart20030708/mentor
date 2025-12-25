<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create mentor users
        $mentors = [
            [
                'name' => 'John Mentor',
                'email' => 'john@mentor.com',
                'password' => Hash::make('password'),
                'level' => 'Advanced',
                'is_mentor' => true,
                'bio' => 'Ingénieur logiciel expérimenté avec plus de 5 ans en développement web. Spécialisé en Laravel, Vue.js et conception de bases de données.',
                'points' => 500,
            ],
            [
                'name' => 'Sarah Expert',
                'email' => 'sarah@mentor.com',
                'password' => Hash::make('password'),
                'level' => 'Advanced',
                'is_mentor' => true,
                'bio' => 'Développeuse full-stack passionnée par l\'enseignement. Experte en React, Node.js et technologies web modernes.',
                'points' => 450,
            ],
            [
                'name' => 'Mike Developer',
                'email' => 'mike@mentor.com',
                'password' => Hash::make('password'),
                'level' => 'Advanced',
                'is_mentor' => true,
                'bio' => 'Spécialiste backend avec expertise en PHP, Laravel et développement d\'API. J\'adore aider les autres à progresser.',
                'points' => 380,
            ],
            [
                'name' => 'Lisa Tech',
                'email' => 'lisa@mentor.com',
                'password' => Hash::make('password'),
                'level' => 'Advanced',
                'is_mentor' => true,
                'bio' => 'Passionnée de frontend spécialisée dans les frameworks JavaScript modernes et les principes UI/UX.',
                'points' => 420,
            ],
        ];

        foreach ($mentors as $mentor) {
            User::create($mentor);
        }

        // Create regular users (mentees)
        $mentees = [
            [
                'name' => 'Alice Student',
                'email' => 'alice@student.com',
                'password' => Hash::make('password'),
                'level' => 'Advanced',
                'is_mentor' => false,
                'bio' => 'Étudiante en informatique désireuse d\'apprendre le développement web.',
                'points' => 120,
            ],
            [
                'name' => 'Bob Learner',
                'email' => 'bob@student.com',
                'password' => Hash::make('password'),
                'level' => 'Advanced',
                'is_mentor' => false,
                'bio' => 'Débutant en programmation, enthousiaste à propos de Laravel.',
                'points' => 80,
            ],
            [
                'name' => 'Charlie Junior',
                'email' => 'charlie@student.com',
                'password' => Hash::make('password'),
                'level' => 'Advanced',
                'is_mentor' => false,
                'bio' => 'Développeur junior cherchant à améliorer ses compétences en développement backend.',
                'points' => 200,
            ],
            [
                'name' => 'Diana Novice',
                'email' => 'diana@student.com',
                'password' => Hash::make('password'),
                'level' => 'Advanced',
                'is_mentor' => false,
                'bio' => 'Nouvelle dans le codage, passionnée par l\'apprentissage.',
                'points' => 50,
            ],
            [
                'name' => 'Eve Beginner',
                'email' => 'eve@student.com',
                'password' => Hash::make('password'),
                'level' => 'Intermediate',
                'is_mentor' => false,
                'bio' => 'Développeuse autodidacte cherchant des conseils en développement web.',
                'points' => 95,
            ],
        ];

        foreach ($mentees as $mentee) {
            User::create($mentee);
        }

        // Create a test user
        User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
            'level' => 'Advanced',
            'is_mentor' => false,
            'bio' => 'Compte de test pour le développement.',
            'points' => 100,
        ]);
    }
}
