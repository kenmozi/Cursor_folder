@extends('layouts.dashboard')

@section('title', 'Author Dashboard')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Author Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Welcome back, {{ session('api_user')['name'] ?? 'Author' }}</p>
        </div>
        <a href="{{ route('author.submissions.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Submission
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Submissions</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Submitted</p>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['submitted'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Under Review</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['under_review'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Accepted</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['accepted'] }}</p>
        </div>
    </div>

    <!-- Recent Submissions -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Recent Submissions</h2>
            <a href="{{ route('author.submissions') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View all</a>
        </div>

        @if(empty($submissions))
            <div class="text-center py-12 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p>No submissions yet.</p>
                <a href="{{ route('author.submissions.create') }}" class="text-indigo-600 hover:underline text-sm mt-1 inline-block">Create your first submission</a>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($submissions as $sub)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('author.submissions.show', $sub['id']) }}" class="font-medium text-gray-900 hover:text-indigo-600 truncate block">
                            {{ $sub['title'] ?? 'Untitled' }}
                        </a>
                        <p class="text-sm text-gray-500">
                            {{ $sub['conference']['title'] ?? 'Unknown Conference' }}
                            @if(!empty($sub['created_at']))
                                · {{ \Carbon\Carbon::parse($sub['created_at'])->format('M d, Y') }}
                            @endif
                        </p>
                    </div>
                    <span class="badge badge-{{ $sub['status'] ?? 'draft' }} ml-4 flex-shrink-0">
                        {{ ucfirst(str_replace('_', ' ', $sub['status'] ?? 'draft')) }}
                    </span>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
