@extends('layouts.dashboard')

@section('title', 'Tracks')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.conferences.show', $conference['slug']) }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Conference Tracks</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $conference['title'] ?? '' }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    @php $slug = $conference['slug']; @endphp

    <!-- Current Tracks -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Current Tracks <span class="text-gray-400 font-normal text-sm">({{ count($tracks) }})</span></h2>
        </div>

        @if(empty($tracks))
            <div class="px-6 py-10 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <p class="text-gray-400 text-sm">No tracks added yet. Add your first track below.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($tracks as $track)
                <div class="px-6 py-4 flex items-start justify-between gap-4" x-data="{ editing: false }">
                    <div class="flex-1 min-w-0">
                        <div x-show="!editing">
                            <p class="font-medium text-gray-900 text-sm">{{ $track['name'] ?? $track['slug'] }}</p>
                            @if(!empty($track['description']))
                                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $track['description'] }}</p>
                            @endif
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $track['slug'] }}</p>
                        </div>

                        <form x-show="editing" method="POST"
                              action="{{ route('admin.conferences.tracks.update', [$slug, $track['id']]) }}"
                              class="space-y-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $track['name'] ?? $track['slug'] }}" required
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <textarea name="description" rows="2" placeholder="Track description (optional)"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ $track['description'] ?? '' }}</textarea>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-indigo-700">Save</button>
                                <button type="button" x-on:click="editing = false" class="border border-gray-300 text-gray-600 px-3 py-1.5 rounded-lg text-xs hover:bg-gray-50">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0" x-show="!editing">
                        <button type="button" x-on:click="editing = true"
                                class="text-gray-500 hover:text-indigo-600 text-xs font-medium">Edit</button>
                        <form method="POST" action="{{ route('admin.conferences.tracks.delete', [$slug, $track['id']]) }}">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete track \'{{ $track['name'] ?? $track['slug'] }}\'? This may affect submissions.')"
                                    class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Add Track Form -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 mb-5">Add Track</h2>
        <form method="POST" action="{{ route('admin.conferences.tracks.store', $slug) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Track Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Machine Learning, Computer Vision, NLP…"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400 text-xs">(optional)</span></label>
                <textarea name="description" rows="2"
                          placeholder="Brief description of this track's scope…"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
            </div>
            <button type="submit"
                    class="bg-teal-600 text-white px-5 py-2.5 rounded-lg hover:bg-teal-700 transition-colors font-medium text-sm">
                Add Track
            </button>
        </form>
    </div>

</div>
@endsection
