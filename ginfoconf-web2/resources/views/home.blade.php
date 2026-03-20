@extends('layouts.app')

@section('title', 'Home')

@section('content')

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full filter blur-3xl"></div>
        <div class="absolute bottom-10 right-20 w-96 h-96 bg-purple-300 rounded-full filter blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
        <h1 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight">
            GInfoConf
        </h1>
        <p class="text-xl md:text-2xl text-indigo-100 max-w-3xl mx-auto mb-10">
            The modern platform for academic conference management. Submit papers, review submissions, and manage conferences — all in one place.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('conferences') }}"
               class="bg-white text-indigo-700 px-8 py-3 rounded-xl font-semibold hover:bg-indigo-50 transition-colors text-lg shadow-lg">
                Browse Conferences
            </a>
            @if(!session('api_token'))
                <a href="{{ route('register') }}"
                   class="border-2 border-white text-white px-8 py-3 rounded-xl font-semibold hover:bg-white hover:text-indigo-700 transition-colors text-lg">
                    Get Started
                </a>
            @else
                <a href="{{ route('author.dashboard') }}"
                   class="border-2 border-white text-white px-8 py-3 rounded-xl font-semibold hover:bg-white hover:text-indigo-700 transition-colors text-lg">
                    My Dashboard
                </a>
            @endif
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Everything You Need</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 text-center hover:shadow-md transition-shadow">
            <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Submit Papers</h3>
            <p class="text-gray-600">Easily submit your research papers to conferences. Track submission status and manage revisions in real time.</p>
        </div>

        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 text-center hover:shadow-md transition-shadow">
            <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Review Submissions</h3>
            <p class="text-gray-600">Participate as a reviewer. Accept or decline assignments, provide detailed feedback, and submit evaluations.</p>
        </div>

        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 text-center hover:shadow-md transition-shadow">
            <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Manage Conferences</h3>
            <p class="text-gray-600">Full conference lifecycle management: set up tracks, invite reviewers, manage assignments, and publish decisions.</p>
        </div>
    </div>
</section>

<!-- Recent Conferences -->
@if(!empty($conferences))
<section class="bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Recent Conferences</h2>
            <a href="{{ route('conferences') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                View all →
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($conferences as $conf)
            @php
                $status = $conf['status'] ?? 'draft';
            @endphp
            <a href="{{ route('conference', $conf['slug'] ?? '#') }}"
               class="bg-gray-50 rounded-xl p-6 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-200 transition-all group">
                <div class="flex items-start justify-between mb-3">
                    <span class="badge badge-{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                </div>
                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-700 text-lg mb-2 line-clamp-2">
                    {{ $conf['title'] ?? 'Untitled Conference' }}
                </h3>
                @if(!empty($conf['location']))
                    <p class="text-gray-500 text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        {{ $conf['location'] }}
                    </p>
                @endif
                @if(!empty($conf['submission_close']))
                    <p class="text-gray-500 text-sm mt-1">
                        Deadline: {{ \Carbon\Carbon::parse($conf['submission_close'])->format('M d, Y') }}
                    </p>
                @endif
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="bg-indigo-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
        <p class="text-indigo-100 text-lg mb-8 max-w-2xl mx-auto">
            Join the GInfoConf platform and streamline your conference participation experience.
        </p>
        @if(!session('api_token'))
            <a href="{{ route('register') }}"
               class="bg-white text-indigo-700 px-8 py-3 rounded-xl font-semibold hover:bg-indigo-50 transition-colors text-lg inline-block">
                Create an Account
            </a>
        @endif
    </div>
</section>

@endsection
