@extends('layouts.dashboard')

@section('title', 'Committees')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.conferences.show', $conference['slug']) }}" class="text-gray-500 hover:text-indigo-600 text-sm flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
    </div>

    <div>
        <h1 class="text-2xl font-bold text-gray-900">Conference Committees</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $conference['title'] ?? '' }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    @php
        $slug = $conference['slug'];
        $committeeLabels = [
            'scientific'  => ['label' => 'Scientific Committee',  'color' => 'blue'],
            'academic'    => ['label' => 'Academic Committee',    'color' => 'purple'],
            'program'     => ['label' => 'Program Committee',     'color' => 'indigo'],
            'organizing'  => ['label' => 'Organizing Committee',  'color' => 'green'],
        ];
        $allMembers = [];
        foreach ($committees as $type => $members) {
            foreach ($members as $m) {
                $allMembers[] = array_merge($m, ['_committee' => $type]);
            }
        }
        $totalCount = count($allMembers);
    @endphp

    <!-- Members by Committee -->
    @foreach($committeeLabels as $type => $info)
        @php $members = $committees[$type] ?? []; @endphp
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">{{ $info['label'] }} <span class="text-gray-400 font-normal text-sm">({{ count($members) }})</span></h2>
            </div>

            @if(empty($members))
                <p class="px-6 py-5 text-gray-400 text-sm italic">No members added to this committee yet.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Role</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Affiliation</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($members as $member)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <p class="font-medium text-gray-900 text-sm">{{ $member['name'] ?? '' }}</p>
                                @if(!empty($member['email']))
                                    <p class="text-xs text-gray-400">{{ $member['email'] }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600 hidden sm:table-cell">{{ ucfirst(str_replace('_', ' ', $member['role'] ?? 'member')) }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500 hidden md:table-cell">{{ $member['affiliation'] ?? '—' }}</td>
                            <td class="px-6 py-3 text-right">
                                <form method="POST" action="{{ route('admin.conferences.committee.delete', [$slug, $member['id']]) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Remove this member?')"
                                            class="text-red-500 hover:text-red-700 text-xs font-medium">Remove</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach

    <!-- Add Member Form -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 mb-5">Add Committee Member</h2>
        <form method="POST" action="{{ route('admin.conferences.committee.add', $slug) }}"
              class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Committee <span class="text-red-500">*</span></label>
                <select name="committee" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select committee…</option>
                    @foreach($committeeLabels as $type => $info)
                        <option value="{{ $type }}" {{ old('committee') === $type ? 'selected' : '' }}>{{ $info['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role within Committee</label>
                <select name="role" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="member" {{ old('role', 'member') === 'member' ? 'selected' : '' }}>Member</option>
                    <option value="chair" {{ old('role') === 'chair' ? 'selected' : '' }}>Chair</option>
                    <option value="co_chair" {{ old('role') === 'co_chair' ? 'selected' : '' }}>Co-Chair</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Dr. Jane Smith"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="jane@university.edu"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Affiliation</label>
                <input type="text" name="affiliation" value="{{ old('affiliation') }}"
                       placeholder="MIT, Cambridge, USA"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Country Code <span class="text-gray-400 text-xs">(2-letter ISO)</span></label>
                <input type="text" name="country" value="{{ old('country') }}"
                       placeholder="US" maxlength="2" style="text-transform:uppercase"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="sm:col-span-2">
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm">
                    Add Member
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
