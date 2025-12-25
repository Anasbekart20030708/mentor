@extends('layouts.app')

@section('title', isset($helpRequest) ? 'Edit Help Request' : 'Create Help Request')

@section('content')
<div class="mb-8">
    <a href="{{ isset($helpRequest) ? route('help-requests.show', $helpRequest) : route('help-requests.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">← Back</a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ isset($helpRequest) ? 'Edit Help Request' : 'Request Help from a Mentor' }}</h1>
        <p class="text-gray-600 mb-6">Fill in the details below and we'll connect you with the right mentor</p>

        <form action="{{ isset($helpRequest) ? route('help-requests.update', $helpRequest) : route('help-requests.store') }}" method="POST">
            @csrf
            @if(isset($helpRequest))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <!-- Module -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Module / Subject *</label>
                    <input type="text" name="module" value="{{ old('module', $helpRequest->module ?? '') }}" required 
                           placeholder="e.g., Calculus, Python Programming, Physics"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('module') border-red-500 @enderror">
                    @error('module')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea name="description" rows="6" required 
                              placeholder="Describe what you need help with in detail..."
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $helpRequest->description ?? '') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Be as specific as possible to get the best help</p>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Proposed Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Proposed Date & Time *</label>
                    <input type="datetime-local" name="proposed_date" 
                           value="{{ old('proposed_date', isset($helpRequest) ? \Carbon\Carbon::parse($helpRequest->proposed_date)->format('Y-m-d\TH:i') : '') }}" 
                           required min="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('proposed_date') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">When would you like to have the session?</p>
                    @error('proposed_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session Type *</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition
                            {{ old('type', $helpRequest->type ?? '') == 'online' ? 'border-purple-500 bg-purple-50' : 'border-gray-200' }}">
                            <input type="radio" name="type" value="online" required
                                   {{ old('type', $helpRequest->type ?? '') == 'online' ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="flex-1">
                                <div class="text-3xl mb-2">💻</div>
                                <p class="font-semibold text-gray-800">Online</p>
                                <p class="text-sm text-gray-600">Virtual meeting</p>
                            </div>
                        </label>
                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition
                            {{ old('type', $helpRequest->type ?? '') == 'in-person' ? 'border-purple-500 bg-purple-50' : 'border-gray-200' }}">
                            <input type="radio" name="type" value="in-person" required
                                   {{ old('type', $helpRequest->type ?? '') == 'in-person' ? 'checked' : '' }}
                                   class="sr-only">
                            <div class="flex-1">
                                <div class="text-3xl mb-2">👥</div>
                                <p class="font-semibold text-gray-800">In-Person</p>
                                <p class="text-sm text-gray-600">Face to face</p>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mentor Selection (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prefer a specific mentor? (Optional)</label>
                    <select name="mentor_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Any available mentor</option>
                        @if(isset($mentors))
                            @foreach($mentors as $mentor)
                                <option value="{{ $mentor->id }}" 
                                        {{ old('mentor_id', $helpRequest->mentor_id ?? '') == $mentor->id ? 'selected' : '' }}>
                                    {{ $mentor->name }} 
                                    @if($mentor->mentor)
                                        ({{ $mentor->mentor->modules }})
                                    @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Leave blank to let the system find the best match</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 flex space-x-4">
                <button type="submit" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                    {{ isset($helpRequest) ? 'Update Request' : 'Submit Request' }}
                </button>
                <a href="{{ isset($helpRequest) ? route('help-requests.show', $helpRequest) : route('help-requests.index') }}" 
                   class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold text-center hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Help Tips -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-6 rounded">
        <h3 class="font-bold text-blue-900 mb-2">💡 Tips for a great help request:</h3>
        <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
            <li>Be specific about what you're struggling with</li>
            <li>Mention any concepts you've already tried</li>
            <li>Include relevant context about your current level</li>
            <li>Propose a realistic time that works for you</li>
        </ul>
    </div>
</div>
@endsection