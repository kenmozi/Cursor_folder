@extends('layouts.dashboard')

@section('title', 'Branding')

@section('content')
<div class="max-w-2xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.conferences.show', $conference['slug']) }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Conference Branding</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $conference['title'] ?? '' }}</p>
    </div>

    @php $slug = $conference['slug']; @endphp

    <!-- Logo -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h2 class="font-semibold text-gray-900">Logo</h2>

        @if(!empty($conference['logo_url']))
            <div class="flex items-center gap-4">
                <img src="{{ $conference['logo_url'] }}" alt="Conference Logo" class="w-24 h-24 object-contain rounded-lg border border-gray-200">
                <form method="POST" action="{{ route('admin.conferences.media.delete', [$slug, 'logo']) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium" onclick="return confirm('Delete logo?')">
                        Remove Logo
                    </button>
                </form>
            </div>
        @else
            <p class="text-sm text-gray-400">No logo uploaded yet.</p>
        @endif

        <form method="POST" action="{{ route('admin.conferences.media.upload', $slug) }}" enctype="multipart/form-data"
              class="flex items-center gap-3">
            @csrf
            <input type="hidden" name="type" value="logo">
            <input type="file" name="file" accept="image/*" required
                   class="text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
            <button type="submit"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium whitespace-nowrap">
                Upload Logo
            </button>
        </form>
        <p class="text-xs text-gray-400">Recommended: square image, at least 200×200px. Max 5 MB.</p>
    </div>

    <!-- Cover Image -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h2 class="font-semibold text-gray-900">Cover Image</h2>

        @if(!empty($conference['cover_url']))
            <div class="space-y-3">
                <img src="{{ $conference['cover_url'] }}" alt="Conference Cover" class="w-full h-40 object-cover rounded-lg border border-gray-200">
                <form method="POST" action="{{ route('admin.conferences.media.delete', [$slug, 'cover']) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium" onclick="return confirm('Delete cover image?')">
                        Remove Cover
                    </button>
                </form>
            </div>
        @else
            <p class="text-sm text-gray-400">No cover image uploaded yet.</p>
        @endif

        <form method="POST" action="{{ route('admin.conferences.media.upload', $slug) }}" enctype="multipart/form-data"
              class="flex items-center gap-3">
            @csrf
            <input type="hidden" name="type" value="cover">
            <input type="file" name="file" accept="image/*" required
                   class="text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
            <button type="submit"
                    class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium whitespace-nowrap">
                Upload Cover
            </button>
        </form>
        <p class="text-xs text-gray-400">Recommended: 1200×400px or wider. Max 5 MB.</p>
    </div>

</div>
@endsection
