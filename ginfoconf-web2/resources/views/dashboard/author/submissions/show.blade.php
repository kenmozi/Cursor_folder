@extends('layouts.dashboard')

@section('title', $submission['title'] ?? 'Submission')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('author.submissions') }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
                <span class="badge badge-{{ $submission['status'] ?? 'draft' }}">
                    {{ ucfirst(str_replace('_', ' ', $submission['status'] ?? 'draft')) }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $submission['title'] ?? 'Untitled' }}</h1>
            <p class="text-gray-500 text-sm mt-1">
                {{ $submission['conference']['title'] ?? 'Unknown Conference' }}
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 flex-shrink-0">
            @if(($submission['status'] ?? '') === 'draft')
                <form method="POST" action="{{ route('author.submissions.submit', $submission['id']) }}">
                    @csrf
                    <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium"
                            onclick="return confirm('Submit this paper for review?')">
                        Submit Paper
                    </button>
                </form>
            @endif
            @if(in_array($submission['status'] ?? '', ['submitted', 'draft']))
                <form method="POST" action="{{ route('author.submissions.withdraw', $submission['id']) }}">
                    @csrf
                    <button type="submit"
                            class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium"
                            onclick="return confirm('Withdraw this submission?')">
                        Withdraw
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Abstract -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-3">Abstract</h2>
                <p class="text-gray-700 text-sm leading-relaxed">{{ $submission['abstract'] ?? 'No abstract provided.' }}</p>
            </div>

            <!-- Keywords -->
            @if(!empty($submission['keywords']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="font-semibold text-gray-900 mb-3">Keywords</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach(is_array($submission['keywords']) ? $submission['keywords'] : explode(',', $submission['keywords']) as $kw)
                            <span class="bg-indigo-50 text-indigo-700 text-xs px-3 py-1 rounded-full font-medium">{{ trim($kw) }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Authors -->
            @if(!empty($submission['authors']))
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-900">Authors</h2>
                    </div>
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Email</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Affiliation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($submission['authors'] as $author)
                            <tr>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                    {{ $author['name'] ?? '' }}
                                    @if($author['is_corresponding'] ?? false)
                                        <span class="text-xs text-indigo-600 ml-1">(corresponding)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $author['email'] ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $author['affiliation'] ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Files -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Files</h2>
                </div>

                @if(!empty($submission['files']))
                    <div class="divide-y divide-gray-100">
                        @foreach($submission['files'] as $file)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $file['original_name'] ?? $file['filename'] ?? 'File' }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($file['type'] ?? 'document') }}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('author.submissions.file.delete', [$submission['id'], $file['id']]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm" onclick="return confirm('Delete this file?')">
                                    Remove
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="px-6 py-4 text-sm text-gray-400">No files uploaded yet.</p>
                @endif

                <!-- Upload Manuscript -->
                @if(in_array($submission['status'] ?? '', ['draft', 'submitted', 'revision_required']))
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Upload Manuscript (PDF)</h3>
                        <form method="POST" action="{{ route('author.submissions.upload', $submission['id']) }}"
                              enctype="multipart/form-data" class="flex items-center gap-3">
                            @csrf
                            <input type="file" name="file" accept=".pdf" required
                                   class="text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                            <button type="submit"
                                    class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium whitespace-nowrap">
                                Upload
                            </button>
                        </form>
                        <p class="text-xs text-gray-400 mt-1">Maximum file size: 50 MB</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-5">

            <!-- Submission Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Details</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd class="mt-0.5">
                            <span class="badge badge-{{ $submission['status'] ?? 'draft' }}">
                                {{ ucfirst(str_replace('_', ' ', $submission['status'] ?? 'draft')) }}
                            </span>
                        </dd>
                    </div>
                    @if(!empty($submission['track']))
                        <div>
                            <dt class="text-gray-500">Track</dt>
                            <dd class="font-medium text-gray-900">{{ $submission['track']['name'] ?? $submission['track'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($submission['created_at']))
                        <div>
                            <dt class="text-gray-500">Submitted On</dt>
                            <dd class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($submission['created_at'])->format('M d, Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Review Feedback -->
            @if(!empty($submission['reviews']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="font-semibold text-gray-900 mb-4">Reviews</h2>
                    @foreach($submission['reviews'] as $review)
                        <div class="border-t border-gray-100 pt-3 mt-3 first:border-0 first:pt-0 first:mt-0">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Reviewer {{ $loop->iteration }}</span>
                                @if(!empty($review['score']))
                                    <span class="text-sm font-bold text-indigo-600">{{ $review['score'] }}/10</span>
                                @endif
                            </div>
                            @if(!empty($review['recommendation']))
                                <p class="text-xs text-gray-500">Recommendation: <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $review['recommendation'])) }}</span></p>
                            @endif
                            @if(!empty($review['comments']))
                                <p class="text-sm text-gray-600 mt-2">{{ $review['comments'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
