@extends('layouts.dashboard')

@section('title', $conference['title'] ?? 'Conference')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.conferences') }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Conferences
                </a>
                <span class="badge badge-{{ $conference['status'] ?? 'draft' }}">
                    {{ ucfirst(str_replace('_', ' ', $conference['status'] ?? 'draft')) }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $conference['title'] ?? 'Conference' }}</h1>
            <p class="text-gray-500 font-mono text-sm mt-1">{{ $conference['slug'] ?? '' }}</p>
        </div>
    </div>

    <!-- Navigation Cards -->
    @php $slug = $conference['slug']; @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        <a href="{{ route('admin.conferences.edit', $slug) }}"
           class="bg-white rounded-xl border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-indigo-200 transition-colors">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-700">Edit Settings</h3>
            <p class="text-sm text-gray-500 mt-1">Update title, description, review mode</p>
        </a>

        <a href="{{ route('admin.conferences.branding', $slug) }}"
           class="bg-white rounded-xl border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-purple-200 transition-colors">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-purple-700">Branding</h3>
            <p class="text-sm text-gray-500 mt-1">Upload logo and cover image</p>
        </a>

        <a href="{{ route('admin.conferences.dates', $slug) }}"
           class="bg-white rounded-xl border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-blue-200 transition-colors">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-blue-700">Important Dates</h3>
            <p class="text-sm text-gray-500 mt-1">Set submission and review deadlines</p>
        </a>

        <a href="{{ route('admin.conferences.tracks', $slug) }}"
           class="bg-white rounded-xl border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-teal-200 transition-colors">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-teal-700">Tracks</h3>
            <p class="text-sm text-gray-500 mt-1">Manage conference tracks</p>
        </a>

        <a href="{{ route('admin.conferences.committee', $slug) }}"
           class="bg-white rounded-xl border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-green-200 transition-colors">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-green-700">Committees</h3>
            <p class="text-sm text-gray-500 mt-1">Scientific, Academic, Program & Organizing</p>
        </a>

        <a href="{{ route('admin.conferences.submissions', $slug) }}"
           class="bg-white rounded-xl border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-yellow-200 transition-colors">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-yellow-700">Submissions</h3>
            <p class="text-sm text-gray-500 mt-1">View and manage paper submissions</p>
        </a>

        <a href="{{ route('admin.conferences.reviewers', $slug) }}"
           class="bg-white rounded-xl border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-orange-200 transition-colors">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-orange-700">Reviewers</h3>
            <p class="text-sm text-gray-500 mt-1">Invite and manage reviewers</p>
        </a>

        <a href="{{ route('admin.conferences.assignments', $slug) }}"
           class="bg-white rounded-xl border border-gray-200 p-5 hover:border-indigo-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-pink-200 transition-colors">
                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900 group-hover:text-pink-700">Assignments</h3>
            <p class="text-sm text-gray-500 mt-1">Assign reviewers to submissions</p>
        </a>

    </div>

</div>
@endsection
