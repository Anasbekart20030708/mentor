@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-800 mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
    <p class="text-gray-600">Here's what's happening in your mentoring journey</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6 card-hover">
        <div class="text-3xl mb-2">📚</div>
        <h3 class="text-gray-500 text-sm uppercase mb-2">Total Sessions</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $totalSessions ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 card-hover">
        <div class="text-3xl mb-2">⭐</div>
        <h3 class="text-gray-500 text-sm uppercase mb-2">Your Rating</h3>
        <p class="text-3xl font-bold text-gray-800">{{ number_format($averageRating ?? 0, 1) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 card-hover">
        <div class="text-3xl mb-2">🎯</div>
        <h3 class="text-gray-500 text-sm uppercase mb-2">Points</h3>
        <p class="text-3xl font-bold text-gray-800">{{ Auth::user()->points }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 card-hover">
        <div class="text-3xl mb-2">📝</div>
        <h3 class="text-gray-500 text-sm uppercase mb-2">Pending Requests</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $pendingRequests ?? 0 }}</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg shadow-lg p-8 text-white">
        <h2 class="text-2xl font-bold mb-4">Need Help?</h2>
        <p class="mb-6 opacity-90">Submit a help request and connect with experienced mentors</p>
        <a href="{{ route('help-requests.create') }}" class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold inline-block hover:bg-gray-100 transition">
            Create Help Request
        </a>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-lg p-8 text-white">
        <h2 class="text-2xl font-bold mb-4">Browse Mentors</h2>
        <p class="mb-6 opacity-90">Find the perfect mentor for your learning needs</p>
        {{-- <a href="{{ route('users.mentors') }}" class="bg-white text-green-600 px-6 py-3 rounded-lg font-semibold inline-block hover:bg-gray-100 transition">
            View All Mentors
        </a> --}}
    </div>
</div>

<!-- Upcoming Sessions -->
@if(isset($upcomingSessions) && $upcomingSessions->count() > 0)
<div class="bg-white rounded-lg shadow-lg p-6 mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Upcoming Sessions</h2>
    <div class="space-y-4">
        @foreach($upcomingSessions as $session)
        <div class="border-l-4 border-purple-500 pl-4 py-2">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $session->module }}</h3>
                    <p class="text-sm text-gray-600">
                        with {{ $session->mentor_id == Auth::id() ? $session->mentee->name : $session->mentor->name }}
                    </p>
                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($session->scheduled_at)->format('M d, Y - h:i A') }}</p>
                </div>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">{{ ucfirst($session->type) }}</span>
            </div>
        </div>
        @endforeach
    </div>
    <a href="{{ route('sessions.upcoming') }}" class="text-purple-600 hover:text-purple-800 font-semibold mt-4 inline-block">View All →</a>
</div>
@endif

<!-- Recent Activity -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Recent Activity</h2>
    @if(isset($recentActivity) && $recentActivity->count() > 0)
        <div class="space-y-3">
            @foreach($recentActivity as $activity)
            <div class="flex items-center space-x-4 py-2 border-b last:border-b-0">
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                    <span class="text-xl">{{ $activity->icon ?? '📌' }}</span>
                </div>
                <div class="flex-1">
                    <p class="text-gray-800">{{ $activity->description }}</p>
                    <p class="text-sm text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">No recent activity</p>
    @endif
</div>
@endsection