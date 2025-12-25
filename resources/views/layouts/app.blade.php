<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mentoring System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Manrope', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold">MentorHub</a>
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('dashboard') }}" class="hover:text-gray-200 transition">Dashboard</a>
                        {{-- <a href="{{ route('users.mentors') }}" class="hover:text-gray-200 transition">Mentors</a> --}}
                        <a href="{{ route('sessions.index') }}" class="hover:text-gray-200 transition">Sessions</a>
                        {{-- <a href="{{ route('help-requests.index') }}" class="hover:text-gray-200 transition">Help Requests</a> --}}
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-sm">{{ Auth::user()->name }}</span>
                        <span class="bg-white text-purple-600 px-3 py-1 rounded-full text-sm font-semibold">{{ Auth::user()->points }} pts</span>
                        <a href="{{ route('users.profile') }}" class="hover:text-gray-200 transition">Profile</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-gray-200 transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-gray-200 transition">Login</a>
                        <a href="{{ route('register') }}" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded">
                {{ session('info') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16 py-8">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2024 MentorHub. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>