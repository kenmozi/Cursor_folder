@extends('layouts.dashboard')

@section('title', 'Submission Detail')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.conferences.submissions', $slug) }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Submissions
        </a>
    </div>

    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="badge badge-{{ $submission['status'] ?? 'draft' }}">
                    {{ ucfirst(str_replace('_', ' ', $submission['status'] ?? 'draft')) }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $submission['title'] ?? 'Untitled' }}</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Abstract -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-3">Abstract</h2>
                <p class="text-gray-700 text-sm leading-relaxed">{{ $submission['abstract'] ?? 'No abstract.' }}</p>
            </div>

            <!-- Authors -->
            @if(!empty($submission['authors']))
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Authors</h2>
                </div>
                <table class="w-full">
                    <tbody class="divide-y divide-gray-100">
                        @foreach($submission['authors'] as $author)
                        <tr>
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $author['name'] ?? '' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500">{{ $author['email'] ?? '' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-400">{{ $author['affiliation'] ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Reviews -->
            @if(!empty($submission['assignments']))
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Review Assignments</h2>
                <div class="space-y-3">
                    @foreach($submission['assignments'] as $asgn)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-sm text-gray-900">
                                Reviewer: {{ $asgn['reviewer']['name'] ?? 'Unknown' }}
                            </p>
                            <span class="text-xs {{ ($asgn['status'] ?? '') === 'completed' ? 'text-green-600' : 'text-yellow-600' }} font-medium">
                                {{ ucfirst($asgn['status'] ?? 'pending') }}
                            </span>
                        </div>
                        @if(!empty($asgn['review']['score']))
                            <p class="text-sm text-gray-500 mt-1">Score: <strong>{{ $asgn['review']['score'] }}/10</strong> · {{ ucfirst(str_replace('_', ' ', $asgn['review']['recommendation'] ?? '')) }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="space-y-5">

            <!-- Decision Form -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Record Decision</h2>
                <form method="POST" action="{{ route('admin.conferences.submissions.decision', [$slug, $submission['id']]) }}"
                      class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Decision</label>
                        <select name="status" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select decision…</option>
                            <option value="accepted">Accept</option>
                            <option value="rejected">Reject</option>
                            <option value="revision_required">Revision Required</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note (optional)</label>
                        <textarea name="note" rows="3"
                                  placeholder="Decision rationale…"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="notify" value="1" checked
                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-600">Notify authors by email</span>
                    </label>

                    <button type="submit"
                            class="w-full bg-indigo-600 text-white py-2.5 rounded-lg hover:bg-indigo-700 transition-colors font-semibold text-sm"
                            onclick="return confirm('Record this decision?')">
                        Record Decision
                    </button>
                </form>
            </div>

            <!-- Submission Details -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Details</h2>
                <dl class="space-y-3 text-sm">
                    @if(!empty($submission['track']))
                        <div>
                            <dt class="text-gray-500">Track</dt>
                            <dd class="font-medium">{{ is_array($submission['track']) ? ($submission['track']['name'] ?? '') : $submission['track'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($submission['keywords']))
                        <div>
                            <dt class="text-gray-500">Keywords</dt>
                            <dd class="font-medium">{{ is_array($submission['keywords']) ? implode(', ', $submission['keywords']) : $submission['keywords'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($submission['created_at']))
                        <div>
                            <dt class="text-gray-500">Submitted</dt>
                            <dd class="font-medium">{{ \Carbon\Carbon::parse($submission['created_at'])->format('M d, Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

        </div>

    </div>

</div>
@endsection
