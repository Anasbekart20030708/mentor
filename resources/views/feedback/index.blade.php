@extends('layouts.app')

@section('title', 'Feedback')

@section('content')
<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-800">Feedback</h1>
    <p class="text-gray-600 mt-2">Browse all feedback and ratings</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form action="{{ route('feedback.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
            <select name="rating" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">All Ratings</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Problem Resolved</label>
            <select name="problem_resolved" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">All</option>
                <option value="1" {{ request('problem_resolved') == '1' ? 'selected' : '' }}>Resolved</option>
                <option value="0" {{ request('problem_resolved') == '0' ? 'selected' : '' }}>Not Resolved</option>
            </select>
        </div>
        <div class="md:col-span-2 flex items-end space-x-2">
            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
                Filter
            </button>
            <a href="{{ route('feedback.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Feedback List -->
<div class="space-y-4">
    @forelse($feedback as $item)
    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
        <div class="flex flex-wrap justify-between items-start gap-4">
            <div class="flex-1">
                <div class="flex items-center space-x-4 mb-3">
                    <!-- Rating Stars -->
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="text-2xl {{ $i <= $item->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                        @endfor
                        <span class="ml-2 text-lg font-bold text-gray-700">{{ $item->rating }}/5</span>
                    </div>
                    
                    <!-- Problem Status -->
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        {{ $item->problem_resolved ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $item->problem_resolved ? '✓ Resolved' : '✗ Not Resolved' }}
                    </span>
                </div>

                @if($item->comment)
                <p class="text-gray-700 mb-3 italic">"{{ $item->comment }}"</p>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Mentor:</span>
                        <span class="font-semibold text-gray-800">{{ $item->mentorUser->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Mentee:</span>
                        <span class="font-semibold text-gray-800">{{ $item->mentee->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Module:</span>
                        <span class="font-semibold text-gray-800">{{ $item->session->module }}</span>
                    </div>
                </div>

                <p class="text-sm text-gray-500 mt-3">{{ $item->created_at->diffForHumans() }}</p>
            </div>

            <div class="flex flex-col space-y-2">
                <a href="{{ route('feedback.show', $item) }}" class="bg-purple-100 text-purple-700 px-4 py-2 rounded-lg text-center font-semibold hover:bg-purple-200 transition whitespace-nowrap">
                    View Details
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow-lg p-12 text-center">
        <div class="text-6xl mb-4">⭐</div>
        <p class="text-xl text-gray-600">No feedback found</p>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($feedback->hasPages())
<div class="mt-8">
    {{ $feedback->links() }}
</div>
@endif
@endsection