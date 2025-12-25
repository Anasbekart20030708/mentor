<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MentorController extends Controller
{
    /**
     * Display a listing of mentors.
     */
    public function index()
    {
        $mentors = Mentor::with('user')->paginate(15);
        return view('users.mentors', compact('mentors'));
    }

    /**
     * Show the form for creating a new mentor.
     */
    public function create()
    {
        $users = User::whereDoesntHave('mentor')->get();
        return view('mentors.create', compact('users'));
    }

    /**
     * Store a newly created mentor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:mentors,user_id',
            'modules' => 'required|string',
            'average_rating' => 'nullable|numeric|min:0|max:5',
            'total_sessions' => 'nullable|integer|min:0',
        ]);

        $mentor = Mentor::create($validated);

        return redirect()->route('mentors.show', $mentor)
            ->with('success', 'Mentor created successfully.');
    }

    /**
     * Display the specified mentor.
     */
    public function show(Mentor $mentor)
    {
        $mentor->load(['user', 'feedback', 'sessions']);
        return view('mentors.show', compact('mentor'));
    }

    /**
     * Show the form for editing the specified mentor.
     */
    public function edit(Mentor $mentor)
    {
        return view('mentors.edit', compact('mentor'));
    }

    /**
     * Update the specified mentor in storage.
     */
    public function update(Request $request, Mentor $mentor)
    {
        $validated = $request->validate([
            'modules' => 'required|string',
            'average_rating' => 'nullable|numeric|min:0|max:5',
            'total_sessions' => 'nullable|integer|min:0',
        ]);

        $mentor->update($validated);

        return redirect()->route('mentors.show', $mentor)
            ->with('success', 'Mentor updated successfully.');
    }

    /**
     * Remove the specified mentor from storage.
     */
    public function destroy(Mentor $mentor)
    {
        $mentor->delete();

        return redirect()->route('mentors.index')
            ->with('success', 'Mentor deleted successfully.');
    }

    /**
     * Get mentor statistics.
     */
    public function statistics(Mentor $mentor)
    {
        $stats = [
            'total_sessions' => $mentor->sessions()->count(),
            'average_rating' => $mentor->feedback()->avg('rating'),
            'total_feedback' => $mentor->feedback()->count(),
            'completed_sessions' => $mentor->sessions()->where('status', 'completed')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Update mentor rating.
     */
    public function updateRating(Mentor $mentor)
    {
        $averageRating = $mentor->feedback()->avg('rating');
        $totalSessions = $mentor->sessions()->count();

        $mentor->update([
            'average_rating' => $averageRating,
            'total_sessions' => $totalSessions,
        ]);

        return response()->json([
            'success' => true,
            'average_rating' => $averageRating,
            'total_sessions' => $totalSessions,
        ]);
    }

    /**
     * Get top mentors by rating.
     */
    public function topMentors()
    {
        $topMentors = Mentor::with('user')
            ->orderBy('average_rating', 'desc')
            ->orderBy('total_sessions', 'desc')
            ->limit(10)
            ->get();

        return view('mentors.top', compact('topMentors'));
    }
}