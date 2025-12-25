@extends('layouts.app')

@section('title', 'Help Request Details')

@section('content')
<div class="mb-8">
    <a href="{{ route('help-requests.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold">← Back</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $helpRequest->module }}</h1>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($helpRequest->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($helpRequest->status == 'accepted') bg-blue-100 text-blue-800
                        @elseif($helpRequest->status == 'in-progress') bg-purple-100 text-purple-800
                        @elseif($helpRequest->status == 'resolved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($helpRequest->status) }}
                    </span>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Description</h3>
                    <p class="text-gray-700">{{ $helpRequest->description }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border-l-4 border-purple-500 pl-4">
                        <p class="text-sm text-gray-600">Requested by</p>
                        <p class="font-semibold text-gray-800">{{ $helpRequest->mentee->name }}</p>
                        <p class="text-sm text-gray-500">{{ $helpRequest->mentee->email }}</p>
                    </div>
                    @if($helpRequest->mentor)
                    <div class="border-l-4 border-green-500 pl-4">
                        <p class="text-sm text-gray-600">Assigned to</p>
                        <p class="font-semibold text-gray-800">{{ $helpRequest->mentor->name }}</p>
                        <p class="text-sm text-gray-500">{{ $helpRequest->mentor->email }}</p>
                    </div>
                    @endif
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Proposed Date</p>
                            <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($helpRequest->proposed_date)->format('M d, Y - h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Session Type</p>
                            <p class="font-semibold text-gray-800">{{ ucfirst($helpRequest->type) }}</p>
                        </div>
                    </div>
                </div>

                @if($helpRequest->session)
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                    <p class="text-sm text-gray-600 mb-1">Related Session</p>
                    <a href="{{ route('sessions.show', $helpRequest->session) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                        View Session Details →
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Actions</h3>
            <div class="space-y-3">
                @if($helpRequest->status == 'pending' && Auth::user()->is_mentor)
                <form action="{{ route('help-requests.accept', $helpRequest) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                        ✓ Accept Request
                    </button>
                </form>
                <form action="{{ route('help-requests.reject', $helpRequest) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-red-700 transition">
                        ✕ Reject Request
                    </button>
                </form>
                @endif

                @if(Auth::id() == $helpRequest->mentee_id && $helpRequest->status == 'pending')
                <a href="{{ route('help-requests.edit', $helpRequest) }}" class="block w-full bg-blue-600 text-white text-center px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    ✏️ Edit Request
                </a>
                <form action="{{ route('help-requests.cancel', $helpRequest) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full bg-gray-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-700 transition">
                        Cancel Request
                    </button>
                </form>
                @endif

                @if(in_array($helpRequest->status, ['in-progress', 'accepted']) && (Auth::id() == $helpRequest->mentor_id || Auth::id() == $helpRequest->mentee_id))
                <form action="{{ route('help-requests.resolve', $helpRequest) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full bg-purple-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Mark as Resolved
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection