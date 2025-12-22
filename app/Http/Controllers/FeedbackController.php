<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Sessionn;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    /**
     * Display a listing of feedback.
     */
    public function index(Request $request)
    {
        $query = Feedback::with(['session', 'mentorUser', 'mentee']);

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by mentor
        if ($request->has('mentor_id')) {
            $query->where('mentor_id', $request->mentor_id);
        }

        // Filter by problem resolved
        if ($request->has('problem_resolved')) {
            $query->where('problem_resolved', $request->problem_resolved);
        }

        $feedback = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('feedback.index', compact('feedback'));
    }

    /**
     * Show the form for creating new feedback.
     */
    public function create(Request $request)
    {
        $sessionId = $request->query('session_id');
        $session = null;
        
        if ($sessionId) {
            $session = Sessionn::with(['mentor', 'mentee'])->findOrFail($sessionId);
            
            // Check if feedback already exists
            if ($session->feedback) {
                return redirect()->route('feedback.show', $session->feedback)
                    ->with('info', 'Feedback already exists for this session.');
            }
        }
        
        return view('feedback.create', compact('session'));
    }

    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:sessionns,id|unique:feedback,session_id',
            'mentor_id' => 'required|exists:users,id',
            'mentee_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'problem_resolved' => 'required|boolean',
        ]);

        $feedback = Feedback::create($validated);

        // Update mentor's average rating
        $this->updateMentorRating($validated['mentor_id']);

        return redirect()->route('feedback.show', $feedback)
            ->with('success', 'Feedback submitted successfully. Thank you!');
    }

    /**
     * Display the specified feedback.
     */
    public function show(Feedback $feedback)
    {
        $feedback->load(['session', 'mentorUser', 'mentee']);
        return view('feedback.show', compact('feedback'));
    }

    /**
     * Show the form for editing the specified feedback.
     */
    public function edit(Feedback $feedback)
    {
        // Only allow mentee to edit their own feedback
        if (Auth::id() !== $feedback->mentee_id) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('feedback.edit', compact('feedback'));
    }

    /**
     * Update the specified feedback in storage.
     */
    public function update(Request $request, Feedback $feedback)
    {
        // Only allow mentee to update their own feedback
        if (Auth::id() !== $feedback->mentee_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'problem_resolved' => 'required|boolean',
        ]);

        $feedback->update($validated);

        // Update mentor's average rating
        $this->updateMentorRating($feedback->mentor_id);

        return redirect()->route('feedback.show', $feedback)
            ->with('success', 'Feedback updated successfully.');
    }

    /**
     * Remove the specified feedback from storage.
     */
    public function destroy(Feedback $feedback)
    {
        $mentorId = $feedback->mentor_id;
        $feedback->delete();

        // Update mentor's average rating
        $this->updateMentorRating($mentorId);

        return redirect()->route('feedback.index')
            ->with('success', 'Feedback deleted successfully.');
    }

    /**
     * Get feedback for a specific mentor.
     */
    public function mentorFeedback($mentorId)
    {
        $mentor = User::findOrFail($mentorId);
        $feedback = Feedback::where('mentor_id', $mentorId)
            ->with(['session', 'mentee'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $stats = $this->getMentorFeedbackStats($mentorId);
        
        return view('feedback.mentor-feedback', compact('mentor', 'feedback', 'stats'));
    }

    /**
     * Get feedback for current user's sessions as mentee.
     */
    public function myFeedback()
    {
        $userId = Auth::id();
        
        $feedback = Feedback::where('mentee_id', $userId)
            ->with(['session', 'mentorUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('feedback.my-feedback', compact('feedback'));
    }

    /**
     * Get feedback received by current user as mentor.
     */
    public function receivedFeedback()
    {
        $userId = Auth::id();
        
        $feedback = Feedback::where('mentor_id', $userId)
            ->with(['session', 'mentee'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $stats = $this->getMentorFeedbackStats($userId);
        
        return view('feedback.received-feedback', compact('feedback', 'stats'));
    }

    /**
     * Get feedback statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_feedback' => Feedback::count(),
            'average_rating' => round(Feedback::avg('rating'), 2),
            'problems_resolved' => Feedback::where('problem_resolved', true)->count(),
            'problems_unresolved' => Feedback::where('problem_resolved', false)->count(),
            'rating_distribution' => [
                '5_star' => Feedback::where('rating', 5)->count(),
                '4_star' => Feedback::where('rating', 4)->count(),
                '3_star' => Feedback::where('rating', 3)->count(),
                '2_star' => Feedback::where('rating', 2)->count(),
                '1_star' => Feedback::where('rating', 1)->count(),
            ],
            'recent_feedback' => Feedback::with(['mentorUser', 'mentee'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('feedback.statistics', compact('stats'));
    }

    /**
     * Get pending feedback (completed sessions without feedback).
     */
    public function pending()
    {
        $userId = Auth::id();
        
        $pendingSessions = Sessionn::where('mentee_id', $userId)
            ->where('status', 'completed')
            ->whereDoesntHave('feedback')
            ->with(['mentor'])
            ->orderBy('scheduled_at', 'desc')
            ->get();
        
        return view('feedback.pending', compact('pendingSessions'));
    }

    /**
     * Update mentor's average rating.
     */
    private function updateMentorRating($mentorId)
    {
        $mentor = Mentor::where('user_id', $mentorId)->first();
        
        if ($mentor) {
            $averageRating = Feedback::where('mentor_id', $mentorId)->avg('rating');
            $totalSessions = Sessionn::where('mentor_id', $mentorId)
                ->where('status', 'completed')
                ->count();
            
            $mentor->update([
                'average_rating' => round($averageRating, 2),
                'total_sessions' => $totalSessions,
            ]);
        }
    }

    /**
     * Get mentor feedback statistics.
     */
    private function getMentorFeedbackStats($mentorId)
    {
        return [
            'total_feedback' => Feedback::where('mentor_id', $mentorId)->count(),
            'average_rating' => round(Feedback::where('mentor_id', $mentorId)->avg('rating'), 2),
            'problems_resolved' => Feedback::where('mentor_id', $mentorId)
                ->where('problem_resolved', true)
                ->count(),
            'rating_distribution' => [
                '5_star' => Feedback::where('mentor_id', $mentorId)->where('rating', 5)->count(),
                '4_star' => Feedback::where('mentor_id', $mentorId)->where('rating', 4)->count(),
                '3_star' => Feedback::where('mentor_id', $mentorId)->where('rating', 3)->count(),
                '2_star' => Feedback::where('mentor_id', $mentorId)->where('rating', 2)->count(),
                '1_star' => Feedback::where('mentor_id', $mentorId)->where('rating', 1)->count(),
            ],
        ];
    }

    /**
     * Get top rated mentors.
     */
    public function topRatedMentors()
    {
        $topMentors = DB::table('feedback')
            ->select('mentor_id', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as feedback_count'))
            ->groupBy('mentor_id')
            ->having('feedback_count', '>=', 3) // At least 3 feedbacks
            ->orderBy('avg_rating', 'desc')
            ->orderBy('feedback_count', 'desc')
            ->limit(10)
            ->get();
        
        $mentors = [];
        foreach ($topMentors as $topMentor) {
            $user = User::with('mentor')->find($topMentor->mentor_id);
            if ($user) {
                $mentors[] = [
                    'user' => $user,
                    'avg_rating' => round($topMentor->avg_rating, 2),
                    'feedback_count' => $topMentor->feedback_count,
                ];
            }
        }
        
        return view('feedback.top-rated', compact('mentors'));
    }
}