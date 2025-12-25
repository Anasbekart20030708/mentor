<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HelpRequest;
use App\Models\User;
use Carbon\Carbon;

class HelpRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get mentees and mentors
        $mentees = User::where('is_mentor', false)->get();
        $mentors = User::where('is_mentor', true)->get();

        if ($mentees->isEmpty() || $mentors->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Pending help requests (En attente) - Still need a mentor assigned
        $pendingRequests = [
            [
                'mentee_id' => $mentees[0]->id,
                'mentor_id' => $mentors[0]->id, // Assigned but not yet accepted
                'module' => 'Laravel Relationships',
                'description' => 'J\'ai besoin d\'aide pour comprendre les relations Eloquent, en particulier les relations polymorphiques.',
                'proposed_date' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
                'type' => 'En ligne',
                'status' => 'En attente',
            ],
            [
                'mentee_id' => $mentees[1]->id,
                'mentor_id' => $mentors[1]->id,
                'module' => 'JavaScript Promises',
                'description' => 'Je galère avec async/await et le chaînage de promesses en JavaScript.',
                'proposed_date' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
                'type' => 'En ligne',
                'status' => 'En attente',
            ],
            [
                'mentee_id' => $mentees[3]->id,
                'mentor_id' => $mentors[3]->id,
                'module' => 'CSS Flexbox',
                'description' => 'Besoin d\'aide avec la mise en page CSS en utilisant Flexbox et Grid.',
                'proposed_date' => Carbon::now()->addDays(4)->format('Y-m-d H:i:s'),
                'type' => 'Présentiel',
                'status' => 'En attente',
            ],
        ];

        foreach ($pendingRequests as $request) {
            HelpRequest::create($request);
        }

        // Accepted help requests (Acceptée)
        $acceptedRequests = [
            [
                'mentee_id' => $mentees[0]->id,
                'mentor_id' => $mentors[0]->id,
                'module' => 'Laravel Middleware',
                'description' => 'Besoin de conseils pour créer un middleware personnalisé dans Laravel.',
                'proposed_date' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
                'type' => 'En ligne',
                'status' => 'Acceptée',
            ],
            [
                'mentee_id' => $mentees[1]->id,
                'mentor_id' => $mentors[1]->id,
                'module' => 'React Hooks',
                'description' => 'Je veux apprendre les hooks useState et useEffect dans React.',
                'proposed_date' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
                'type' => 'En ligne',
                'status' => 'Acceptée',
            ],
            [
                'mentee_id' => $mentees[2]->id,
                'mentor_id' => $mentors[2]->id,
                'module' => 'Database Optimization',
                'description' => 'Besoin d\'aide pour optimiser mes requêtes de base de données et les index.',
                'proposed_date' => Carbon::now()->addDays(5)->format('Y-m-d H:i:s'),
                'type' => 'Présentiel',
                'status' => 'Acceptée',
            ],
        ];

        foreach ($acceptedRequests as $request) {
            HelpRequest::create($request);
        }

        // Rejected requests (Refusée)
        $rejectedRequests = [
            [
                'mentee_id' => $mentees[4]->id,
                'mentor_id' => $mentors[0]->id,
                'module' => 'Advanced AI',
                'description' => 'Besoin d\'aide avec les algorithmes d\'apprentissage automatique.',
                'proposed_date' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
                'type' => 'En ligne',
                'status' => 'Refusée',
            ],
        ];

        foreach ($rejectedRequests as $request) {
            HelpRequest::create($request);
        }
    }
}
