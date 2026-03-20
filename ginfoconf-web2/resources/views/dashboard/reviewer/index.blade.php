@extends('layouts.dashboard')

@section('title', 'Reviewer Dashboard')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Reviewer Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Manage your review assignments</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Pending Response</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">In Progress</p>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Completed</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">All Assignments</h2>
        </div>

        @if(empty($assignments))
            <div class="text-center py-16 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="font-medium">No assignments yet</p>
                <p class="text-sm mt-1">You haven't been assigned any papers to review.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Paper Title</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Conference</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Due Date</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($assignments as $assignment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 text-sm">
                                {{ Str::limit($assignment['submission']['title'] ?? 'Untitled Paper', 60) }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                            {{ $assignment['submission']['conference']['title'] ?? '—' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $status = $assignment['status'] ?? 'pending';
                                $badgeClass = match($status) {
                                    'pending' => 'badge-submitted',
                                    'accepted', 'in_progress' => 'badge-under_review',
                                    'completed' => 'badge-accepted',
                                    'declined' => 'badge-rejected',
                                    default => 'badge-draft',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                            {{ !empty($assignment['due_date']) ? \Carbon\Carbon::parse($assignment['due_date'])->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('reviewer.assignments.show', $assignment['id']) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
