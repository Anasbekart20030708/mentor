@extends('layouts.app')

@section('title', 'Browse Mentors')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-800">Find Your Perfect Mentor</h1>
    <p class="text-gray-600 mt-2">Connect with experienced mentors ready to help you succeed</p>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form action="{{ route('users.mentors') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Search by Module</label>
            <input type="text" name="module" value="{{ request('module') }}" 
                   placeholder="e.g., Math, Programming, Physics..."
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
                Search
            </button>
            <a href="{{ route('users.mentors') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Mentors Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($mentors as $mentor)
    <div class="bg-white rounded-lg shadow-lg overflow-hidden card-hover">
        <div class="gradient-bg p-6 text-white">
            <div class="w-20 h-20 rounded-full bg-white mx-auto flex items-center justify-center mb-4">
                <span class="text-3xl text-purple-600 font-bold">{{ substr($mentor->name, 0, 1) }}</span>
            </div>
            <h3 class="text-xl font-bold text-center">{{ $mentor->name }}</h3>
            <p class="text-center text-sm opacity-90 mt-1">{{ $mentor->level }}</p>
        </div>
        
        <div class="p-6">
            @if($mentor->mentor)
            <div class="flex items-center justify-center space-x-4 mb-4 pb-4 border-b">
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($mentor->mentor->average_rating, 1) }}</div>
                    <div class="text-xs text-gray-500">Rating</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $mentor->mentor->total_sessions }}</div>
                    <div class="text-xs text-gray-500">Sessions</div>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm font-semibold text-gray-700 mb-2">Expertise:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(array_slice(explode(',', $mentor->mentor->modules), 0, 3) as $module)
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">{{ trim($module) }}</span>
                    @endforeach
                    @if(count(explode(',', $mentor->mentor->modules)) > 3)
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">+{{ count(explode(',', $mentor->mentor->modules)) - 3 }} more</span>
                    @endif
                </div>
            </div>
            @endif

            @if($mentor->bio)
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $mentor->bio }}</p>
            @endif

            <div class="flex space-x-2">
                <a href="{{ route('users.show', $mentor) }}" class="flex-1 bg-purple-100 text-purple-700 text-center px-4 py-2 rounded-lg font-semibold hover:bg-purple-200 transition">
                    View Profile
                </a>
                <a href="{{ route('help-requests.create', ['mentor_id' => $mentor->id]) }}" class="flex-1 bg-purple-600 text-white text-center px-4 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Request Help
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12">
        <div class="text-6xl mb-4">🔍</div>
        <p class="text-xl text-gray-600">No mentors found matching your criteria</p>
        <a href="{{ route('users.mentors') }}" class="text-purple-600 hover:text-purple-800 font-semibold mt-2 inline-block">Clear filters</a>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($mentors->hasPages())
<div class="mt-8">
    {{ $mentors->links() }}
</div>
@endif
@endsection