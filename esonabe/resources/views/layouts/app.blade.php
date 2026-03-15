<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'e-SONABE') }} — @yield('title', __('messages.dashboard'))</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root { --brand: #0d47a1; --brand-light: #1565c0; --brand-accent: #f57c00; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white transition-all; }
        .sidebar-link.active { @apply bg-white/15 text-white; }
        .stat-card { @apply bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-2; }
        .badge-pending  { @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800; }
        .badge-paid     { @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800; }
        .badge-overdue  { @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800; }
        .badge-active   { @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800; }
        .badge-inactive { @apply inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600; }
        .btn-primary    { @apply inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors; }
        .btn-secondary  { @apply inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors; }
        .btn-danger     { @apply inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors; }
        .btn-success    { @apply inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors; }
        .form-input     { @apply w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500; }
        .form-label     { @apply block text-sm font-medium text-gray-700 mb-1; }
        .card           { @apply bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden; }
        .card-header    { @apply px-6 py-4 border-b border-gray-100 flex items-center justify-between; }
        .card-body      { @apply p-6; }
        .table-base     { @apply min-w-full divide-y divide-gray-100; }
        .th             { @apply px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider; }
        .td             { @apply px-4 py-3 text-sm text-gray-700; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

<div class="flex h-screen overflow-hidden">
    {{-- Sidebar --}}
    <aside class="w-64 flex-shrink-0 flex flex-col" style="background: linear-gradient(180deg,#0d47a1 0%,#01579b 100%);">
        {{-- Logo --}}
        <div class="px-6 py-5 flex items-center gap-3 border-b border-white/10">
            <div class="w-9 h-9 bg-orange-500 rounded-lg flex items-center justify-center text-white font-bold text-lg">⚡</div>
            <div>
                <div class="text-white font-bold text-lg leading-none">e-SONABE</div>
                <div class="text-blue-200 text-xs">Énergie & Services</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                {{ __('messages.dashboard') }}
            </a>
            <a href="{{ route('contracts.index') }}" class="sidebar-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('messages.contracts') }}
            </a>
            <a href="{{ route('bills.index') }}" class="sidebar-link {{ request()->routeIs('bills.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                {{ __('messages.bills') }}
            </a>
            <a href="{{ route('cashpower.index') }}" class="sidebar-link {{ request()->routeIs('cashpower.*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                {{ __('messages.cashpower') }}
            </a>
        </nav>

        {{-- User + Lang switch --}}
        <div class="px-3 py-4 border-t border-white/10 space-y-2">
            {{-- Language toggle --}}
            <div class="flex items-center gap-2 px-2">
                <span class="text-blue-200 text-xs">{{ __('messages.language') }}:</span>
                <a href="{{ route('locale.switch', 'fr') }}" class="text-xs px-2 py-1 rounded {{ app()->getLocale() === 'fr' ? 'bg-white text-blue-800 font-bold' : 'text-white hover:bg-white/20' }}">FR</a>
                <a href="{{ route('locale.switch', 'en') }}" class="text-xs px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-white text-blue-800 font-bold' : 'text-white hover:bg-white/20' }}">EN</a>
            </div>
            <a href="{{ route('profile.edit') }}" class="sidebar-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="truncate">{{ Auth::user()->name }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-full text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    {{ __('messages.logout') }}
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-h-0 overflow-y-auto">
        {{-- Top bar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div>
                @hasSection('title')
                    <h1 class="text-xl font-bold text-gray-900">@yield('title')</h1>
                @else
                    <h1 class="text-xl font-bold text-gray-900">{{ __('messages.dashboard') }}</h1>
                @endif
                @hasSection('subtitle')
                    <p class="text-sm text-gray-500 mt-0.5">@yield('subtitle')</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @yield('header-actions')
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <main class="flex-1 px-6 py-4 pb-8">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
