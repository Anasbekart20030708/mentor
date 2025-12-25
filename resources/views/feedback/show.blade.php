@extends('layouts.app')

@section('title', 'Feedback Details')

@section('content')
<div class="mb-8">
    <a href="{{ route('feedback.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">← Back to Feedback</a>
</div>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="flex justify-center items-center mb-4">
                @for($i = 1; $i <= 5; $i++)
                    <span class="text-5xl {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                @endfor
            </div>
            <div class="text-4xl font-bold text-gray-800 mb-2">{{ $feedback->rating }}/5</div>
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                {{ $feedback->problem_resolved ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                Problem {{ $feedback->problem_resolved ? 'Resolved' : 'Not Resolved' }}
            </span>
        </div>

        @if($feedback->comment)
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-600 mb-2">COMMENT</h3>
            <p class="text-gray-800 text-lg italic">"{{ $feedback->comment }}"</p>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="border-l-4 border-purple-500 pl-4">
                <p class="text-sm text-gray-600 mb-1">Mentor</p>
                <p class="font-bold text-gray-800 text-lg">{{ $feedback->mentorUser->name }}</p>
                <a href="{{ route('users.show', $feedback->mentorUser) }}" class="text-purple-600 hover:text-purple-800 text-sm">View Profile →</a>
            </div>

            <div class="border-l-4 border-blue-500 pl-4">
                <p class="text-sm text-gray-600 mb-1">Mentee</p>
                <p class="font-bold text-gray-800 text-lg">{{ $feedback->mentee->name }}</p>
                <a href="{{ route('users.show', $feedback->mentee) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Profile →</a>
            </div>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded mb-6">
            <p class="text-sm text-gray-600 mb-1">Session</p>
            <p class="font-semibold text-gray-800">{{ $feedback->session->module }}</p>
            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($feedback->session->scheduled_at)->format('M d, Y') }}</p>
            <a href="{{ route('sessions.show', $feedback->session) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">View Session →</a>
        </div>

        <div class="text-center text-sm text-gray-500">
            Submitted {{ $feedback->created_at->diffForHumans() }}
        </div>

        @if(Auth::id() == $feedback->mentee_id)
        <div class="mt-6 flex space-x-3">
            <a href="{{ route('feedback.edit', $feedback) }}" class="flex-1 bg-blue-600 text-white text-center px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                Edit Feedback
            </a>
            <form action="{{ route('feedback.destroy', $feedback) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this feedback?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition">
                    Delete
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection