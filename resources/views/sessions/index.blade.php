@extends('layouts.app')

@section('title', 'Sessions')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-4xl font-bold text-gray-800">Sessions</h1>
        <p class="text-gray-600 mt-2">Manage all mentoring sessions</p>
    </div>
    <a href="{{ route('sessions.create') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
        + Create Session
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form action="{{ route('sessions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">All Statuses</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="no-show" {{ request('status') == 'no-show' ? 'selected' : '' }}>No Show</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
            <select name="type" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">All Types</option>
                <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Online</option>
                <option value="in-person" {{ request('type') == 'in-person' ? 'selected' : '' }}>In-Person</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
                Filter
            </button>
            <a href="{{ route('sessions.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Sessions List -->
<div class="space-y-4">
    @forelse($sessions as $session)
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
        <div class="flex flex-wrap justify-between items-start gap-4">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
                    <h3 class="text-xl font-bold text-gray-800">{{ $session->module }}</h3>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($session->status == 'completed') bg-green-100 text-green-800
                        @elseif($session->status == 'scheduled') bg-blue-100 text-blue-800
                        @elseif($session->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($session->status) }}
                    </span>
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                        {{ ucfirst($session->type) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <p class="text-sm text-gray-600">Mentor</p>
                        <p class="font-semibold text-gray-800">{{ $session->mentor->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Mentee</p>
                        <p class="font-semibold text-gray-800">{{ $session->mentee->name }}</p>
                    </div>
                </div>

                <div class="mt-3 flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('M d, Y - h:i A') }}
                </div>

                @if($session->mentor_notes)
                <div class="mt-3 text-sm text-gray-600">
                    <strong>Notes:</strong> {{ $session->mentor_notes }}
                </div>
                @endif
            </div>

            <div class="flex flex-col space-y-2">
                <a href="{{ route('sessions.show', $session) }}" class="bg-purple-100 text-purple-700 px-4 py-2 rounded-lg text-center font-semibold hover:bg-purple-200 transition">
                    View Details
                </a>
                @if($session->status == 'scheduled' && (Auth::id() == $session->mentor_id || Auth::id() == $session->mentee_id))
                <form action="{{ route('sessions.complete', $session) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
                        Mark Complete
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow-lg p-12 text-center">
        <div class="text-6xl mb-4">📅</div>
        <p class="text-xl text-gray-600">No sessions found</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($sessions->hasPages())
<div class="mt-8">
    {{ $sessions->links() }}
</div>
@endif
@endsection