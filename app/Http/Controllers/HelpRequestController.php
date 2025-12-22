<?php

namespace App\Http\Controllers;

use App\Models\HelpRequest;
use App\Models\User;
use App\Models\Mentor;
use App\Models\Sessionn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HelpRequestController extends Controller
{
    /**
     * Display a listing of help requests.
     */
    public function index(Request $request)
    {
        $query = HelpRequest::with(['mentee', 'mentor']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by module
        if ($request->has('module')) {
            $query->where('module', 'like', '%' . $request->module . '%');
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('proposed_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('proposed_date', '<=', $request->date_to);
        }

        $helpRequests = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('help-requests.index', compact('helpRequests'));
    }

    /**
     * Show the form for creating a new help request.
     */
    public function create()
    {
        $mentors = User::whereHas('mentor')->with('mentor')->get();
        return view('help-requests.create', compact('mentors'));
    }

    /**
     * Store a newly created help request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mentor_id' => 'nullable|exists:users,id',
            'module' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'proposed_date' => 'required|date|after:now',
            'type' => 'required|in:online,in-person',
            'status' => 'nullable|in:pending,accepted,rejected,in-progress,resolved,cancelled',
        ]);

        // Set mentee_id to current authenticated user
        $validated['mentee_id'] = Auth::id();
        
        // Set default status
        $validated['status'] = $validated['status'] ?? 'pending';

        $helpRequest = HelpRequest::create($validated);

        return redirect()->route('help-requests.show', $helpRequest)
            ->with('success', 'Help request submitted successfully.');
    }

    /**
     * Display the specified help request.
     */
    public function show(HelpRequest $helpRequest)
    {
        $helpRequest->load(['mentee', 'mentor', 'session']);
        return view('help-requests.show', compact('helpRequest'));
    }

    /**
     * Show the form for editing the specified help request.
     */
    public function edit(HelpRequest $helpRequest)
    {
        // Only allow mentee to edit their own pending requests
        if (Auth::id() !== $helpRequest->mentee_id || $helpRequest->status !== 'pending') {
            abort(403, 'Unauthorized action.');
        }

        $mentors = User::whereHas('mentor')->with('mentor')->get();
        return view('help-requests.edit', compact('helpRequest', 'mentors'));
    }

    /**
     * Update the specified help request in storage.
     */
    public function update(Request $request, HelpRequest $helpRequest)
    {
        // Only allow mentee to update their own pending requests
        if (Auth::id() !== $helpRequest->mentee_id || $helpRequest->status !== 'pending') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'mentor_id' => 'nullable|exists:users,id',
            'module' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'proposed_date' => 'required|date|after:now',
            'type' => 'required|in:online,in-person',
        ]);

        $helpRequest->update($validated);

        return redirect()->route('help-requests.show', $helpRequest)
            ->with('success', 'Help request updated successfully.');
    }

    /**
     * Remove the specified help request from storage.
     */
    public function destroy(HelpRequest $helpRequest)
    {
        // Only allow mentee to delete their own requests
        if (Auth::id() !== $helpRequest->mentee_id) {
            abort(403, 'Unauthorized action.');
        }

        $helpRequest->delete();

        return redirect()->route('help-requests.index')
            ->with('success', 'Help request deleted successfully.');
    }

    /**
     * Get help requests for current user as mentee.
     */
    public function myRequests()
    {
        $userId = Auth::id();
        
        $helpRequests = HelpRequest::where('mentee_id', $userId)
            ->with(['mentor', 'session'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('help-requests.my-requests', compact('helpRequests'));
    }

    /**
     * Get help requests assigned to current user as mentor.
     */
    public function assignedToMe()
    {
        $userId = Auth::id();
        
        $helpRequests = HelpRequest::where('mentor_id', $userId)
            ->with(['mentee', 'session'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('help-requests.assigned-to-me', compact('helpRequests'));
    }

    /**
     * Get pending help requests (for mentors to view).
     */
    public function pending()
    {
        $helpRequests = HelpRequest::where('status', 'pending')
            ->with(['mentee'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('help-requests.pending', compact('helpRequests'));
    }

    /**
     * Accept a help request (mentor action).
     */
    public function accept(HelpRequest $helpRequest)
    {
        $userId = Auth::id();
        
        // Check if user is a mentor
        if (!User::find($userId)->mentor) {
            abort(403, 'Only mentors can accept help requests.');
        }

        // Update the help request
        $helpRequest->update([
            'mentor_id' => $userId,
            'status' => 'accepted',
        ]);

        // Optionally create a session automatically
        Sessionn::create([
            'help_request_id' => $helpRequest->id,
            'mentor_id' => $userId,
            'mentee_id' => $helpRequest->mentee_id,
            'module' => $helpRequest->module,
            'scheduled_at' => $helpRequest->proposed_date,
            'type' => $helpRequest->type,
            'status' => 'scheduled',
        ]);

        // Update help request status to in-progress
        $helpRequest->update(['status' => 'in-progress']);

        return redirect()->back()
            ->with('success', 'Help request accepted and session created successfully.');
    }

    /**
     * Reject a help request (mentor action).
     */
    public function reject(Request $request, HelpRequest $helpRequest)
    {
        $userId = Auth::id();
        
        // Check if user is the assigned mentor or any mentor for unassigned requests
        if ($helpRequest->mentor_id && $helpRequest->mentor_id !== $userId) {
            abort(403, 'Unauthorized action.');
        }

        $helpRequest->update([
            'status' => 'rejected',
            'mentor_id' => $userId, // Record who rejected it
        ]);

        return redirect()->back()
            ->with('success', 'Help request rejected.');
    }

    /**
     * Cancel a help request (mentee action).
     */
    public function cancel(HelpRequest $helpRequest)
    {
        // Only mentee can cancel their own request
        if (Auth::id() !== $helpRequest->mentee_id) {
            abort(403, 'Unauthorized action.');
        }

        $helpRequest->update(['status' => 'cancelled']);

        return redirect()->back()
            ->with('success', 'Help request cancelled successfully.');
    }

    /**
     * Mark help request as resolved.
     */
    public function resolve(HelpRequest $helpRequest)
    {
        // Only mentor or mentee can mark as resolved
        if (Auth::id() !== $helpRequest->mentor_id && Auth::id() !== $helpRequest->mentee_id) {
            abort(403, 'Unauthorized action.');
        }

        $helpRequest->update(['status' => 'resolved']);

        return redirect()->back()
            ->with('success', 'Help request marked as resolved.');
    }

    /**
     * Assign a mentor to a help request.
     */
    public function assignMentor(Request $request, HelpRequest $helpRequest)
    {
        $validated = $request->validate([
            'mentor_id' => 'required|exists:users,id',
        ]);

        // Verify the user is a mentor
        if (!User::find($validated['mentor_id'])->mentor) {
            return redirect()->back()
                ->with('error', 'Selected user is not a mentor.');
        }

        $helpRequest->update([
            'mentor_id' => $validated['mentor_id'],
            'status' => 'accepted',
        ]);

        return redirect()->back()
            ->with('success', 'Mentor assigned successfully.');
    }

    /**
     * Get help request statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_requests' => HelpRequest::count(),
            'pending' => HelpRequest::where('status', 'pending')->count(),
            'accepted' => HelpRequest::where('status', 'accepted')->count(),
            'rejected' => HelpRequest::where('status', 'rejected')->count(),
            'in_progress' => HelpRequest::where('status', 'in-progress')->count(),
            'resolved' => HelpRequest::where('status', 'resolved')->count(),
            'cancelled' => HelpRequest::where('status', 'cancelled')->count(),
            'online' => HelpRequest::where('type', 'online')->count(),
            'in_person' => HelpRequest::where('type', 'in-person')->count(),
            'today' => HelpRequest::whereDate('created_at', today())->count(),
            'this_week' => HelpRequest::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'avg_response_time' => $this->calculateAverageResponseTime(),
        ];

        return view('help-requests.statistics', compact('stats'));
    }

    /**
     * Calculate average response time.
     */
    private function calculateAverageResponseTime()
    {
        $acceptedRequests = HelpRequest::whereIn('status', ['accepted', 'in-progress', 'resolved'])
            ->whereNotNull('updated_at')
            ->get();

        if ($acceptedRequests->isEmpty()) {
            return 0;
        }

        $totalMinutes = 0;
        foreach ($acceptedRequests as $request) {
            $totalMinutes += $request->created_at->diffInMinutes($request->updated_at);
        }

        return round($totalMinutes / $acceptedRequests->count() / 60, 2); // Return in hours
    }

    /**
     * Search help requests by module or description.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $helpRequests = HelpRequest::where('module', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->with(['mentee', 'mentor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('help-requests.search', compact('helpRequests', 'query'));
    }

    /**
     * Get available mentors for a specific module.
     */
    public function availableMentors($module)
    {
        $mentors = Mentor::where('modules', 'like', '%' . $module . '%')
            ->with('user')
            ->orderBy('average_rating', 'desc')
            ->get();
        
        return response()->json($mentors);
    }
}