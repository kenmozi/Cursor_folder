@extends('layouts.app')

@section('title', $conference['title'] ?? 'Conference')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- ── HERO COVER ──────────────────────────────────────────────────── --}}
    @if(!empty($conference['cover']))
        <div class="relative w-full" style="height: 360px;">
            <img src="{{ $conference['cover'] }}" alt="{{ $conference['title'] ?? 'Conference' }}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
            <a href="{{ route('conferences') }}"
               class="absolute top-5 left-5 sm:left-8 flex items-center gap-1 text-white/80 hover:text-white text-sm bg-black/30 rounded-full px-3 py-1.5 backdrop-blur-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                All Conferences
            </a>
            <div class="absolute bottom-0 left-0 right-0 px-5 sm:px-8 lg:px-16 pb-8">
                <div class="max-w-5xl mx-auto">
                    @php $status = $conference['status'] ?? 'draft'; @endphp
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white border border-white/30">{{ ucfirst(str_replace('_',' ',$status)) }}</span>
                        @if(!empty($conference['city']))
                            <span class="flex items-center gap-1.5 text-white/90 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $conference['city'] }}
                            </span>
                        @endif
                        @if(!empty($conference['submission_close']))
                            <span class="text-white/80 text-sm">Deadline: {{ \Carbon\Carbon::parse($conference['submission_close'])->format('M d, Y') }}</span>
                        @endif
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-white leading-tight">{{ $conference['title'] ?? 'Conference' }}</h1>
                    @if(!empty($conference['subtitle']))<p class="text-lg text-white/80 mt-2">{{ $conference['subtitle'] }}</p>@endif
                </div>
            </div>
        </div>
    @else
        <div class="bg-indigo-700 px-5 sm:px-8 lg:px-16 py-12">
            <div class="max-w-5xl mx-auto">
                <a href="{{ route('conferences') }}" class="flex items-center gap-1 text-indigo-300 hover:text-white text-sm mb-5 w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    All Conferences
                </a>
                @php $status = $conference['status'] ?? 'draft'; @endphp
                <div class="flex flex-wrap items-center gap-3 mb-3">
                    <span class="badge badge-{{ $status }}">{{ ucfirst(str_replace('_',' ',$status)) }}</span>
                    @if(!empty($conference['city']))
                        <span class="flex items-center gap-1.5 text-indigo-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $conference['city'] }}
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-white">{{ $conference['title'] ?? 'Conference' }}</h1>
                @if(!empty($conference['subtitle']))<p class="text-lg text-indigo-200 mt-2">{{ $conference['subtitle'] }}</p>@endif
            </div>
        </div>
    @endif

    {{-- ── BODY ────────────────────────────────────────────────────────── --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- CTA row --}}
        <div class="mb-8 flex flex-wrap items-center gap-4">
            @if(!empty($conference['is_submission_open']))
                @if(session('api_token'))
                    <a href="{{ route('author.submissions.create') }}"
                       class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Submit a Paper
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors">Register to Submit</a>
                @endif
            @endif
            @if(!empty($conference['website_url']))
                <a href="{{ $conference['website_url'] }}" target="_blank" rel="noopener"
                   class="border border-gray-300 text-gray-700 px-5 py-3 rounded-xl font-medium hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Visit Website
                </a>
            @endif
        </div>

        {{-- Key dates bar --}}
        @php
            $keyDates = array_filter([
                ['label'=>'Submission Deadline','date'=>$conference['submission_close']??null],
                ['label'=>'Author Notification','date'=>$conference['notification_date']??null],
                ['label'=>'Camera-Ready','date'=>$conference['camera_ready_date']??null],
            ], fn($d)=>!empty($d['date']));
        @endphp
        @if(!empty($keyDates))
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-10">
                @foreach($keyDates as $item)
                <div class="bg-white border border-gray-200 rounded-xl px-5 py-4 flex items-start gap-3">
                    <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ $item['label'] }}</p>
                        <p class="text-sm font-bold text-gray-900 mt-0.5">{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

        {{-- Tabs --}}
        <div x-data="{ tab: 'overview' }" class="space-y-6">
            <div class="border-b border-gray-200 flex gap-0 overflow-x-auto">
                @php
                    $tabs = ['overview'=>'Overview','cfp'=>'Call for Papers'];
                    if(!empty($conference['publication_guidelines'])) $tabs['guidelines']='Guidelines';
                    $tabs['dates']='Important Dates';
                    if(!empty($conference['tracks'])) $tabs['tracks']='Tracks';
                    if(!empty($conference['committees'])) $tabs['committee']='Committee';
                    if(!empty($conference['gallery'])) $tabs['gallery']='Gallery';
                    $tabs['contact']='Contact';
                @endphp
                @foreach($tabs as $key=>$label)
                <button x-on:click="tab='{{ $key }}'"
                        x-bind:class="tab==='{{ $key }}' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">{{ $label }}</button>
                @endforeach
            </div>

            {{-- Overview --}}
            <div x-show="tab==='overview'" x-cloak class="bg-white rounded-xl border border-gray-200 p-6">
                @if(!empty($conference['description']))
                    <div class="prose prose-indigo max-w-none text-gray-700">{!! $conference['description'] !!}</div>
                @else
                    <p class="text-gray-400 italic">No description available.</p>
                @endif
            </div>

            {{-- Call for Papers --}}
            <div x-show="tab==='cfp'" x-cloak class="bg-white rounded-xl border border-gray-200 p-6">
                @if(!empty($conference['cfp_text']))
                    <div class="prose prose-indigo max-w-none text-gray-700">{!! $conference['cfp_text'] !!}</div>
                @else
                    <p class="text-gray-400 italic">Call for papers not yet available.</p>
                @endif
            </div>

            {{-- Guidelines --}}
            @if(!empty($conference['publication_guidelines']))
            <div x-show="tab==='guidelines'" x-cloak class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="prose prose-indigo max-w-none text-gray-700">{!! $conference['publication_guidelines'] !!}</div>
            </div>
            @endif

            {{-- Important Dates --}}
            <div x-show="tab==='dates'" x-cloak class="bg-white rounded-xl border border-gray-200 p-6">
                @php
                    $allDates = array_filter([
                        ['label'=>'Submission Opens','date'=>$conference['submission_open']??null],
                        ['label'=>'Submission Deadline','date'=>$conference['submission_close']??null],
                        ['label'=>'Review Period Opens','date'=>$conference['review_open']??null],
                        ['label'=>'Review Period Closes','date'=>$conference['review_close']??null],
                        ['label'=>'Author Notification','date'=>$conference['notification_date']??null],
                        ['label'=>'Camera-Ready Deadline','date'=>$conference['camera_ready_date']??null],
                    ], fn($d)=>!empty($d['date']));
                    $customDates = $conference['dates'] ?? [];
                @endphp
                @if(empty($allDates) && empty($customDates))
                    <p class="text-gray-400 italic">Important dates not yet published.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($allDates as $item)
                        <div class="flex items-start gap-3 p-4 bg-indigo-50 rounded-lg">
                            <svg class="w-5 h-5 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <div>
                                <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide">{{ $item['label'] }}</p>
                                <p class="text-gray-900 font-bold mt-0.5">{{ \Carbon\Carbon::parse($item['date'])->format('F d, Y') }}</p>
                            </div>
                        </div>
                        @endforeach
                        @foreach($customDates as $date)
                            @if(!empty($date['date']) && !empty($date['label']))
                            <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">{{ $date['label'] }}</p>
                                    <p class="text-gray-900 font-bold mt-0.5">{{ \Carbon\Carbon::parse($date['date'])->format('F d, Y') }}</p>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Tracks --}}
            @if(!empty($conference['tracks']))
            <div x-show="tab==='tracks'" x-cloak class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($conference['tracks'] as $track)
                    <div class="flex items-start gap-3 p-4 bg-teal-50 rounded-lg border border-teal-100">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $track['name'] ?? $track['slug'] ?? '' }}</p>
                            @if(!empty($track['description']))<p class="text-xs text-gray-500 mt-1">{{ $track['description'] }}</p>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Committee --}}
            @if(!empty($conference['committees']))
            <div x-show="tab==='committee'" x-cloak class="space-y-6">
                @php $committeeLabels=['scientific'=>'Scientific Committee','academic'=>'Academic Committee','program'=>'Program Committee','organizing'=>'Organizing Committee']; @endphp
                @foreach($committeeLabels as $type=>$label)
                    @if(!empty($conference['committees'][$type]))
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-900 mb-4 text-lg">{{ $label }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($conference['committees'][$type] as $member)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-indigo-600 font-bold text-sm">{{ strtoupper(substr($member['name']??'M',0,1)) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $member['name']??'' }}</p>
                                    <p class="text-xs text-indigo-600 font-medium">{{ ucfirst(str_replace('_',' ',$member['role']??'member')) }}</p>
                                    @if(!empty($member['affiliation']))<p class="text-xs text-gray-500 truncate">{{ $member['affiliation'] }}</p>@endif
                                    @if(!empty($member['country']))<p class="text-xs text-gray-400">{{ strtoupper($member['country']) }}</p>@endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif

            {{-- Gallery --}}
            @if(!empty($conference['gallery']))
            <div x-show="tab==='gallery'" x-cloak class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($conference['gallery'] as $photo)
                    <a href="{{ $photo['url'] }}" target="_blank" class="block overflow-hidden rounded-lg border border-gray-200 hover:opacity-90 transition-opacity">
                        <img src="{{ $photo['url'] }}" alt="Conference photo" class="w-full h-40 object-cover">
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Contact --}}
            <div x-show="tab==='contact'" x-cloak class="bg-white rounded-xl border border-gray-200 p-6">
                @if(empty($conference['contact_name']) && empty($conference['contact_email']) && empty($conference['contact_phone']) && empty($conference['contact_address']) && empty($conference['location']))
                    <p class="text-gray-400 italic">Contact information not yet available.</p>
                @else
                    <div class="space-y-4">
                        @if(!empty($conference['contact_name']))
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div><p class="text-xs text-gray-500 font-medium">Main Contact</p><p class="text-gray-900 font-semibold">{{ $conference['contact_name'] }}</p></div>
                        </div>
                        @endif
                        @if(!empty($conference['contact_email']))
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div><p class="text-xs text-gray-500 font-medium">Email</p><a href="mailto:{{ $conference['contact_email'] }}" class="text-indigo-600 hover:underline font-medium">{{ $conference['contact_email'] }}</a></div>
                        </div>
                        @endif
                        @if(!empty($conference['contact_phone']))
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div><p class="text-xs text-gray-500 font-medium">Phone</p><p class="text-gray-900 font-semibold">{{ $conference['contact_phone'] }}</p></div>
                        </div>
                        @endif
                        @if(!empty($conference['contact_address']) || !empty($conference['location']))
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div><p class="text-xs text-gray-500 font-medium">Address</p><p class="text-gray-900">{{ $conference['contact_address'] ?? $conference['location'] }}</p></div>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
