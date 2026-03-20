@extends('layouts.dashboard')

@section('title', 'Assignments')

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
        <h1 class="text-2xl font-bold text-gray-900">Review Assignments</h1>
    </div>

    <!-- Assign Reviewer Form -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 mb-5">Assign Reviewer</h2>
        <form method="POST" action="{{ route('admin.conferences.assignments.assign', $slug) }}"
              class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Submission <span class="text-red-500">*</span></label>
                <select name="submission_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select submission…</option>
                    @foreach($submissions as $sub)
                        <option value="{{ $sub['id'] }}">{{ Str::limit($sub['title'] ?? 'Untitled', 50) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reviewer <span class="text-red-500">*</span></label>
                <select name="reviewer_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select reviewer…</option>
                    @foreach($reviewers as $rev)
                        <option value="{{ $rev['id'] }}">{{ $rev['name'] ?? $rev['email'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                <input type="date" name="due_date"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="sm:col-span-3">
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm">
                    Assign Reviewer
                </button>
            </div>
        </form>
    </div>

    <!-- Assignments Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Current Assignments ({{ count($assignments) }})</h2>
        </div>

        @if(empty($assignments))
            <p class="px-6 py-8 text-gray-400 text-center text-sm">No assignments yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Paper</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Reviewer</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Due Date</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($assignments as $asgn)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900">
                                {{ Str::limit($asgn['submission']['title'] ?? 'Unknown Paper', 50) }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 hidden md:table-cell">
                            {{ $asgn['reviewer']['name'] ?? $asgn['reviewer']['email'] ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $st = $asgn['status'] ?? 'pending';
                                $bc = match($st) {
                                    'pending' => 'badge-submitted',
                                    'accepted', 'in_progress' => 'badge-under_review',
                                    'completed' => 'badge-accepted',
                                    'declined' => 'badge-rejected',
                                    default => 'badge-draft',
                                };
                            @endphp
                            <span class="badge {{ $bc }}">{{ ucfirst(str_replace('_', ' ', $st)) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                            {{ !empty($asgn['due_date']) ? \Carbon\Carbon::parse($asgn['due_date'])->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @php
                                $subId = $asgn['submission']['id'] ?? $asgn['submission_id'] ?? 0;
                            @endphp
                            <form method="POST"
                                  action="{{ route('admin.conferences.assignments.unassign', [$slug, $subId, $asgn['id']]) }}"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm"
                                        onclick="return confirm('Remove this assignment?')">Unassign</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
