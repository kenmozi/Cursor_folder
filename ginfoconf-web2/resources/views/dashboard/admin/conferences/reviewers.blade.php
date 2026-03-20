@extends('layouts.dashboard')

@section('title', 'Reviewers')

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
        <h1 class="text-2xl font-bold text-gray-900">Reviewers</h1>
    </div>

    <!-- Current Reviewers -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Accepted Reviewers ({{ count($reviewers) }})</h2>
        </div>
        @if(empty($reviewers))
            <p class="px-6 py-8 text-gray-400 text-center text-sm">No reviewers have accepted yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Email</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Affiliation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($reviewers as $reviewer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-900 text-sm">{{ $reviewer['name'] ?? 'Unknown' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $reviewer['email'] ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-500 hidden lg:table-cell">{{ $reviewer['affiliation'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Pending Invitations -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Pending Invitations ({{ count($invitations) }})</h2>
        </div>
        @if(empty($invitations))
            <p class="px-6 py-8 text-gray-400 text-center text-sm">No pending invitations.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Sent</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($invitations as $inv)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $inv['email'] ?? '' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-500 hidden md:table-cell">
                            <span class="badge badge-{{ $inv['status'] === 'pending' ? 'submitted' : ($inv['status'] === 'accepted' ? 'accepted' : 'rejected') }}">
                                {{ ucfirst($inv['status'] ?? 'pending') }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-500 hidden md:table-cell">
                            {{ !empty($inv['created_at']) ? \Carbon\Carbon::parse($inv['created_at'])->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-6 py-3 text-right">
                            @if(($inv['status'] ?? '') === 'pending')
                                <form method="POST" action="{{ route('admin.conferences.invitations.cancel', [$slug, $inv['id']]) }}"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm" onclick="return confirm('Cancel this invitation?')">
                                        Cancel
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Send Invitation Form -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 mb-4">Invite a Reviewer</h2>
        <form method="POST" action="{{ route('admin.conferences.invitations.send', $slug) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email" required placeholder="reviewer@university.edu"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 max-w-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Personal Message (optional)</label>
                <textarea name="message" rows="3"
                          placeholder="We would like to invite you to serve as a reviewer for…"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none max-w-lg"></textarea>
            </div>
            <button type="submit"
                    class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm">
                Send Invitation
            </button>
        </form>
    </div>

</div>
@endsection
