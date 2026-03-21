@extends('layouts.dashboard')

@section('title', 'New Conference')

@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endpush

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

    <form method="POST" action="{{ route('admin.conferences.store') }}" class="space-y-6" id="conference-form">
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
            <h2 class="font-semibold text-gray-900 text-lg">Basic Information</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500">
                    <span class="bg-gray-50 px-3 py-2.5 text-sm text-gray-500 border-r border-gray-300">/conferences/</span>
                    <input type="text" name="slug" value="{{ old('slug') }}" required
                           pattern="[a-z0-9\-]+" placeholder="my-conference-2025"
                           class="flex-1 px-3 py-2.5 text-sm outline-none @error('slug') bg-red-50 @enderror">
                </div>
                <p class="text-xs text-gray-400 mt-1">Lowercase letters, numbers, and hyphens only</p>
                @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title (English) <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-400 @enderror">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                <input type="text" name="subtitle" value="{{ old('subtitle') }}"
                       placeholder="Annual Conference on…"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Conference Overview (WYSIWYG) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Conference Overview</label>
                <p class="text-xs text-gray-400 mb-2">Rich description shown to attendees and authors on the conference page.</p>
                <div id="description-editor" class="border border-gray-300 rounded-lg bg-white" style="min-height: 200px;"></div>
                <input type="hidden" name="description" id="description-input" value="{{ old('description') }}">
            </div>

            <!-- Call for Papers (WYSIWYG) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Call for Papers</label>
                <p class="text-xs text-gray-400 mb-2">Describe the scope, topics and submission instructions.</p>
                <div id="cfp-editor" class="border border-gray-300 rounded-lg bg-white" style="min-height: 180px;"></div>
                <input type="hidden" name="cfp_text" id="cfp-input" value="{{ old('cfp_text') }}">
            </div>
        </div>

        <!-- Location & Contact -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <h2 class="font-semibold text-gray-900 text-lg">Location & Contact</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                           placeholder="Vienna, Austria"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Venue / Location</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                           placeholder="Vienna University, Auditorium A"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                    <input type="url" name="website_url" value="{{ old('website_url') }}"
                           placeholder="https://myconference.org"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <hr class="border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Main Contact Person</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Name</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name') }}"
                           placeholder="Dr. Jane Smith"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                           placeholder="contact@conference.org"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                           placeholder="+1 (555) 000-0000"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="contact_address" value="{{ old('contact_address') }}"
                           placeholder="123 University Ave, Vienna, Austria"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Review Settings -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <h2 class="font-semibold text-gray-900 text-lg">Review Settings</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Blind Mode <span class="text-red-500">*</span></label>
                    <select name="blind_mode" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select…</option>
                        <option value="double" {{ old('blind_mode', 'double') === 'double' ? 'selected' : '' }}>Double-blind</option>
                        <option value="single" {{ old('blind_mode') === 'single' ? 'selected' : '' }}>Single-blind</option>
                        <option value="open" {{ old('blind_mode') === 'open' ? 'selected' : '' }}>Open review</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Timezone <span class="text-red-500">*</span></label>
                    <select name="timezone" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select…</option>
                        <option value="UTC" {{ old('timezone', 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
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
            <h2 class="font-semibold text-gray-900 text-lg">Important Dates</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach([
                    'submission_open'   => 'Submission Opens',
                    'submission_close'  => 'Submission Deadline',
                    'review_open'       => 'Review Opens',
                    'review_close'      => 'Review Closes',
                    'notification_date' => 'Author Notification',
                    'camera_ready_date' => 'Camera-Ready Deadline',
                ] as $field => $label)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                    <input type="datetime-local" name="{{ $field }}" value="{{ old($field) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                @endforeach
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

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    const toolbarOptions = [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link'],
        ['clean']
    ];

    const descriptionEditor = new Quill('#description-editor', {
        theme: 'snow',
        modules: { toolbar: toolbarOptions },
        placeholder: 'Write a compelling overview of your conference…',
    });

    const cfpEditor = new Quill('#cfp-editor', {
        theme: 'snow',
        modules: { toolbar: toolbarOptions },
        placeholder: 'Describe topics of interest, submission format, review process…',
    });

    // Pre-fill from old() if validation failed
    const descOld = document.getElementById('description-input').value;
    if (descOld) descriptionEditor.root.innerHTML = descOld;
    const cfpOld = document.getElementById('cfp-input').value;
    if (cfpOld) cfpEditor.root.innerHTML = cfpOld;

    document.getElementById('conference-form').addEventListener('submit', function () {
        document.getElementById('description-input').value = descriptionEditor.root.innerHTML;
        document.getElementById('cfp-input').value = cfpEditor.root.innerHTML;
    });
</script>
@endsection
