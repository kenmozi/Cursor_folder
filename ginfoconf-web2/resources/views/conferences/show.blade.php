@extends('layouts.app')

@section('title', $conference['title'] ?? 'Conference')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('conferences') }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Conferences
            </a>
        </div>

        @php $status = $conference['status'] ?? 'draft'; @endphp
        <div class="flex items-center gap-3 mb-3">
            <span class="badge badge-{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
            @if(!empty($conference['blind_mode']))
                <span class="text-sm text-gray-500">{{ ucfirst($conference['blind_mode']) }}-blind review</span>
            @endif
        </div>

        <h1 class="text-4xl font-bold text-gray-900 mb-3">
            {{ $conference['title'] ?? 'Conference' }}
        </h1>

        @if(!empty($conference['subtitle']))
            <p class="text-xl text-gray-600">{{ $conference['subtitle'] }}</p>
        @endif

        <!-- Submit Paper CTA -->
        @if(session('api_token'))
            <div class="mt-6">
                <a href="{{ route('author.submissions.create') }}"
                   class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Submit a Paper
                </a>
            </div>
        @else
            <div class="mt-6">
                <a href="{{ route('register') }}"
                   class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                    Register to Submit
                </a>
            </div>
        @endif
    </div>

    <!-- Tabs -->
    <div x-data="{ tab: 'overview' }" class="space-y-6">
        <div class="border-b border-gray-200 flex gap-1 overflow-x-auto">
            <button x-on:click="tab = 'overview'"
                    x-bind:class="tab === 'overview' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap">
                Overview
            </button>
            <button x-on:click="tab = 'cfp'"
                    x-bind:class="tab === 'cfp' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap">
                Call for Papers
            </button>
            <button x-on:click="tab = 'dates'"
                    x-bind:class="tab === 'dates' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap">
                Important Dates
            </button>
            <button x-on:click="tab = 'committee'"
                    x-bind:class="tab === 'committee' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap">
                Committee
            </button>
        </div>

        <!-- Overview Tab -->
        <div x-show="tab === 'overview'" class="bg-white rounded-xl border border-gray-200 p-6">
            @if(!empty($conference['description']))
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($conference['description'])) !!}
                </div>
            @else
                <p class="text-gray-400 italic">No description available.</p>
            @endif
        </div>

        <!-- Call for Papers Tab -->
        <div x-show="tab === 'cfp'" class="bg-white rounded-xl border border-gray-200 p-6">
            @if(!empty($conference['call_for_papers']))
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($conference['call_for_papers'])) !!}
                </div>
            @elseif(!empty($conference['topics']))
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">Topics of Interest</h3>
                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                        @foreach($conference['topics'] as $topic)
                            <li>{{ is_array($topic) ? ($topic['name'] ?? '') : $topic }}</li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-gray-400 italic">Call for papers information not yet available.</p>
            @endif
        </div>

        <!-- Important Dates Tab -->
        <div x-show="tab === 'dates'" class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $dates = [
                        'submission_open' => 'Submission Opens',
                        'submission_close' => 'Submission Deadline',
                        'review_open' => 'Review Period Opens',
                        'review_close' => 'Review Period Closes',
                        'notification_date' => 'Author Notification',
                        'camera_ready_date' => 'Camera-Ready Deadline',
                    ];
                @endphp
                @foreach($dates as $key => $label)
                    @if(!empty($conference[$key]))
                        <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                            <svg class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ $label }}</p>
                                <p class="text-gray-900 font-semibold mt-0.5">
                                    {{ \Carbon\Carbon::parse($conference[$key])->format('F d, Y') }}
                                </p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Committee Tab -->
        <div x-show="tab === 'committee'" class="bg-white rounded-xl border border-gray-200 p-6">
            @if(!empty($conference['committee_members']))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($conference['committee_members'] as $member)
                        @if($member['is_public'] ?? true)
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-indigo-600 font-semibold text-sm">
                                        {{ strtoupper(substr($member['name'] ?? 'M', 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $member['name'] ?? '' }}</p>
                                    <p class="text-sm text-gray-500">{{ $member['role'] ?? '' }}</p>
                                    @if(!empty($member['affiliation']))
                                        <p class="text-xs text-gray-400">{{ $member['affiliation'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 italic">Committee information not yet available.</p>
            @endif
        </div>

    </div>
</div>
@endsection
