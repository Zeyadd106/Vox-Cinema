<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Admin Dashboard</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-indigo-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <a href="{{ route('admin.dashboard') }}" class="text-white font-bold text-xl">
                                {{ config('app.name') }} Admin
                            </a>
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.movies.index') }}" 
                                   class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">
                                    Movies
                                </a>
                                <a href="{{ route('admin.users.index') }}" 
                                   class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">
                                    Users
                                </a>
                                <a href="{{ route('admin.bookings.index') }}" 
                                   class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">
                                    Bookings
                                </a>
                                <a href="{{ route('admin.settings') }}" 
                                   class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">
                                    Settings
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            <form method="POST" action="{{ route('logout') }}" class="ml-3">
                                @csrf
                                <button type="submit" 
                                        class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-4">
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html> 