<?php

namespace App\Http\Controllers;

use App\Models\Sessionn;
use App\Models\HelpRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionnController extends Controller
{
    /**
     * Display a listing of sessions.
     */
    public function index(Request $request)
    {
        $query = Sessionn::with(['mentor', 'mentee', 'helpRequest']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }

        $sessions = $query->orderBy('scheduled_at', 'desc')->paginate(15);
        
        return view('sessions.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new session.
     */
    public function create()
    {
        $helpRequests = HelpRequest::where('status', 'pending')->get();
        $mentors = User::whereHas('mentor')->get();
        $mentees = User::all();
        
        return view('sessions.create', compact('helpRequests', 'mentors', 'mentees'));
    }

    /**
     * Store a newly created session in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'help_request_id' => 'nullable|exists:help_requests,id',
            'mentor_id' => 'required|exists:users,id',
            'mentee_id' => 'required|exists:users,id',
            'module' => 'required|string|max:255',
            'scheduled_at' => 'required|date|after:now',
            'type' => 'required|in:online,in-person',
            'status' => 'nullable|in:scheduled,completed,cancelled,no-show',
            'mentor_notes' => 'nullable|string',
        ]);

        // Set default status
        $validated['status'] = $validated['status'] ?? 'scheduled';

        $session = Sessionn::create($validated);

        // Update help request status if linked
        if ($session->help_request_id) {
            HelpRequest::find($session->help_request_id)
                ->update(['status' => 'in-progress']);
        }

        return redirect()->route('sessions.show', $session)
            ->with('success', 'Session created successfully.');
    }

    /**
     * Display the specified session.
     */
    public function show(Sessionn $sessionn)
    {
        $sessionn->load(['mentor', 'mentee', 'helpRequest', 'feedback']);
        return view('sessions.show', compact('sessionn'));
    }

    /**
     * Show the form for editing the specified session.
     */
    public function edit(Sessionn $sessionn)
    {
        $mentors = User::whereHas('mentor')->get();
        $mentees = User::all();
        
        return view('sessions.edit', compact('sessionn', 'mentors', 'mentees'));
    }

    /**
     * Update the specified session in storage.
     */
    public function update(Request $request, Sessionn $sessionn)
    {
        $validated = $request->validate([
            'module' => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'type' => 'required|in:online,in-person',
            'status' => 'required|in:scheduled,completed,cancelled,no-show',
            'mentor_notes' => 'nullable|string',
        ]);

        $sessionn->update($validated);

        // Update help request status if session is completed
        if ($validated['status'] === 'completed' && $sessionn->help_request_id) {
            HelpRequest::find($sessionn->help_request_id)
                ->update(['status' => 'resolved']);
        }

        return redirect()->route('sessions.show', $sessionn)
            ->with('success', 'Session updated successfully.');
    }

    /**
     * Remove the specified session from storage.
     */
    public function destroy(Sessionn $sessionn)
    {
        $sessionn->delete();

        return redirect()->route('sessions.index')
            ->with('success', 'Session deleted successfully.');
    }

    /**
     * Get sessions for current user (as mentor or mentee).
     */
    public function mySessions()
    {
        $userId = Auth::id();
        
        $sessions = Sessionn::where('mentor_id', $userId)
            ->orWhere('mentee_id', $userId)
            ->with(['mentor', 'mentee', 'helpRequest'])
            ->orderBy('scheduled_at', 'desc')
            ->paginate(15);
        
        return view('sessions.my-sessions', compact('sessions'));
    }

    /**
     * Get upcoming sessions.
     */
    public function upcoming()
    {
        $sessions = Sessionn::where('scheduled_at', '>', now())
            ->where('status', 'scheduled')
            ->with(['mentor', 'mentee'])
            ->orderBy('scheduled_at', 'asc')
            ->paginate(15);
        
        return view('sessions.upcoming', compact('sessions'));
    }

    /**
     * Mark session as completed.
     */
    public function complete(Sessionn $sessionn)
    {
        $sessionn->update(['status' => 'completed']);

        // Update help request if linked
        if ($sessionn->help_request_id) {
            HelpRequest::find($sessionn->help_request_id)
                ->update(['status' => 'resolved']);
        }

        // Update mentor's total sessions
        $mentor = User::find($sessionn->mentor_id)->mentor;
        if ($mentor) {
            $mentor->update([
                'total_sessions' => $mentor->sessions()->where('status', 'completed')->count()
            ]);
        }

        return redirect()->back()
            ->with('success', 'Session marked as completed.');
    }

    /**
     * Cancel session.
     */
    public function cancel(Request $request, Sessionn $sessionn)
    {
        $sessionn->update([
            'status' => 'cancelled',
            'mentor_notes' => $request->input('cancellation_reason', $sessionn->mentor_notes)
        ]);

        // Update help request status if needed
        if ($sessionn->help_request_id) {
            HelpRequest::find($sessionn->help_request_id)
                ->update(['status' => 'pending']);
        }

        return redirect()->back()
            ->with('success', 'Session cancelled successfully.');
    }

    /**
     * Get session statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_sessions' => Sessionn::count(),
            'scheduled' => Sessionn::where('status', 'scheduled')->count(),
            'completed' => Sessionn::where('status', 'completed')->count(),
            'cancelled' => Sessionn::where('status', 'cancelled')->count(),
            'no_show' => Sessionn::where('status', 'no-show')->count(),
            'online' => Sessionn::where('type', 'online')->count(),
            'in_person' => Sessionn::where('type', 'in-person')->count(),
            'today' => Sessionn::whereDate('scheduled_at', today())->count(),
            'this_week' => Sessionn::whereBetween('scheduled_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];

        return view('sessions.statistics', compact('stats'));
    }

    /**
     * Reschedule session.
     */
    public function reschedule(Request $request, Sessionn $sessionn)
    {
        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        $sessionn->update($validated);

        return redirect()->back()
            ->with('success', 'Session rescheduled successfully.');
    }
}