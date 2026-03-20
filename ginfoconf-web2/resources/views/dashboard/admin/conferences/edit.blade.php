@extends('layouts.dashboard')

@section('title', 'Edit Conference')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.conferences.show', $conference['slug']) }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Conference
        </a>
    </div>

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Conference</h1>
        <p class="text-gray-500 text-sm mt-1 font-mono">{{ $conference['slug'] }}</p>
    </div>

    <form method="POST" action="{{ route('admin.conferences.update', $conference['slug']) }}" class="space-y-6">
        @csrf
        @method('PUT')

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
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $conference['title']) }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $conference['subtitle'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('description', $conference['description'] ?? '') }}</textarea>
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
                        <option value="double" {{ old('blind_mode', $conference['blind_mode'] ?? '') === 'double' ? 'selected' : '' }}>Double-blind</option>
                        <option value="single" {{ old('blind_mode', $conference['blind_mode'] ?? '') === 'single' ? 'selected' : '' }}>Single-blind</option>
                        <option value="open" {{ old('blind_mode', $conference['blind_mode'] ?? '') === 'open' ? 'selected' : '' }}>Open review</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Timezone <span class="text-red-500">*</span></label>
                    <select name="timezone" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @php
                            $timezones = ['UTC' => 'UTC', 'America/New_York' => 'Eastern Time (ET)', 'America/Chicago' => 'Central Time (CT)', 'America/Los_Angeles' => 'Pacific Time (PT)', 'Europe/London' => 'London (GMT/BST)', 'Europe/Paris' => 'Paris (CET/CEST)', 'Asia/Tokyo' => 'Tokyo (JST)', 'Asia/Shanghai' => 'Beijing (CST)', 'Australia/Sydney' => 'Sydney (AEST)'];
                            $currentTz = old('timezone', $conference['timezone'] ?? 'UTC');
                        @endphp
                        @foreach($timezones as $val => $label)
                            <option value="{{ $val }}" {{ $currentTz === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.conferences.show', $conference['slug']) }}"
               class="border border-gray-300 text-gray-600 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                Cancel
            </a>
            <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                Save Changes
            </button>
        </div>
    </form>

</div>
@endsection
