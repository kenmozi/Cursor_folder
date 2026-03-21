@extends('layouts.dashboard')

@section('title', 'Edit Conference')

@push('head')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
@endpush

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

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.conferences.update', $conference['slug']) }}" class="space-y-6" id="edit-form">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <!-- Basic Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <h2 class="font-semibold text-gray-900 text-lg">Basic Information</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $conference['title'] ?? '') }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                <input type="text" name="subtitle" value="{{ old('subtitle', $conference['subtitle'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Conference Overview (WYSIWYG) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Conference Overview</label>
                <div id="description-editor" class="border border-gray-300 rounded-lg bg-white" style="min-height: 200px;"></div>
                <input type="hidden" name="description" id="description-input" value="{{ old('description', $conference['description'] ?? '') }}">
            </div>

            <!-- Call for Papers (WYSIWYG) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Call for Papers</label>
                <div id="cfp-editor" class="border border-gray-300 rounded-lg bg-white" style="min-height: 180px;"></div>
                <input type="hidden" name="cfp_text" id="cfp-input" value="{{ old('cfp_text', $conference['cfp_text'] ?? '') }}">
            </div>

            <!-- Publication & Submission Guidelines (WYSIWYG) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Publication & Submission Guidelines</label>
                <div id="guidelines-editor" class="border border-gray-300 rounded-lg bg-white" style="min-height: 180px;"></div>
                <input type="hidden" name="publication_guidelines" id="guidelines-input" value="{{ old('publication_guidelines', $conference['publication_guidelines'] ?? '') }}">
            </div>
        </div>

        <!-- Location & Contact -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <h2 class="font-semibold text-gray-900 text-lg">Location & Contact</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" value="{{ old('city', $conference['city'] ?? '') }}"
                           placeholder="Vienna, Austria"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Venue / Location</label>
                    <input type="text" name="location" value="{{ old('location', $conference['location'] ?? '') }}"
                           placeholder="Vienna University, Auditorium A"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                    <input type="url" name="website_url" value="{{ old('website_url', $conference['website_url'] ?? '') }}"
                           placeholder="https://myconference.org"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <hr class="border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Main Contact Person</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Name</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name', $conference['contact_name'] ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $conference['contact_email'] ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $conference['contact_phone'] ?? '') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="contact_address" value="{{ old('contact_address', $conference['contact_address'] ?? '') }}"
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
                    <select name="blind_mode" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @php $bm = old('blind_mode', $conference['blind_mode'] ?? 'double'); @endphp
                        <option value="double" {{ $bm === 'double' ? 'selected' : '' }}>Double-blind</option>
                        <option value="single" {{ $bm === 'single' ? 'selected' : '' }}>Single-blind</option>
                        <option value="open" {{ $bm === 'open' ? 'selected' : '' }}>Open review</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Timezone <span class="text-red-500">*</span></label>
                    <select name="timezone" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @php $timezones = ['UTC'=>'UTC','America/New_York'=>'Eastern Time (ET)','America/Chicago'=>'Central Time (CT)','America/Los_Angeles'=>'Pacific Time (PT)','Europe/London'=>'London (GMT/BST)','Europe/Paris'=>'Paris (CET/CEST)','Asia/Tokyo'=>'Tokyo (JST)','Asia/Shanghai'=>'Beijing (CST)','Australia/Sydney'=>'Sydney (AEST)']; $currentTz = old('timezone', $conference['timezone'] ?? 'UTC'); @endphp
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
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors font-semibold">
                Save Changes
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
        ['link'], ['clean']
    ];

    const descriptionEditor = new Quill('#description-editor', {
        theme: 'snow', modules: { toolbar: toolbarOptions },
    });
    const cfpEditor = new Quill('#cfp-editor', {
        theme: 'snow', modules: { toolbar: toolbarOptions },
    });
    const guidelinesEditor = new Quill('#guidelines-editor', {
        theme: 'snow', modules: { toolbar: toolbarOptions },
    });

    const descVal = document.getElementById('description-input').value;
    if (descVal) descriptionEditor.root.innerHTML = descVal;
    const cfpVal = document.getElementById('cfp-input').value;
    if (cfpVal) cfpEditor.root.innerHTML = cfpVal;
    const guidelinesVal = document.getElementById('guidelines-input').value;
    if (guidelinesVal) guidelinesEditor.root.innerHTML = guidelinesVal;

    document.getElementById('edit-form').addEventListener('submit', function () {
        document.getElementById('description-input').value = descriptionEditor.root.innerHTML;
        document.getElementById('cfp-input').value = cfpEditor.root.innerHTML;
        document.getElementById('guidelines-input').value = guidelinesEditor.root.innerHTML;
    });
</script>
@endsection
