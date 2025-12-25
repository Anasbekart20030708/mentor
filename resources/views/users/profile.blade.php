@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-800">My Profile</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile Card -->
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

            <div class="mt-6 space-y-2">
                <a href="{{ route('users.edit', $user) }}" class="block w-full bg-purple-600 text-white text-center px-4 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Edit Profile
                </a>
                <button onclick="document.getElementById('passwordModal').classList.remove('hidden')" class="block w-full bg-gray-200 text-gray-700 text-center px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Change Password
                </button>
            </div>
        </div>

        @if($user->bio)
        <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Bio</h3>
            <p class="text-gray-600">{{ $user->bio }}</p>
        </div>
        @endif

        @if($user->is_mentor && $user->mentor)
        <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Modules</h3>
            <div class="flex flex-wrap gap-2">
                @foreach(explode(',', $user->mentor->modules) as $module)
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">{{ trim($module) }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Stats and Activity -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">📚</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_sessions_as_mentee'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Sessions as Mentee</p>
            </div>
            @if($user->is_mentor)
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">🎓</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_sessions_as_mentor'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Sessions as Mentor</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">✅</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['completed_sessions_as_mentor'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Completed</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">💬</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_feedback'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Feedback Received</p>
            </div>
            @else
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">❓</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['help_requests'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Help Requests</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">⏳</div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_help_requests'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Pending</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl mb-2">💬</div>
                <p class="text-2xl font-bold text-gray-800">{{ $user->feedbackGiven->count() }}</p>
                <p class="text-sm text-gray-600">Feedback Given</p>
            </div>
            @endif
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Links</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('sessions.my-sessions') }}" class="border-2 border-purple-200 rounded-lg p-4 hover:border-purple-500 transition">
                    <div class="flex items-center space-x-3">
                        <div class="text-3xl">📅</div>
                        <div>
                            <p class="font-semibold text-gray-800">My Sessions</p>
                            <p class="text-sm text-gray-600">View all sessions</p>
                        </div>
                    </div>
                </a>
                <a href="{{ route('help-requests.my-requests') }}" class="border-2 border-purple-200 rounded-lg p-4 hover:border-purple-500 transition">
                    <div class="flex items-center space-x-3">
                        <div class="text-3xl">💡</div>
                        <div>
                            <p class="font-semibold text-gray-800">My Requests</p>
                            <p class="text-sm text-gray-600">View help requests</p>
                        </div>
                    </div>
                </a>
                <a href="{{ route('feedback.my-feedback') }}" class="border-2 border-purple-200 rounded-lg p-4 hover:border-purple-500 transition">
                    <div class="flex items-center space-x-3">
                        <div class="text-3xl">⭐</div>
                        <div>
                            <p class="font-semibold text-gray-800">My Feedback</p>
                            <p class="text-sm text-gray-600">Feedback given</p>
                        </div>
                    </div>
                </a>
                @if($user->is_mentor)
                <a href="{{ route('feedback.received-feedback') }}" class="border-2 border-purple-200 rounded-lg p-4 hover:border-purple-500 transition">
                    <div class="flex items-center space-x-3">
                        <div class="text-3xl">📊</div>
                        <div>
                            <p class="font-semibold text-gray-800">Received Feedback</p>
                            <p class="text-sm text-gray-600">View ratings</p>
                        </div>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div id="passwordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Change Password</h3>
        <form action="{{ route('users.update-password', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" name="current_password" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
            <div class="mt-6 flex space-x-3">
                <button type="submit" class="flex-1 bg-purple-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Update Password
                </button>
                <button type="button" onclick="document.getElementById('passwordModal').classList.add('hidden')" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection