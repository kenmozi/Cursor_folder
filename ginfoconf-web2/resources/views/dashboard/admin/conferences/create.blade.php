@extends('layouts.dashboard')

@section('title', 'New Conference')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.conferences') }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Create Conference</h1>
        <p class="text-gray-500 text-sm mt-1">Set up a new academic conference</p>
    </div>

    <form method="POST" action="{{ route('admin.conferences.store') }}" class="space-y-6">
        @csrf

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Basic Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <h2 class="font-semibold text-gray-900">Basic Information</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500">
                    <span class="bg-gray-50 px-3 py-2.5 text-sm text-gray-500 border-r border-gray-300">/conferences/</span>
                    <input type="text" name="slug" value="{{ old('slug') }}" required
                           pattern="[a-z0-9\-]+" placeholder="my-conference-2025"
                           class="flex-1 px-3 py-2.5 text-sm outline-none @error('slug') bg-red-50 @enderror">
                </div>
                <p class="text-xs text-gray-400 mt-1">Lowercase letters, numbers, and hyphens only</p>
                @error('slug')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title (English) <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-400 @enderror">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                <input type="text" name="subtitle" value="{{ old('subtitle') }}"
                       placeholder="Annual Conference on…"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Review Settings -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <h2 class="font-semibold text-gray-900">Review Settings</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Blind Mode <span class="text-red-500">*</span></label>
                    <select name="blind_mode" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select…</option>
                        <option value="double" {{ old('blind_mode') === 'double' ? 'selected' : '' }}>Double-blind</option>
                        <option value="single" {{ old('blind_mode') === 'single' ? 'selected' : '' }}>Single-blind</option>
                        <option value="open" {{ old('blind_mode') === 'open' ? 'selected' : '' }}>Open review</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Timezone <span class="text-red-500">*</span></label>
                    <select name="timezone" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select…</option>
                        <option value="UTC" {{ old('timezone') === 'UTC' ? 'selected' : '' }}>UTC</option>
                        <option value="America/New_York" {{ old('timezone') === 'America/New_York' ? 'selected' : '' }}>Eastern Time (ET)</option>
                        <option value="America/Chicago" {{ old('timezone') === 'America/Chicago' ? 'selected' : '' }}>Central Time (CT)</option>
                        <option value="America/Los_Angeles" {{ old('timezone') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (PT)</option>
                        <option value="Europe/London" {{ old('timezone') === 'Europe/London' ? 'selected' : '' }}>London (GMT/BST)</option>
                        <option value="Europe/Paris" {{ old('timezone') === 'Europe/Paris' ? 'selected' : '' }}>Paris (CET/CEST)</option>
                        <option value="Asia/Tokyo" {{ old('timezone') === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo (JST)</option>
                        <option value="Asia/Shanghai" {{ old('timezone') === 'Asia/Shanghai' ? 'selected' : '' }}>Beijing (CST)</option>
                        <option value="Australia/Sydney" {{ old('timezone') === 'Australia/Sydney' ? 'selected' : '' }}>Sydney (AEST)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Important Dates -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <h2 class="font-semibold text-gray-900">Important Dates</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Submission Opens</label>
                    <input type="datetime-local" name="submission_open" value="{{ old('submission_open') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Submission Closes</label>
                    <input type="datetime-local" name="submission_close" value="{{ old('submission_close') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Review Opens</label>
                    <input type="datetime-local" name="review_open" value="{{ old('review_open') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Review Closes</label>
                    <input type="datetime-local" name="review_close" value="{{ old('review_close') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Author Notification</label>
                    <input type="datetime-local" name="notification_date" value="{{ old('notification_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Camera-Ready Date</label>
                    <input type="datetime-local" name="camera_ready_date" value="{{ old('camera_ready_date') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.conferences') }}"
               class="border border-gray-300 text-gray-600 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                Cancel
            </a>
            <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                Create Conference
            </button>
        </div>

    </form>
</div>
@endsection
