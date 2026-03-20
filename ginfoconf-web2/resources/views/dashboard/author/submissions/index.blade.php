@extends('layouts.dashboard')

@section('title', 'My Submissions')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Submissions</h1>
            <p class="text-gray-500 text-sm mt-1">Manage all your paper submissions</p>
        </div>
        <a href="{{ route('author.submissions.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Submission
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('author.submissions') }}" class="flex gap-3">
        <select name="status" onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
            <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
            <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if(empty($submissions))
            <div class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-medium">No submissions found</p>
                <a href="{{ route('author.submissions.create') }}" class="text-indigo-600 hover:underline text-sm mt-1 inline-block">Create your first submission</a>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Conference</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Submitted</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($submissions as $sub)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('author.submissions.show', $sub['id']) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                {{ Str::limit($sub['title'] ?? 'Untitled', 60) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                            {{ $sub['conference']['title'] ?? '—' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="badge badge-{{ $sub['status'] ?? 'draft' }}">
                                {{ ucfirst(str_replace('_', ' ', $sub['status'] ?? 'draft')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                            {{ !empty($sub['created_at']) ? \Carbon\Carbon::parse($sub['created_at'])->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('author.submissions.show', $sub['id']) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            @if(!empty($meta['last_page']) && $meta['last_page'] > 1)
                <div class="flex justify-center gap-2 px-6 py-4 border-t border-gray-100">
                    @for($i = 1; $i <= $meta['last_page']; $i++)
                        <a href="?page={{ $i }}&status={{ request('status') }}"
                           class="{{ $meta['current_page'] == $i ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-medium transition-colors">
                            {{ $i }}
                        </a>
                    @endfor
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
