@extends('layouts.dashboard')

@section('title', 'Conferences')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Conferences</h1>
            <p class="text-gray-500 text-sm mt-1">Manage all conferences on the platform</p>
        </div>
        <a href="{{ route('admin.conferences.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Conference
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('admin.conferences') }}" class="flex gap-3">
        <select name="status" onchange="this.form.submit()"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
        </select>
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if(empty($conferences))
            <div class="text-center py-16 text-gray-400">
                <p>No conferences found. <a href="{{ route('admin.conferences.create') }}" class="text-indigo-600 hover:underline">Create one</a></p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Title</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Submissions Close</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($conferences as $conf)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $conf['title'] ?? 'Untitled' }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $conf['slug'] ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="badge badge-{{ $conf['status'] ?? 'draft' }}">
                                {{ ucfirst(str_replace('_', ' ', $conf['status'] ?? 'draft')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                            {{ !empty($conf['submission_close']) ? \Carbon\Carbon::parse($conf['submission_close'])->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.conferences.show', $conf['slug']) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage</a>
                                <a href="{{ route('admin.conferences.edit', $conf['slug']) }}"
                                   class="text-gray-500 hover:text-gray-700 text-sm">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if(!empty($meta['last_page']) && $meta['last_page'] > 1)
                <div class="flex justify-center gap-2 px-6 py-4 border-t border-gray-100">
                    @for($i = 1; $i <= $meta['last_page']; $i++)
                        <a href="?page={{ $i }}"
                           class="{{ $meta['current_page'] == $i ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-medium transition-colors">
                            {{ $i }}
                        </a>
                    @endfor
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
