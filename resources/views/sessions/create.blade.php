@extends('layouts.app')

@section('title', isset($sessionn) ? 'Edit Session' : 'Create Session')

@section('content')
<div class="mb-8">
    <a href="{{ route('sessions.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">← Back</a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ isset($sessionn) ? 'Edit Session' : 'Create New Session' }}</h1>

        <form action="{{ isset($sessionn) ? route('sessions.update', $sessionn) : route('sessions.store') }}" method="POST">
            @csrf
            @if(isset($sessionn))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mentor *</label>
                    <select name="mentor_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Mentor</option>
                        @foreach($mentors as $mentor)
                            <option value="{{ $mentor->id }}" {{ old('mentor_id', $sessionn->mentor_id ?? '') == $mentor->id ? 'selected' : '' }}>
                                {{ $mentor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mentee *</label>
                    <select name="mentee_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Mentee</option>
                        @foreach($mentees as $mentee)
                            <option value="{{ $mentee->id }}" {{ old('mentee_id', $sessionn->mentee_id ?? '') == $mentee->id ? 'selected' : '' }}>
                                {{ $mentee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Module *</label>
                    <input type="text" name="module" value="{{ old('module', $sessionn->module ?? '') }}" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled At *</label>
                    <input type="datetime-local" name="scheduled_at" 
                           value="{{ old('scheduled_at', isset($sessionn) ? \Carbon\Carbon::parse($sessionn->scheduled_at)->format('Y-m-d\TH:i') : '') }}" 
                           required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                    <select name="type" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="online" {{ old('type', $sessionn->type ?? '') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="in-person" {{ old('type', $sessionn->type ?? '') == 'in-person' ? 'selected' : '' }}>In-Person</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="scheduled" {{ old('status', $sessionn->status ?? 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ old('status', $sessionn->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $sessionn->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="no-show" {{ old('status', $sessionn->status ?? '') == 'no-show' ? 'selected' : '' }}>No Show</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="mentor_notes" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">{{ old('mentor_notes', $sessionn->mentor_notes ?? '') }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex space-x-4">
                <button type="submit" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                    {{ isset($sessionn) ? 'Update Session' : 'Create Session' }}
                </button>
                <a href="{{ route('sessions.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold text-center hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection