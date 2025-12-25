<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sessionn;
use App\Models\HelpRequest;
use App\Models\User;
use Carbon\Carbon;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get accepted help requests for sessions
        $acceptedRequests = HelpRequest::where('status', 'Acceptée')->get();

        if ($acceptedRequests->isEmpty()) {
            $this->command->warn('No accepted help requests found. Please run HelpRequestSeeder first.');
            return;
        }

        // Create scheduled sessions for accepted requests
        foreach ($acceptedRequests as $request) {
            Sessionn::create([
                'help_request_id' => $request->id,
                'mentor_id' => $request->mentor_id,
                'mentee_id' => $request->mentee_id,
                'module' => $request->module,
                'scheduled_at' => Carbon::parse($request->proposed_date),
                'type' => $request->type,
                'status' => 'Planifiée',
                'mentor_notes' => null,
            ]);
        }

        // Create some additional past completed sessions
        $mentors = User::where('is_mentor', true)->get();
        $mentees = User::where('is_mentor', false)->get();

        if ($mentors->isNotEmpty() && $mentees->isNotEmpty()) {
            // Create dummy help requests for completed sessions
            $completedHelpRequests = [];

            $completedData = [
                [
                    'mentee_id' => $mentees[0]->id,
                    'mentor_id' => $mentors[0]->id,
                    'module' => 'Laravel Validation',
                    'description' => 'Besoin d\'aide avec les règles de validation des formulaires.',
                    'proposed_date' => Carbon::now()->subDays(5)->format('Y-m-d H:i:s'),
                    'type' => 'En ligne',
                    'status' => 'Acceptée',
                ],
                [
                    'mentee_id' => $mentees[1]->id,
                    'mentor_id' => $mentors[1]->id,
                    'module' => 'Git Basics',
                    'description' => 'Apprentissage des commandes Git et des workflows.',
                    'proposed_date' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                    'type' => 'En ligne',
                    'status' => 'Acceptée',
                ],
                [
                    'mentee_id' => $mentees[2]->id,
                    'mentor_id' => $mentors[2]->id,
                    'module' => 'PHP OOP',
                    'description' => 'Compréhension des concepts de programmation orientée objet.',
                    'proposed_date' => Carbon::now()->subDays(10)->format('Y-m-d H:i:s'),
                    'type' => 'Présentiel',
                    'status' => 'Acceptée',
                ],
                [
                    'mentee_id' => $mentees[3]->id,
                    'mentor_id' => $mentors[3]->id,
                    'module' => 'HTML/CSS Basics',
                    'description' => 'Débuter avec les fondamentaux du développement web.',
                    'proposed_date' => Carbon::now()->subDays(3)->format('Y-m-d H:i:s'),
                    'type' => 'En ligne',
                    'status' => 'Acceptée',
                ],
                [
                    'mentee_id' => $mentees[0]->id,
                    'mentor_id' => $mentors[0]->id,
                    'module' => 'Laravel Routes',
                    'description' => 'Apprentissage des concepts de routage de base et avancés.',
                    'proposed_date' => Carbon::now()->subDays(15)->format('Y-m-d H:i:s'),
                    'type' => 'En ligne',
                    'status' => 'Acceptée',
                ],
            ];

            foreach ($completedData as $data) {
                $helpRequest = HelpRequest::create($data);

                Sessionn::create([
                    'help_request_id' => $helpRequest->id,
                    'mentor_id' => $data['mentor_id'],
                    'mentee_id' => $data['mentee_id'],
                    'module' => $data['module'],
                    'scheduled_at' => Carbon::parse($data['proposed_date']),
                    'type' => $data['type'],
                    'status' => 'Terminée',
                    'mentor_notes' => 'Session bien déroulée. L\'étudiant a montré une bonne compréhension et posé des questions pertinentes.',
                ]);
            }

            // Create one cancelled session
            $cancelledRequest = HelpRequest::create([
                'mentee_id' => $mentees[3]->id,
                'mentor_id' => $mentors[0]->id,
                'module' => 'Vue.js Components',
                'description' => 'Apprentissage des composants Vue.js.',
                'proposed_date' => Carbon::now()->subDays(2)->format('Y-m-d H:i:s'),
                'type' => 'En ligne',
                'status' => 'Acceptée',
            ]);

            Sessionn::create([
                'help_request_id' => $cancelledRequest->id,
                'mentor_id' => $mentors[0]->id,
                'mentee_id' => $mentees[3]->id,
                'module' => 'Vue.js Components',
                'scheduled_at' => Carbon::now()->subDays(2),
                'type' => 'En ligne',
                'status' => 'Annulée',
                'mentor_notes' => 'Session annulée en raison d\'un conflit d\'horaire.',
            ]);
        }
    }
}
