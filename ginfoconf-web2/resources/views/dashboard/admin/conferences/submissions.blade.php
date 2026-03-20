@extends('layouts.dashboard')

@section('title', 'Submissions')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.conferences.show', $slug) }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Submissions</h1>
    </div>

    <!-- Status Filter -->
    <div class="flex flex-wrap gap-2">
        @php
            $statuses = ['' => 'All', 'submitted' => 'Submitted', 'under_review' => 'Under Review', 'accepted' => 'Accepted', 'rejected' => 'Rejected', 'revision_required' => 'Revision Required'];
        @endphp
        @foreach($statuses as $val => $label)
            <a href="?status={{ $val }}"
               class="{{ $currentStatus == $val ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }} border px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if(empty($submissions))
            <div class="text-center py-12 text-gray-400">
                <p>No submissions found.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Title</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Authors</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Submitted</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($submissions as $sub)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 text-sm">{{ Str::limit($sub['title'] ?? 'Untitled', 55) }}</p>
                            @if(!empty($sub['track']))
                                <p class="text-xs text-gray-400">{{ is_array($sub['track']) ? $sub['track']['name'] : $sub['track'] }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                            @if(!empty($sub['authors']))
                                {{ collect($sub['authors'])->pluck('name')->take(2)->implode(', ') }}
                                @if(count($sub['authors']) > 2)
                                    <span class="text-gray-400">+{{ count($sub['authors']) - 2 }}</span>
                                @endif
                            @else
                                —
                            @endif
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
                            <a href="{{ route('admin.conferences.submissions.show', [$slug, $sub['id']]) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

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
