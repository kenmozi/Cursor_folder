@extends('layouts.dashboard')

@section('title', 'Branding')

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
        <h1 class="text-2xl font-bold text-gray-900">Conference Branding</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $conference['title'] ?? '' }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    @php
        $slug    = $conference['slug'];
        $media   = $conference['media'] ?? [];
        $logoUrl = $media['logo'] ?? null;
        $coverUrl = $media['cover'] ?? null;
        $gallery  = $media['gallery'] ?? [];
    @endphp

    <!-- Logo -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h2 class="font-semibold text-gray-900">Logo</h2>

        @if($logoUrl)
            <div class="flex items-center gap-4">
                <img src="{{ $logoUrl }}" alt="Conference Logo" class="w-24 h-24 object-contain rounded-lg border border-gray-200 bg-gray-50">
                <form method="POST" action="{{ route('admin.conferences.media.delete', [$slug, 'logo']) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium" onclick="return confirm('Delete logo?')">
                        Remove Logo
                    </button>
                </form>
            </div>
        @else
            <p class="text-sm text-gray-400">No logo uploaded yet.</p>
        @endif

        <form method="POST" action="{{ route('admin.conferences.media.upload', $slug) }}" enctype="multipart/form-data" class="flex items-center gap-3">
            @csrf
            <input type="hidden" name="type" value="logo">
            <input type="file" name="file" accept="image/jpeg,image/png,image/gif,image/webp" required
                   class="text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium whitespace-nowrap">
                Upload Logo
            </button>
        </form>
        <p class="text-xs text-gray-400">Recommended: square, at least 200×200px. JPEG, PNG, GIF or WebP. Max 5 MB.</p>
    </div>

    <!-- Cover Image -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h2 class="font-semibold text-gray-900">Cover Image <span class="text-xs font-normal text-gray-400 ml-1">— displayed as the event hero banner</span></h2>

        @if($coverUrl)
            <div class="space-y-3">
                <img src="{{ $coverUrl }}" alt="Conference Cover" class="w-full h-48 object-cover rounded-lg border border-gray-200">
                <form method="POST" action="{{ route('admin.conferences.media.delete', [$slug, 'cover']) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium" onclick="return confirm('Delete cover image?')">
                        Remove Cover
                    </button>
                </form>
            </div>
        @else
            <div class="border-2 border-dashed border-gray-200 rounded-lg h-32 flex items-center justify-center">
                <p class="text-sm text-gray-400">No cover image uploaded yet.</p>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.conferences.media.upload', $slug) }}" enctype="multipart/form-data" class="flex items-center gap-3">
            @csrf
            <input type="hidden" name="type" value="cover">
            <input type="file" name="file" accept="image/jpeg,image/png,image/gif,image/webp" required
                   class="text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium whitespace-nowrap">
                Upload Cover
            </button>
        </form>
        <p class="text-xs text-gray-400">Recommended: 1200×400px or wider. JPEG, PNG, GIF or WebP. Max 5 MB.</p>
    </div>

    <!-- Gallery -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Photo Gallery <span class="text-xs font-normal text-gray-400 ml-1">— up to 10 images</span></h2>
            <span class="text-sm text-gray-500">{{ count($gallery) }}/10</span>
        </div>

        @if(!empty($gallery))
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($gallery as $photo)
                    <div class="relative group">
                        <img src="{{ $photo['url'] }}" alt="Gallery photo" class="w-full h-28 object-cover rounded-lg border border-gray-200">
                        <form method="POST" action="{{ route('admin.conferences.gallery.delete', [$slug, $photo['id']]) }}"
                              class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Remove this photo?')"
                                    class="bg-red-600 text-white w-7 h-7 rounded-full flex items-center justify-center hover:bg-red-700 shadow">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="border-2 border-dashed border-gray-200 rounded-lg h-24 flex items-center justify-center">
                <p class="text-sm text-gray-400">No gallery photos yet.</p>
            </div>
        @endif

        @if(count($gallery) < 10)
            <form method="POST" action="{{ route('admin.conferences.gallery.upload', $slug) }}" enctype="multipart/form-data" class="flex items-center gap-3">
                @csrf
                <input type="file" name="file" accept="image/jpeg,image/png,image/gif,image/webp" required
                       class="text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm font-medium whitespace-nowrap">
                    Add Photo
                </button>
            </form>
            <p class="text-xs text-gray-400">JPEG, PNG, GIF or WebP. Max 5 MB per photo. {{ 10 - count($gallery) }} slot(s) remaining.</p>
        @else
            <p class="text-xs text-amber-600 font-medium">Maximum of 10 photos reached. Remove a photo to add a new one.</p>
        @endif
    </div>

</div>
@endsection
