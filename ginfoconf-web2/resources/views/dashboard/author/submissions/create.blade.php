@extends('layouts.dashboard')

@section('title', 'New Submission')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('author.submissions') }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    <div>
        <h1 class="text-2xl font-bold text-gray-900">New Submission</h1>
        <p class="text-gray-500 text-sm mt-1">Submit your paper to a conference</p>
    </div>

    <div x-data="{
        step: 0,
        authors: [{ name: '', email: '', affiliation: '', country: '', is_corresponding: true }],
        addAuthor() {
            this.authors.push({ name: '', email: '', affiliation: '', country: '', is_corresponding: false });
        },
        removeAuthor(index) {
            if (this.authors.length > 1) this.authors.splice(index, 1);
        }
    }">

        <!-- Step Indicators -->
        <div class="flex items-center gap-2 mb-6">
            <template x-for="(label, i) in ['Paper Details', 'Authors', 'Review & Submit']" :key="i">
                <div class="flex items-center gap-2">
                    <div x-bind:class="step === i ? 'bg-indigo-600 text-white' : (step > i ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500')"
                         class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">
                        <template x-if="step > i">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <span x-show="step <= i" x-text="i + 1"></span>
                    </div>
                    <span x-text="label" x-bind:class="step === i ? 'font-semibold text-indigo-700' : 'text-gray-500'" class="text-sm hidden sm:inline"></span>
                    <template x-if="i < 2">
                        <div class="w-8 h-px bg-gray-300 mx-1"></div>
                    </template>
                </div>
            </template>
        </div>

        <form method="POST" action="{{ route('author.submissions.store') }}" id="submission-form">
            @csrf

            <!-- Step 0: Paper Details -->
            <div x-show="step === 0" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
                <h2 class="font-semibold text-gray-900 text-lg">Paper Details</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conference <span class="text-red-500">*</span></label>
                    <select name="conference_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('conference_id') border-red-400 @enderror">
                        <option value="">Select a conference…</option>
                        @foreach($conferences as $conf)
                            <option value="{{ $conf['id'] }}" {{ old('conference_id') == $conf['id'] ? 'selected' : '' }}>
                                {{ $conf['title'] ?? 'Conference #' . $conf['id'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('conference_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="Enter the full title of your paper"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-400 @enderror">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Abstract <span class="text-red-500">*</span></label>
                    <textarea name="abstract" rows="6" required
                              placeholder="Provide a concise summary of your paper (150-300 words recommended)…"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none @error('abstract') border-red-400 @enderror">{{ old('abstract') }}</textarea>
                    @error('abstract')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keywords</label>
                    <input type="text" name="keywords" value="{{ old('keywords') }}"
                           placeholder="machine learning, neural networks, optimization (comma-separated)"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Separate keywords with commas</p>
                </div>

                <div class="flex justify-end">
                    <button type="button" x-on:click="step = 1"
                            class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        Next: Authors →
                    </button>
                </div>
            </div>

            <!-- Step 1: Authors -->
            <div x-show="step === 1" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
                <h2 class="font-semibold text-gray-900 text-lg">Authors</h2>
                <p class="text-sm text-gray-500">Add all co-authors in order. Mark the corresponding author.</p>

                <div class="space-y-4">
                    <template x-for="(author, index) in authors" :key="index">
                        <div class="border border-gray-200 rounded-lg p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">
                                    <span x-text="'Author ' + (index + 1)"></span>
                                    <span x-show="author.is_corresponding" class="text-xs text-indigo-600 ml-2">(Corresponding)</span>
                                </span>
                                <button type="button" x-on:click="removeAuthor(index)"
                                        x-show="authors.length > 1"
                                        class="text-red-400 hover:text-red-600 text-sm">Remove</button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Full Name *</label>
                                    <input type="text" x-bind:name="'authors[' + index + '][name]'" x-model="author.name" required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Email *</label>
                                    <input type="email" x-bind:name="'authors[' + index + '][email]'" x-model="author.email" required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Affiliation</label>
                                    <input type="text" x-bind:name="'authors[' + index + '][affiliation]'" x-model="author.affiliation"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Country</label>
                                    <input type="text" x-bind:name="'authors[' + index + '][country]'" x-model="author.country"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-bind:name="'authors[' + index + '][is_corresponding]'"
                                       x-model="author.is_corresponding" value="1"
                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-600">Corresponding author</span>
                            </label>
                        </div>
                    </template>
                </div>

                <button type="button" x-on:click="addAuthor()"
                        class="border border-dashed border-gray-300 text-gray-600 hover:border-indigo-400 hover:text-indigo-600 px-4 py-2 rounded-lg w-full text-sm transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Author
                </button>

                <div class="flex justify-between">
                    <button type="button" x-on:click="step = 0"
                            class="border border-gray-300 text-gray-600 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        ← Back
                    </button>
                    <button type="button" x-on:click="step = 2"
                            class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        Next: Review →
                    </button>
                </div>
            </div>

            <!-- Step 2: Review & Submit -->
            <div x-show="step === 2" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
                <h2 class="font-semibold text-gray-900 text-lg">Review & Submit</h2>
                <p class="text-sm text-gray-500">Please review your submission before submitting.</p>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
                    <p class="font-medium mb-1">Ready to submit?</p>
                    <p>Your submission will be recorded as a draft. You can then upload your manuscript and formally submit it for review from the submission page.</p>
                </div>

                <div class="flex justify-between">
                    <button type="button" x-on:click="step = 1"
                            class="border border-gray-300 text-gray-600 px-6 py-2.5 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        ← Back
                    </button>
                    <button type="submit"
                            class="bg-green-600 text-white px-6 py-2.5 rounded-lg hover:bg-green-700 transition-colors font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Submission
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection
