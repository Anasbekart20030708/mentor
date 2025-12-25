<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sessionn;
use App\Models\HelpRequest;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Calculate statistics
        $totalSessions = $user->sessionsAsMentee->count() + $user->sessionsAsMentor->count();

        $averageRating = 0;
        if ($user->is_mentor) {
            $averageRating = $user->feedbackReceived()->avg('rating') ?? 0;
        }

        $pendingRequests = $user->helpRequestsAsMentee()
            ->where('status', 'pending')
            ->count();

        // Get upcoming sessions
        $upcomingSessions = Sessionn::where(function ($query) use ($user) {
            $query->where('mentor_id', $user->id)
                ->orWhere('mentee_id', $user->id);
        })
            ->where('scheduled_at', '>', now())
            ->where('status', 'scheduled')
            ->with(['mentor', 'mentee'])
            ->orderBy('scheduled_at', 'asc')
            ->limit(5)
            ->get();

        // Get recent activity (mix of different activities)
        $recentActivity = collect();

        // Recent sessions
        $recentSessions = Sessionn::where(function ($query) use ($user) {
            $query->where('mentor_id', $user->id)
                ->orWhere('mentee_id', $user->id);
        })
            ->where('created_at', '>', now()->subDays(7))
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($session) use ($user) {
                return (object)[
                    'icon' => '📚',
                    'description' => $session->mentor_id == $user->id
                        ? "Session scheduled with {$session->mentee->name} for {$session->module}"
                        : "Session scheduled with {$session->mentor->name} for {$session->module}",
                    'created_at' => $session->created_at
                ];
            });

        // Recent help requests
        $recentRequests = HelpRequest::where('mentee_id', $user->id)
            ->where('created_at', '>', now()->subDays(7))
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($request) {
                return (object)[
                    'icon' => '❓',
                    'description' => "Help request created for {$request->module}",
                    'created_at' => $request->created_at
                ];
            });

        // Recent feedback received (if mentor)
        if ($user->is_mentor) {
            $recentFeedback = Feedback::where('mentor_id', $user->id)
                ->where('created_at', '>', now()->subDays(7))
                ->latest()
                ->limit(3)
                ->get()
                ->map(function ($feedback) {
                    return (object)[
                        'icon' => '⭐',
                        'description' => "Received {$feedback->rating}-star feedback from {$feedback->mentee->name}",
                        'created_at' => $feedback->created_at
                    ];
                });
            $recentActivity = $recentActivity->merge($recentFeedback);
        }

        $recentActivity = $recentActivity
            ->merge($recentSessions)
            ->merge($recentRequests)
            ->sortByDesc('created_at')
            ->take(10);

        return view('dashboard', compact(
            'totalSessions',
            'averageRating',
            'pendingRequests',
            'upcomingSessions',
            'recentActivity'
        ));
    }
}
