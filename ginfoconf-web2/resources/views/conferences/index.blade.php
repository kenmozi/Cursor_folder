@extends('layouts.app')

@section('title', 'Conferences')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Conferences</h1>
            <p class="text-gray-500 mt-1">Browse all available academic conferences</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('conferences') }}" class="flex flex-wrap gap-3 mb-8">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search conferences..."
               class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 w-64">
        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition-colors">
            Search
        </button>
    </form>

    @if(empty($conferences))
        <div class="text-center py-20">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
            </svg>
            <h3 class="text-xl font-semibold text-gray-500">No conferences found</h3>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($conferences as $conf)
            @php $status = $conf['status'] ?? 'draft'; @endphp
            <a href="{{ route('conference', $conf['slug'] ?? '#') }}"
               class="bg-white rounded-xl border border-gray-200 hover:border-indigo-200 hover:shadow-md transition-all p-6 group">
                <div class="flex items-start justify-between mb-3">
                    <span class="badge badge-{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                    @if(!empty($conf['blind_mode']))
                        <span class="text-xs text-gray-400">{{ ucfirst($conf['blind_mode']) }} blind</span>
                    @endif
                </div>

                <h3 class="font-semibold text-gray-900 group-hover:text-indigo-700 text-lg mb-2 leading-snug">
                    {{ $conf['title'] ?? 'Untitled' }}
                </h3>

                @if(!empty($conf['subtitle']))
                    <p class="text-sm text-gray-500 mb-3">{{ $conf['subtitle'] }}</p>
                @endif

                <div class="space-y-1 mt-3 pt-3 border-t border-gray-100 text-sm text-gray-500">
                    @if(!empty($conf['submission_open']) && !empty($conf['submission_close']))
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Submissions: {{ \Carbon\Carbon::parse($conf['submission_open'])->format('M d') }} – {{ \Carbon\Carbon::parse($conf['submission_close'])->format('M d, Y') }}
                        </p>
                    @endif
                    @if(!empty($conf['location']))
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            {{ $conf['location'] }}
                        </p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(!empty($meta['last_page']) && $meta['last_page'] > 1)
            <div class="flex justify-center gap-2 mt-10">
                @for($i = 1; $i <= $meta['last_page']; $i++)
                    <a href="?page={{ $i }}"
                       class="{{ $meta['current_page'] == $i ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-indigo-50' }} px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium transition-colors">
                        {{ $i }}
                    </a>
                @endfor
            </div>
        @endif
    @endif

</div>
@endsection
