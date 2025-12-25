@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="mb-8">
    <a href="{{ route('users.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">← Back to Users</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- User Profile -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-6">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 mx-auto flex items-center justify-center mb-4">
                    <span class="text-4xl text-white font-bold">{{ substr($user->name, 0, 1) }}</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-gray-600">{{ $user->email }}</p>
                @if($user->is_mentor)
                    <span class="inline-block mt-2 px-4 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Mentor</span>
                @else
                    <span class="inline-block mt-2 px-4 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">Mentee</span>
                @endif
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b">
                    <span class="text-gray-600">Level</span>
                    <span class="font-semibold text-gray-800">{{ $user->level }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b">
                    <span class="text-gray-600">Points</span>
                    <span class="font-semibold text-purple-600">{{ $user->points }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b">
                    <span class="text-gray-600">Member Since</span>
                    <span class="font-semibold text-gray-800">{{ $user->created_at->format('M Y') }}</span>
                </div>
                @if($user->is_mentor && $user->mentor)
                <div class="flex justify-between items-center py-3">
                    <span class="text-gray-600">Average Rating</span>
                    <span class="font-semibold text-yellow-600">⭐ {{ number_format($user->mentor->average_rating, 1) }}</span>
                </div>
                @endif
            </div>

            @if(Auth::id() === $user->id || (Auth::user()->is_admin ?? false))
            <div class="mt-6">
                <a href="{{ route('users.edit', $user) }}" class="block w-full bg-purple-600 text-white text-center px-4 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Edit Profile
                </a>
            </div>
            @endif
        </div>

        @if($user->bio)
        <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Bio</h3>
            <p class="text-gray-600">{{ $user->bio }}</p>
        </div>
        @endif

        @if($user->is_mentor && $user->mentor)
        <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Expertise</h3>
            <div class="flex flex-wrap gap-2">
                @foreach(explode(',', $user->mentor->modules) as $module)
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">{{ trim($module) }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Stats and Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">📚</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_sessions_as_mentee'] }}</p>
                <p class="text-sm text-gray-600">As Mentee</p>
            </div>
            @if($user->is_mentor)
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">🎓</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_sessions_as_mentor'] }}</p>
                <p class="text-sm text-gray-600">As Mentor</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">✅</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['completed_sessions_as_mentor'] }}</p>
                <p class="text-sm text-gray-600">Completed</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">⭐</div>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['average_rating'] ?? 0, 1) }}</p>
                <p class="text-sm text-gray-600">Avg Rating</p>
            </div>
            @else
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">❓</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['help_requests'] }}</p>
                <p class="text-sm text-gray-600">Help Requests</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">⏳</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_help_requests'] }}</p>
                <p class="text-sm text-gray-600">Pending</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">💬</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_feedback'] }}</p>
                <p class="text-sm text-gray-600">Feedback</p>
            </div>
            @endif
        </div>

        <!-- Recent Sessions -->
        @if($user->sessionsAsMentor->count() > 0 || $user->sessionsAsMentee->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Sessions</h3>
            <div class="space-y-4">
                @php
                    $recentSessions = $user->sessionsAsMentor->merge($user->sessionsAsMentee)->sortByDesc('scheduled_at')->take(5);
                @endphp
                @foreach($recentSessions as $session)
                <div class="border-l-4 border-purple-500 pl-4 py-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $session->module }}</h4>
                            <p class="text-sm text-gray-600">
                                {{ $session->mentor_id == $user->id ? 'with ' . $session->mentee->name : 'with ' . $session->mentor->name }}
                            </p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($session->scheduled_at)->format('M d, Y') }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded
                            @if($session->status == 'completed') bg-green-100 text-green-800
                            @elseif($session->status == 'scheduled') bg-blue-100 text-blue-800
                            @elseif($session->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Feedback -->
        @if($user->is_mentor && $user->feedbackReceived->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Feedback</h3>
            <div class="space-y-4">
                @foreach($user->feedbackReceived->take(5) as $feedback)
                <div class="border-b last:border-b-0 pb-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $feedback->mentee->name }}</p>
                            <div class="flex items-center mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-yellow-400">{{ $i <= $feedback->rating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                        </div>
                        <span class="text-sm text-gray-500">{{ $feedback->created_at->diffForHumans() }}</span>
                    </div>
                    @if($feedback->comment)
                    <p class="text-gray-600 text-sm">{{ $feedback->comment }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection