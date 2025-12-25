<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feedback;
use App\Models\Sessionn;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get completed sessions
        $completedSessions = Sessionn::where('status', 'Terminée')->get();

        if ($completedSessions->isEmpty()) {
            $this->command->warn('No completed sessions found. Please run SessionSeeder first.');
            return;
        }

        $feedbackComments = [
            [
                'rating' => 5,
                'comment' => 'Excellent mentor ! Très patient et a expliqué les concepts clairement. J\'ai beaucoup appris.',
                'problem_resolved' => 'Oui',
            ],
            [
                'rating' => 5,
                'comment' => 'Session exceptionnelle ! Le mentor a fait plus que nécessaire pour m\'aider à comprendre.',
                'problem_resolved' => 'Oui',
            ],
            [
                'rating' => 4,
                'comment' => 'Très bon mentor avec de bonnes connaissances. J\'aurais aimé plus d\'exemples pratiques.',
                'problem_resolved' => 'Oui',
            ],
            [
                'rating' => 5,
                'comment' => 'Parfait ! J\'ai obtenu exactement l\'aide dont j\'avais besoin. Je recommande vivement ce mentor.',
                'problem_resolved' => 'Oui',
            ],
            [
                'rating' => 4,
                'comment' => 'Session très utile. Le mentor était compétent et sympathique.',
                'problem_resolved' => 'Oui',
            ],
            [
                'rating' => 5,
                'comment' => 'Compétences pédagogiques exceptionnelles ! A rendu les sujets complexes faciles à comprendre.',
                'problem_resolved' => 'Oui',
            ],
            [
                'rating' => 4,
                'comment' => 'Bonne session dans l\'ensemble. J\'ai appris de nouvelles techniques et bonnes pratiques.',
                'problem_resolved' => 'Partiellement',
            ],
            [
                'rating' => 5,
                'comment' => 'Mentor incroyable ! Très réactif et a fourni d\'excellents exemples de code.',
                'problem_resolved' => 'Oui',
            ],
            [
                'rating' => 3,
                'comment' => 'Session correcte mais un peu précipitée. J\'ai quand même appris quelque chose de précieux.',
                'problem_resolved' => 'Partiellement',
            ],
            [
                'rating' => 5,
                'comment' => 'Expérience merveilleuse ! Le mentor se soucie vraiment de la réussite de l\'étudiant.',
                'problem_resolved' => 'Oui',
            ],
        ];

        foreach ($completedSessions as $index => $session) {
            $feedbackData = $feedbackComments[$index % count($feedbackComments)];

            Feedback::create([
                'session_id' => $session->id,
                'mentor_id' => $session->mentor_id,
                'mentee_id' => $session->mentee_id,
                'rating' => $feedbackData['rating'],
                'comment' => $feedbackData['comment'],
                'problem_resolved' => $feedbackData['problem_resolved'],
            ]);
        }
    }
}
