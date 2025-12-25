@extends('layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Create User')

@section('content')
<div class="mb-8">
    <a href="{{ isset($user) ? route('users.show', $user) : route('users.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">← Back</a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">{{ isset($user) ? 'Edit Profile' : 'Create New User' }}</h1>

        <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password (only for create) -->
                @if(!isset($user))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                @endif

                <!-- Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Level *</label>
                    <select name="level" required 
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('level') border-red-500 @enderror">
                        <option value="">Select Level</option>
                        <option value="Beginner" {{ old('level', $user->level ?? '') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="Intermediate" {{ old('level', $user->level ?? '') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="Advanced" {{ old('level', $user->level ?? '') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                        <option value="Expert" {{ old('level', $user->level ?? '') == 'Expert' ? 'selected' : '' }}>Expert</option>
                    </select>
                    @error('level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                    <textarea name="bio" rows="4" 
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('bio') border-red-500 @enderror">{{ old('bio', $user->bio ?? '') }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Mentor -->
                <div>
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="is_mentor" value="1" {{ old('is_mentor', $user->is_mentor ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                               onchange="document.getElementById('modulesDiv').classList.toggle('hidden')">
                        <span class="text-sm font-medium text-gray-700">This user is a mentor</span>
                    </label>
                </div>

                <!-- Modules (shown if mentor) -->
                <div id="modulesDiv" class="{{ old('is_mentor', $user->is_mentor ?? false) ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Modules (comma-separated)</label>
                    <input type="text" name="modules" value="{{ old('modules', $user->mentor->modules ?? '') }}"
                           placeholder="e.g., Math, Physics, Programming"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="mt-1 text-sm text-gray-500">Enter modules separated by commas</p>
                </div>

                <!-- Points (only for admin) -->
                @if(!isset($user))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Initial Points</label>
                    <input type="number" name="points" value="{{ old('points', 0) }}" min="0"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                @endif
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 flex space-x-4">
                <button type="submit" class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                    {{ isset($user) ? 'Update Profile' : 'Create User' }}
                </button>
                <a href="{{ isset($user) ? route('users.show', $user) : route('users.index') }}" 
                   class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold text-center hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection