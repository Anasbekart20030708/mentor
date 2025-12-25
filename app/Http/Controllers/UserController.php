<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by mentor status
        if ($request->has('is_mentor')) {
            $query->where('is_mentor', $request->is_mentor);
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Order by points
        if ($request->has('sort_by_points')) {
            $query->orderBy('points', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(15);
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'level' => 'required|string|max:50',
            'is_mentor' => 'boolean',
            'bio' => 'nullable|string|max:1000',
            'points' => 'nullable|integer|min:0',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_mentor'] = $request->has('is_mentor') ? true : false;
        $validated['points'] = $validated['points'] ?? 0;

        $user = User::create($validated);

        // If user is a mentor, create mentor record
        if ($user->is_mentor && $request->has('modules')) {
            Mentor::create([
                'user_id' => $user->id,
                'modules' => $request->modules,
                'average_rating' => 0,
                'total_sessions' => 0,
            ]);
        }

        return redirect()->route('users.show', $user)
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load([
            'mentor',
            'sessionsAsMentor',
            'sessionsAsMentee',
            'feedbackReceived',
            'helpRequestsAsMentee'
        ]);

        $stats = [
            'total_sessions_as_mentor' => $user->sessionsAsMentor()->count(),
            'total_sessions_as_mentee' => $user->sessionsAsMentee()->count(),
            'completed_sessions_as_mentor' => $user->sessionsAsMentor()->where('status', 'completed')->count(),
            'average_rating' => $user->feedbackReceived()->avg('rating'),
            'total_feedback' => $user->feedbackReceived()->count(),
            'help_requests' => $user->helpRequestsAsMentee()->count(),
            'pending_help_requests' => $user->helpRequestsAsMentee()->where('status', 'pending')->count(),
        ];

        return view('users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Only allow users to edit their own profile or admins
        if (Auth::id() !== $user->id && !Auth::user()->is_admin ?? false) {
            abort(403, 'Unauthorized action.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Only allow users to update their own profile or admins
        if (Auth::id() !== $user->id && !Auth::user()->is_admin ?? false) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'level' => 'required|string|max:50',
            'bio' => 'nullable|string|max:1000',
            'is_mentor' => 'boolean',
        ]);
        $validated['level'] = 'Beginner';
        $validated['is_mentor'] = $request->has('is_mentor') ? true : false;

        $user->update($validated);

        // Handle mentor status change
        if ($validated['is_mentor'] && !$user->mentor) {
            // User became a mentor, create mentor record
            if ($request->has('modules')) {
                Mentor::create([
                    'user_id' => $user->id,
                    'modules' => json_encode($request->modules), // <- encode it                    'average_rating' => 0,
                    'total_sessions' => 0,
                ]);
            }
        } elseif (!$validated['is_mentor'] && $user->mentor) {
            // User is no longer a mentor, optionally handle this
            // You might want to keep the mentor record for history
        }

        return redirect()->route('users.show', $user)
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if (Auth::id() === $user->id) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Show user profile (current authenticated user).
     */
    public function profile()
    {
        $user = Auth::user();
        $user->load([
            'mentor',
            'sessionsAsMentor',
            'sessionsAsMentee',
            'feedbackReceived',
            'helpRequestsAsMentee'
        ]);

        $stats = [
            'total_sessions_as_mentor' => $user->sessionsAsMentor()->count(),
            'total_sessions_as_mentee' => $user->sessionsAsMentee()->count(),
            'completed_sessions_as_mentor' => $user->sessionsAsMentor()->where('status', 'completed')->count(),
            'average_rating' => round($user->feedbackReceived()->avg('rating'), 2),
            'total_feedback' => $user->feedbackReceived()->count(),
            'help_requests' => $user->helpRequestsAsMentee()->count(),
            'pending_help_requests' => $user->helpRequestsAsMentee()->where('status', 'pending')->count(),
            'points' => $user->points,
            'level' => $user->level,
        ];

        return view('users.profile', compact('user', 'stats'));
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request, User $user)
    {
        // Only allow users to update their own password
        if (Auth::id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()
                ->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->back()
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Add points to user.
     */
    public function addPoints(Request $request, User $user)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $user->increment('points', $validated['points']);

        return redirect()->back()
            ->with('success', 'Points added successfully.');
    }

    /**
     * Get leaderboard (top users by points).
     */
    public function leaderboard()
    {
        $users = User::orderBy('points', 'desc')
            ->orderBy('name', 'asc')
            ->limit(50)
            ->get();

        return view('users.leaderboard', compact('users'));
    }

    /**
     * Get all mentors.
     */
    public function mentors(Request $request)
    {
        $query = User::where('is_mentor', true)->with('mentor');

        // Search by module
        if ($request->has('module')) {
            $module = $request->module;
            $query->whereHas('mentor', function($q) use ($module) {
                $q->where('modules', 'like', '%' . $module . '%');
            });
        }

        // Sort by rating
        if ($request->has('sort_by_rating')) {
            $query->join('mentors', 'users.id', '=', 'mentors.user_id')
                  ->orderBy('mentors.average_rating', 'desc')
                  ->select('users.*');
        }

        $mentors = $query->paginate(15);

        return view('users.mentors', compact('mentors'));
    }

    /**
     * Get user statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'total_mentors' => User::where('is_mentor', true)->count(),
            'total_mentees' => User::where('is_mentor', false)->count(),
            'avg_points' => round(User::avg('points'), 2),
            'top_user' => User::orderBy('points', 'desc')->first(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_this_week' => User::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'users_by_level' => User::select('level', \DB::raw('count(*) as count'))
                ->groupBy('level')
                ->get(),
        ];

        return view('users.statistics', compact('stats'));
    }

    /**
     * Toggle mentor status.
     */
    public function toggleMentorStatus(Request $request, User $user)
    {
        $user->update([
            'is_mentor' => !$user->is_mentor
        ]);

        if ($user->is_mentor && !$user->mentor) {
            // Create mentor record
            Mentor::create([
                'user_id' => $user->id,
                'modules' => $request->input('modules', ''),
                'average_rating' => 0,
                'total_sessions' => 0,
            ]);
        }

        $status = $user->is_mentor ? 'mentor' : 'regular user';

        return redirect()->back()
            ->with('success', "User status changed to {$status}.");
    }
}