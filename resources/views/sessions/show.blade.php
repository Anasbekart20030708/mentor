@extends('layouts.app')

@section('title', 'Session Details')

@section('content')
<div class="mb-8">
    <a href="{{ route('sessions.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">← Back to Sessions</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Session Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $sessionn->module }}</h1>
                    <div class="flex space-x-2">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($sessionn->status == 'completed') bg-green-100 text-green-800
                            @elseif($sessionn->status == 'scheduled') bg-blue-100 text-blue-800
                            @elseif($sessionn->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($sessionn->status) }}
                        </span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                            {{ ucfirst($sessionn->type) }}
                        </span>
                    </div>
                </div>
                
                @if($sessionn->status == 'scheduled' && (Auth::id() == $sessionn->mentor_id || Auth::id() == $sessionn->mentee_id))
                <div class="flex space-x-2">
                    <a href="{{ route('sessions.edit', $sessionn) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Edit
                    </a>
                </div>
                @endif
            </div>

            <!-- Session Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="border-l-4 border-purple-500 pl-4">
                    <p class="text-sm text-gray-600 mb-1">Mentor</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            {{-- <span class="text-purple-600 font-semibold">{{ substr($sessionn->mentor->name, 0, 1) }}</span> --}}
                        </div>
                        <div>
                            {{-- <p class="font-semibold text-gray-800">{{ $sessionn->mentor->name }}</p> --}}
                            {{-- <p class="text-sm text-gray-500">{{ $sessionn->mentor->email }}</p> --}}
                        </div>
                    </div>
                </div>

                <div class="border-l-4 border-blue-500 pl-4">
                    <p class="text-sm text-gray-600 mb-1">Mentee</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            {{-- <span class="text-blue-600 font-semibold">{{ substr($sessionn->mentee->name, 0, 1) }}</span> --}}
                        </div>
                        <div>
                            {{-- <p class="font-semibold text-gray-800">{{ $sessionn->mentee->name }}</p> --}}
                            {{-- <p class="text-sm text-gray-500">{{ $sessionn->mentee->email }}</p> --}}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex items-center text-gray-700">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($sessionn->scheduled_at)->format('l, F d, Y') }}</p>
                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($sessionn->scheduled_at)->format('h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Mentor Notes -->
            @if($sessionn->mentor_notes)
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Notes</h3>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <p class="text-gray-700">{{ $sessionn->mentor_notes }}</p>
                </div>
            </div>
            @endif

            <!-- Help Request Link -->
            @if($sessionn->helpRequest)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <p class="text-sm text-gray-600 mb-1">Related to Help Request</p>
                <a href="{{ route('help-requests.show', $sessionn->helpRequest) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                    View Original Request →
                </a>
            </div>
            @endif
        </div>

        <!-- Feedback Section -->
        @if($sessionn->status == 'completed')
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Feedback</h2>
            
            @if($sessionn->feedback)
            <div class="border-l-4 border-green-500 pl-4">
                <div class="flex items-center mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="text-2xl text-yellow-400">{{ $i <= $sessionn->feedback->rating ? '★' : '☆' }}</span>
                    @endfor
                    <span class="ml-3 text-lg font-semibold text-gray-700">{{ $sessionn->feedback->rating }}/5</span>
                </div>
                @if($sessionn->feedback->comment)
                <p class="text-gray-700 mt-2">{{ $sessionn->feedback->comment }}</p>
                @endif
                <div class="mt-3">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $sessionn->feedback->problem_resolved ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        Problem {{ $sessionn->feedback->problem_resolved ? 'Resolved' : 'Not Resolved' }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-3">Submitted {{ $sessionn->feedback->created_at->diffForHumans() }}</p>
            </div>
            @elseif(Auth::id() == $sessionn->mentee_id)
            <div class="text-center py-8">
                <div class="text-5xl mb-4">⭐</div>
                <p class="text-gray-600 mb-4">Please provide feedback for this session</p>
                <a href="{{ route('feedback.create', ['session_id' => $sessionn->id]) }}" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Submit Feedback
                </a>
            </div>
            @else
            <p class="text-gray-500 text-center py-8">No feedback submitted yet</p>
            @endif
        </div>
        @endif
    </div>

    <!-- Actions Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Actions</h3>
            
            <div class="space-y-3">
                @if($sessionn->status == 'scheduled')
                    @if(Auth::id() == $sessionn->mentor_id || Auth::id() == $sessionn->mentee_id)
                    <form action="{{ route('sessions.complete', $sessionn) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                            ✓ Mark as Complete
                        </button>
                    </form>

                    <button onclick="document.getElementById('rescheduleModal').classList.remove('hidden')" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                        📅 Reschedule
                    </button>

                    <button onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="w-full bg-red-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-red-700 transition">
                        ✕ Cancel Session
                    </button>
                    @endif
                @endif

                {{-- <a href="{{ route('sessions.edit', $sessionn) }}" class="block w-full bg-gray-200 text-gray-700 text-center px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    ✏️ Edit Details
                </a> --}}

                @if(Auth::id() == $sessionn->mentor_id)
                <a href="{{ route('users.show', $sessionn->mentee) }}" class="block w-full bg-purple-100 text-purple-700 text-center px-4 py-3 rounded-lg font-semibold hover:bg-purple-200 transition">
                    👤 View Mentee Profile
                </a>
                @endif

                @if(Auth::id() == $sessionn->mentee_id)
                <a href="{{ route('users.show', $sessionn->mentor) }}" class="block w-full bg-purple-100 text-purple-700 text-center px-4 py-3 rounded-lg font-semibold hover:bg-purple-200 transition">
                    👤 View Mentor Profile
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div id="rescheduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Reschedule Session</h3>
        {{-- <form action="{{ route('sessions.reschedule', $sessionn) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Date & Time</label>
                <input type="datetime-local" name="scheduled_at" required 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Reschedule
                </button>
                <button type="button" onclick="document.getElementById('rescheduleModal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form> --}}
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Cancel Session</h3>
        {{-- <form action="{{ route('sessions.cancel', $sessionn) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Cancellation</label>
                <textarea name="cancellation_reason" rows="3" 
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition">
                    Confirm Cancel
                </button>
                <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Keep Session
                </button>
            </div>
        </form> --}}
    </div>
</div>
@endsection