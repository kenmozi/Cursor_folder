@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Manage conferences and platform settings</p>
        </div>
        <a href="{{ route('admin.conferences.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Conference
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Conferences</p>
            <p class="text-3xl font-bold text-gray-900">{{ count($conferences) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Active</p>
            <p class="text-3xl font-bold text-green-600">
                {{ count(array_filter($conferences, fn($c) => in_array($c['status'] ?? '', ['active', 'open']))) }}
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Archived</p>
            <p class="text-3xl font-bold text-gray-400">
                {{ count(array_filter($conferences, fn($c) => ($c['status'] ?? '') === 'archived')) }}
            </p>
        </div>
    </div>

    <!-- Recent Conferences -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Recent Conferences</h2>
            <a href="{{ route('admin.conferences') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View all</a>
        </div>

        @if(empty($conferences))
            <div class="text-center py-12 text-gray-400">
                <p>No conferences yet. <a href="{{ route('admin.conferences.create') }}" class="text-indigo-600 hover:underline">Create one</a></p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Title</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Submission Close</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($conferences as $conf)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $conf['title'] ?? 'Untitled' }}</p>
                            <p class="text-xs text-gray-400">{{ $conf['slug'] ?? '' }}</p>
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
                            <a href="{{ route('admin.conferences.show', $conf['slug']) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
