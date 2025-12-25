@extends('layouts.app')

@section('title', 'Submit Feedback')

@section('content')
<div class="mb-8">
    <a href="{{ isset($session) ? route('sessions.show', $session) : route('feedback.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">← Back</a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Rate Your Mentoring Session</h1>
        <p class="text-gray-600 mb-6">Your feedback helps mentors improve and helps other mentees find the right mentor</p>

        @if(isset($session))
        <div class="bg-purple-50 border-l-4 border-purple-400 p-4 rounded mb-6">
            <p class="text-sm text-gray-600">Session with</p>
            <p class="font-bold text-gray-800">{{ $session->mentor->name }}</p>
            <p class="text-sm text-gray-600">{{ $session->module }} - {{ \Carbon\Carbon::parse($session->scheduled_at)->format('M d, Y') }}</p>
        </div>
        @endif

        <form action="{{ route('feedback.store') }}" method="POST">
            @csrf

            @if(isset($session))
            <input type="hidden" name="session_id" value="{{ $session->id }}">
            <input type="hidden" name="mentor_id" value="{{ $session->mentor_id }}">
            <input type="hidden" name="mentee_id" value="{{ $session->mentee_id }}">
            @endif

            <div class="space-y-6">
                <!-- Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">How would you rate this session? *</label>
                    <div class="flex justify-center space-x-4 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="{{ $i }}" required class="sr-only peer">
                            <div class="text-6xl transition-all peer-checked:scale-125 opacity-40 peer-checked:opacity-100 hover:opacity-70">
                                ⭐
                            </div>
                        </label>
                        @endfor
                    </div>
                    <div class="text-center text-sm text-gray-500 space-x-8">
                        <span>Poor</span>
                        <span>Fair</span>
                        <span>Good</span>
                        <span>Great</span>
                        <span>Excellent</span>
                    </div>
                    @error('rating')
                        <p class="mt-2 text-sm text-red-600 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Problem Resolved -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Was your problem resolved? *</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition border-gray-200">
                            <input type="radio" name="problem_resolved" value="1" required class="sr-only peer">
                            <div class="flex-1 peer-checked:text-green-600">
                                <div class="text-3xl mb-2">✅</div>
                                <p class="font-semibold">Yes, resolved!</p>
                                <p class="text-sm opacity-75">I got the help I needed</p>
                            </div>
                        </label>
                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition border-gray-200">
                            <input type="radio" name="problem_resolved" value="0" required class="sr-only peer">
                            <div class="flex-1 peer-checked:text-red-600">
                                <div class="text-3xl mb-2">❌</div>
                                <p class="font-semibold">Not quite</p>
                                <p class="text-sm opacity-75">I still need more help</p>
                            </div>
                        </label>
                    </div>
                    @error('problem_resolved')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Comments (Optional)</label>
                    <textarea name="comment" rows="6" 
                              placeholder="Share your experience... What did you like? What could be improved?"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('comment') border-red-500 @enderror">{{ old('comment') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Your detailed feedback helps mentors grow and improve</p>
                    @error('comment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 flex space-x-4">
                <button type="submit" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Submit Feedback
                </button>
                <a href="{{ isset($session) ? route('sessions.show', $session) : route('feedback.index') }}" 
                   class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold text-center hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Privacy Note -->
    <div class="mt-6 bg-gray-50 border border-gray-200 p-4 rounded text-sm text-gray-600">
        <p><strong>Note:</strong> Your feedback will be visible to the mentor and will contribute to their overall rating. Please be honest and constructive.</p>
    </div>
</div>
@endsection