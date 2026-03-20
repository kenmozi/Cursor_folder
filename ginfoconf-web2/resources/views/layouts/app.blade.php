<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GInfoConf') — GInfoConf</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <!-- Navbar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">G</span>
                    </div>
                    <span class="text-xl font-bold text-indigo-600">GInfoConf</span>
                </a>

                <!-- Nav Links -->
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('conferences') }}" class="text-gray-600 hover:text-indigo-600 font-medium transition-colors">
                        Conferences
                    </a>
                    @if(session('api_token'))
                        <a href="{{ route('author.dashboard') }}" class="text-gray-600 hover:text-indigo-600 font-medium transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 font-medium transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                            Register
                        </a>
                    @endif
                </nav>

                <!-- Right side: locale + mobile menu -->
                <div class="flex items-center gap-3">
                    <!-- Locale Switcher -->
                    <div class="flex items-center gap-1 text-sm">
                        <a href="?locale=en" class="{{ session('locale', 'en') === 'en' ? 'font-bold text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">EN</a>
                        <span class="text-gray-300">/</span>
                        <a href="?locale=fr" class="{{ session('locale', 'en') === 'fr' ? 'font-bold text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">FR</a>
                    </div>

                    <!-- Mobile menu button -->
                    <button x-data x-on:click="$dispatch('toggle-mobile-menu')" class="md:hidden p-2 text-gray-600 hover:text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-data="{ open: false }" x-on:toggle-mobile-menu.window="open = !open" x-show="open" class="md:hidden border-t border-gray-200 bg-white px-4 py-3 space-y-2">
            <a href="{{ route('conferences') }}" class="block text-gray-600 hover:text-indigo-600 py-1 font-medium">Conferences</a>
            @if(session('api_token'))
                <a href="{{ route('author.dashboard') }}" class="block text-gray-600 hover:text-indigo-600 py-1 font-medium">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block text-gray-600 hover:text-indigo-600 py-1 font-medium">Login</a>
                <a href="{{ route('register') }}" class="block text-indigo-600 font-medium py-1">Register</a>
            @endif
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if($errors->has('message'))
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ $errors->first('message') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-indigo-600 rounded flex items-center justify-center">
                        <span class="text-white font-bold text-xs">G</span>
                    </div>
                    <span class="text-gray-700 font-semibold">GInfoConf</span>
                </div>
                <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} GInfoConf. All rights reserved.</p>
                <nav class="flex gap-4 text-sm text-gray-500">
                    <a href="{{ route('conferences') }}" class="hover:text-indigo-600">Conferences</a>
                    <a href="{{ route('login') }}" class="hover:text-indigo-600">Login</a>
                </nav>
            </div>
        </div>
    </footer>

</body>
</html>
