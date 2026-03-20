@extends('layouts.dashboard')

@section('title', 'Review Assignment')

@section('content')
<div class="max-w-3xl space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('reviewer.dashboard') }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Assignments
        </a>
    </div>

    <!-- Assignment Info -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        @php
            $status = $assignment['status'] ?? 'pending';
            $submission = $assignment['submission'] ?? [];
        @endphp
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Review Assignment</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $submission['conference']['title'] ?? 'Unknown Conference' }}
                </p>
            </div>
            @php
                $badgeClass = match($status) {
                    'pending' => 'badge-submitted',
                    'accepted', 'in_progress' => 'badge-under_review',
                    'completed' => 'badge-accepted',
                    'declined' => 'badge-rejected',
                    default => 'badge-draft',
                };
            @endphp
            <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
        </div>

        <div class="border-t border-gray-100 pt-4 space-y-3">
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Paper Title</p>
                <p class="font-semibold text-gray-900 mt-0.5">{{ $submission['title'] ?? 'Untitled Paper' }}</p>
            </div>
            @if(!empty($submission['abstract']))
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Abstract</p>
                    <p class="text-sm text-gray-700 mt-0.5 leading-relaxed">{{ $submission['abstract'] }}</p>
                </div>
            @endif
            @if(!empty($assignment['due_date']))
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Review Due</p>
                    <p class="font-semibold text-gray-900 mt-0.5">{{ \Carbon\Carbon::parse($assignment['due_date'])->format('F d, Y') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Pending: Accept / Decline -->
    @if($status === 'pending')
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-4">Respond to Assignment</h2>
            <p class="text-sm text-gray-500 mb-5">Please indicate whether you can review this paper.</p>

            <div class="flex gap-3">
                <form method="POST" action="{{ route('reviewer.assignments.respond', $assignment['id']) }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="action" value="accept">
                    <button type="submit"
                            class="w-full bg-green-600 text-white py-2.5 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                        Accept Assignment
                    </button>
                </form>

                <div x-data="{ open: false }" class="flex-1">
                    <button x-on:click="open = !open"
                            class="w-full border border-red-300 text-red-600 py-2.5 rounded-lg font-semibold hover:bg-red-50 transition-colors">
                        Decline Assignment
                    </button>
                    <div x-show="open" class="mt-3">
                        <form method="POST" action="{{ route('reviewer.assignments.respond', $assignment['id']) }}">
                            @csrf
                            <input type="hidden" name="action" value="decline">
                            <textarea name="reason" rows="3" placeholder="Optional: provide a reason for declining..."
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 mb-2 resize-none"></textarea>
                            <button type="submit"
                                    class="w-full bg-red-600 text-white py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors text-sm">
                                Confirm Decline
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Accepted/In Progress: Review Form -->
    @if(in_array($status, ['accepted', 'in_progress']) && !($review && ($review['submitted_at'] ?? null)))
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-5">Submit Review</h2>

            <form method="POST" action="{{ route('reviewer.assignments.review', $assignment['id']) }}" class="space-y-5">
                @csrf

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Score -->
                <div x-data="{ score: {{ old('score', $review['score'] ?? 5) }} }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Overall Score: <span class="text-indigo-600 font-bold" x-text="score"></span>/10
                    </label>
                    <input type="range" name="score" min="1" max="10" x-model="score"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                    <div class="flex justify-between text-xs text-gray-400 mt-1">
                        <span>1 (Reject)</span>
                        <span>5 (Borderline)</span>
                        <span>10 (Strong Accept)</span>
                    </div>
                </div>

                <!-- Recommendation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recommendation <span class="text-red-500">*</span></label>
                    <select name="recommendation" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select a recommendation…</option>
                        <option value="accept" {{ old('recommendation', $review['recommendation'] ?? '') === 'accept' ? 'selected' : '' }}>Accept</option>
                        <option value="minor_revision" {{ old('recommendation', $review['recommendation'] ?? '') === 'minor_revision' ? 'selected' : '' }}>Minor Revision</option>
                        <option value="major_revision" {{ old('recommendation', $review['recommendation'] ?? '') === 'major_revision' ? 'selected' : '' }}>Major Revision</option>
                        <option value="reject" {{ old('recommendation', $review['recommendation'] ?? '') === 'reject' ? 'selected' : '' }}>Reject</option>
                    </select>
                </div>

                <!-- Comments -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comments to Authors <span class="text-red-500">*</span></label>
                    <textarea name="comments" rows="6" required
                              placeholder="Provide detailed feedback for the authors…"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('comments', $review['comments'] ?? '') }}</textarea>
                </div>

                <!-- Confidential Comments -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confidential Comments (to Chairs only)</label>
                    <textarea name="confidential_comments" rows="3"
                              placeholder="Optional: comments visible only to the program chairs…"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('confidential_comments', $review['confidential_comments'] ?? '') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" name="action" value="draft"
                            class="border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">
                        Save Draft
                    </button>
                    <button type="submit" name="action" value="submit"
                            class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors font-semibold text-sm"
                            onclick="return confirm('Submit your final review? This cannot be undone.')">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Completed: Show Review -->
    @if($status === 'completed' && $review)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-semibold text-gray-900">Submitted Review</h2>
                <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">Submitted</span>
            </div>

            <div class="space-y-4">
                @if(!empty($review['score']))
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Score</p>
                        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $review['score'] }}/10</p>
                    </div>
                @endif
                @if(!empty($review['recommendation']))
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Recommendation</p>
                        <p class="font-medium text-gray-900 mt-1">{{ ucfirst(str_replace('_', ' ', $review['recommendation'])) }}</p>
                    </div>
                @endif
                @if(!empty($review['comments']))
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Comments</p>
                        <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $review['comments'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
