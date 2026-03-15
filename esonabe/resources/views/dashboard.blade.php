@extends('layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
<div class="space-y-6">

    {{-- Welcome banner --}}
    <div class="bg-gradient-to-r from-blue-800 to-blue-600 rounded-xl p-6 text-white flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold">{{ __('messages.welcome') }}, {{ Auth::user()->name }} 👋</h2>
            <p class="text-blue-200 mt-1">{{ now()->translatedFormat('l d F Y') }}</p>
        </div>
        <div class="text-5xl opacity-30">⚡</div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">{{ __('messages.total_contracts') }}</span>
                <span class="text-blue-600 bg-blue-50 rounded-lg p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $totalContracts }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">{{ __('messages.total_meters') }}</span>
                <span class="text-orange-500 bg-orange-50 rounded-lg p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                </span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $totalMeters }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">{{ __('messages.pending_bills') }}</span>
                <span class="text-yellow-600 bg-yellow-50 rounded-lg p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $pendingBills->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">{{ __('messages.total_pending') }}</span>
                <span class="text-red-500 bg-red-50 rounded-lg p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ number_format($totalPending, 0, ',', ' ') }} <span class="text-base font-normal text-gray-400">CFA</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Consumption chart --}}
        <div class="card lg:col-span-2">
            <div class="card-header">
                <h3 class="font-semibold text-gray-800">{{ __('messages.consumption_30d') }}</h3>
                <span class="text-xs text-gray-400">kWh/jour</span>
            </div>
            <div class="card-body">
                @if(array_sum($chartData) > 0)
                    <canvas id="consumptionChart" height="200"></canvas>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <p>{{ __('messages.no_data') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- CashPower balances --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-800">⚡ Cash-Power</h3>
                <a href="{{ route('cashpower.index') }}" class="text-xs text-blue-600 hover:underline">{{ __('messages.view') }}</a>
            </div>
            <div class="card-body space-y-4">
                @forelse($cashpowerMeters as $meter)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-xs text-gray-500">{{ $meter->meter_number }}</div>
                            <div class="text-sm font-medium text-gray-800">{{ $meter->contract->name }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-lg {{ $meter->balance_color_class }}">
                                {{ number_format($meter->cashpower_balance_kwh, 1) }} kWh
                            </div>
                            @if($meter->balance_status === 'danger')
                                <div class="text-xs text-red-500 font-medium">⚠ {{ __('messages.balance_low') }}</div>
                            @elseif($meter->balance_status === 'warning')
                                <div class="text-xs text-orange-500 font-medium">⚠ {{ __('messages.balance_medium') }}</div>
                            @else
                                <div class="text-xs text-green-500 font-medium">✓ {{ __('messages.balance_high') }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">{{ __('messages.cashpower_no_meters') }}</p>
                @endforelse

                @if($cashpowerMeters->isNotEmpty())
                    <a href="{{ route('cashpower.index') }}" class="btn-primary w-full justify-center text-center">
                        ⚡ {{ __('messages.cashpower_buy') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Pending Bills --}}
    @if($pendingBills->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">{{ __('messages.pending_bills') }}</h3>
            <a href="{{ route('bills.index') }}" class="text-xs text-blue-600 hover:underline">{{ __('messages.view') }} →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="th">{{ __('messages.bill_number') }}</th>
                        <th class="th">{{ __('messages.contract_name') }}</th>
                        <th class="th">{{ __('messages.bill_due') }}</th>
                        <th class="th">{{ __('messages.bill_total') }}</th>
                        <th class="th">{{ __('messages.bill_status') }}</th>
                        <th class="th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($pendingBills->take(5) as $bill)
                    <tr class="hover:bg-gray-50">
                        <td class="td font-mono text-blue-700">{{ $bill->bill_number }}</td>
                        <td class="td">{{ $bill->contract->name }}</td>
                        <td class="td {{ $bill->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                            {{ $bill->due_date->format('d/m/Y') }}
                        </td>
                        <td class="td font-semibold">{{ number_format($bill->total_cfa, 0, ',', ' ') }} CFA</td>
                        <td class="td">
                            @if($bill->isOverdue())
                                <span class="badge-overdue">{{ __('messages.status_overdue') }}</span>
                            @else
                                <span class="badge-pending">{{ __('messages.status_pending') }}</span>
                            @endif
                        </td>
                        <td class="td">
                            <a href="{{ route('bills.show', $bill) }}" class="text-blue-600 hover:underline text-xs">{{ __('messages.details') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Contracts overview --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">{{ __('messages.contracts') }}</h3>
            <a href="{{ route('contracts.create') }}" class="btn-primary text-xs">
                + {{ __('messages.add_contract') }}
            </a>
        </div>
        <div class="card-body">
            @forelse($contracts as $contract)
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl mb-3 last:mb-0 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-700 font-bold text-sm">
                            {{ strtoupper(substr($contract->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">{{ $contract->name }}</div>
                            <div class="text-xs text-gray-400">{{ $contract->contract_number }} · {{ $contract->address }}, {{ $contract->city }}</div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs text-gray-500">{{ $contract->meters->count() }} {{ __('messages.meters') }}</span>
                                @if($contract->status === 'active')
                                    <span class="badge-active">{{ __('messages.status_active') }}</span>
                                @else
                                    <span class="badge-inactive">{{ __('messages.status_suspended') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('contracts.show', $contract) }}" class="btn-secondary text-xs">{{ __('messages.view') }}</a>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="text-4xl mb-4">📋</div>
                    <p class="text-gray-500 mb-4">{{ __('messages.no_contracts') }}</p>
                    <a href="{{ route('contracts.create') }}" class="btn-primary">+ {{ __('messages.add_contract') }}</a>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if(array_sum($chartData) > 0)
const ctx = document.getElementById('consumptionChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'kWh',
            data: {!! json_encode($chartData) !!},
            backgroundColor: 'rgba(13,71,161,0.15)',
            borderColor: '#0d47a1',
            borderWidth: 2,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { maxTicksLimit: 10 } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
        }
    }
});
@endif
</script>
@endpush
