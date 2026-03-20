@extends('layouts.dashboard')

@section('title', 'Important Dates')

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
        <h1 class="text-2xl font-bold text-gray-900">Important Dates</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $conference['title'] ?? '' }}</p>
    </div>

    <form method="POST" action="{{ route('admin.conferences.dates.update', $conference['slug']) }}">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Submission Opens</label>
                    <input type="datetime-local" name="submission_open"
                           value="{{ old('submission_open', !empty($conference['submission_open']) ? \Carbon\Carbon::parse($conference['submission_open'])->format('Y-m-d\TH:i') : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Submission Closes</label>
                    <input type="datetime-local" name="submission_close"
                           value="{{ old('submission_close', !empty($conference['submission_close']) ? \Carbon\Carbon::parse($conference['submission_close'])->format('Y-m-d\TH:i') : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Review Opens</label>
                    <input type="datetime-local" name="review_open"
                           value="{{ old('review_open', !empty($conference['review_open']) ? \Carbon\Carbon::parse($conference['review_open'])->format('Y-m-d\TH:i') : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Review Closes</label>
                    <input type="datetime-local" name="review_close"
                           value="{{ old('review_close', !empty($conference['review_close']) ? \Carbon\Carbon::parse($conference['review_close'])->format('Y-m-d\TH:i') : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Author Notification</label>
                    <input type="datetime-local" name="notification_date"
                           value="{{ old('notification_date', !empty($conference['notification_date']) ? \Carbon\Carbon::parse($conference['notification_date'])->format('Y-m-d\TH:i') : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Camera-Ready Deadline</label>
                    <input type="datetime-local" name="camera_ready_date"
                           value="{{ old('camera_ready_date', !empty($conference['camera_ready_date']) ? \Carbon\Carbon::parse($conference['camera_ready_date'])->format('Y-m-d\TH:i') : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.conferences.show', $conference['slug']) }}"
                   class="border border-gray-300 text-gray-600 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                    Save Dates
                </button>
            </div>
        </div>

    </form>

</div>
@endsection
