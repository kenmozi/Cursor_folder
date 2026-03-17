@extends('layouts.app')
@section('title', __('messages.dashboard'))
@section('header-actions')
    <a href="{{ route('contracts.create') }}" class="btn btn-primary btn-sm">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        {{ __('messages.add_contract') }}
    </a>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

    {{-- Welcome banner --}}
    <div style="background:linear-gradient(120deg, #1a1a1a 0%, #2E7D32 60%, #1b5e20 100%); border-radius:14px; padding:24px 28px; display:flex; align-items:center; justify-content:space-between; overflow:hidden; position:relative;">
        <div style="position:absolute; right:-10px; top:-20px; opacity:.08;">
            <img src="/images/sonabel-logo.svg" style="width:180px; height:auto; filter:invert(1);">
        </div>
        <div style="position:relative; z-index:1;">
            <p style="margin:0 0 4px; font-size:.82rem; color:rgba(255,255,255,.6); letter-spacing:.3px;">{{ now()->isoFormat('dddd D MMMM Y') }}</p>
            <h2 style="margin:0; font-size:1.5rem; font-weight:800; color:#fff;">
                {{ __('messages.welcome') }}, {{ Auth::user()->name }}
            </h2>
            <p style="margin:6px 0 0; font-size:.83rem; color:rgba(255,255,255,.55);">Bienvenue dans votre espace client e-SONABE</p>
        </div>
        <div style="border:3px solid rgba(255,214,0,.5); border-radius:12px; padding:4px; flex-shrink:0; position:relative; z-index:1;">
            <img src="/images/sonabel-logo.svg" alt="SONABEL" style="width:76px; height:auto; background:#fff; border-radius:8px; padding:4px; display:block;">
        </div>
    </div>

    {{-- KPI cards --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px;">

        <div class="stat-card">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <span style="font-size:.78rem; color:#6b7280; font-weight:600;">{{ __('messages.total_contracts') }}</span>
                <div style="width:36px; height:36px; background:#fef2f2; border-radius:9px; display:flex; align-items:center; justify-content:center;">
                    <svg style="width:18px;height:18px;color:#D32F2F;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div style="font-size:2rem; font-weight:800; color:#111; line-height:1.1;">{{ $totalContracts }}</div>
            <div style="font-size:.73rem; color:#9ca3af;">contrats actifs</div>
        </div>

        <div class="stat-card">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <span style="font-size:.78rem; color:#6b7280; font-weight:600;">{{ __('messages.total_meters') }}</span>
                <div style="width:36px; height:36px; background:#fff7ed; border-radius:9px; display:flex; align-items:center; justify-content:center;">
                    <svg style="width:18px;height:18px;color:#ea580c;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <div style="font-size:2rem; font-weight:800; color:#111; line-height:1.1;">{{ $totalMeters }}</div>
            <div style="font-size:.73rem; color:#9ca3af;">compteurs enregistrés</div>
        </div>

        <div class="stat-card">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <span style="font-size:.78rem; color:#6b7280; font-weight:600;">{{ __('messages.pending_bills') }}</span>
                <div style="width:36px; height:36px; background:#fefce8; border-radius:9px; display:flex; align-items:center; justify-content:center;">
                    <svg style="width:18px;height:18px;color:#ca8a04;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div style="font-size:2rem; font-weight:800; color:#111; line-height:1.1;">{{ $pendingBills->count() }}</div>
            <div style="font-size:.73rem; color:#9ca3af;">factures en attente</div>
        </div>

        <div class="stat-card">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <span style="font-size:.78rem; color:#6b7280; font-weight:600;">{{ __('messages.total_pending') }}</span>
                <div style="width:36px; height:36px; background:#fef2f2; border-radius:9px; display:flex; align-items:center; justify-content:center;">
                    <svg style="width:18px;height:18px;color:#dc2626;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div style="font-size:1.5rem; font-weight:800; color:#111; line-height:1.2;">
                {{ number_format($totalPending, 0, ',', ' ') }}
                <span style="font-size:.85rem; font-weight:500; color:#9ca3af;">CFA</span>
            </div>
            <div style="font-size:.73rem; color:#9ca3af;">montant impayé</div>
        </div>
    </div>

    {{-- Charts row --}}
    <div style="display:grid; grid-template-columns:1fr 320px; gap:16px;">

        {{-- Consumption chart --}}
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.consumption_30d') }}</h3>
                <span style="font-size:.73rem; color:#9ca3af; background:#f9f9f9; padding:3px 9px; border-radius:20px; border:1px solid #ebebeb;">kWh / jour</span>
            </div>
            <div class="card-body">
                @if(array_sum($chartData) > 0)
                    <canvas id="consumptionChart" style="max-height:220px;"></canvas>
                @else
                    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:48px 0; color:#d1d5db;">
                        <svg style="width:48px;height:48px;margin-bottom:12px;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <p style="margin:0; font-size:.85rem;">{{ __('messages.no_data') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- CashPower panel --}}
        <div class="card">
            <div class="card-header">
                <h3>⚡ Cash-Power</h3>
                <a href="{{ route('cashpower.index') }}" class="btn btn-secondary btn-sm" style="font-size:.72rem;">{{ __('messages.view') }}</a>
            </div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:12px;">
                @forelse($cashpowerMeters as $meter)
                    <div style="background:#fafafa; border:1px solid #f0f0f0; border-radius:10px; padding:12px 14px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                            <div>
                                <div style="font-size:.7rem; color:#9ca3af; font-family:monospace;">{{ $meter->meter_number }}</div>
                                <div style="font-size:.84rem; font-weight:700; color:#111;">{{ $meter->contract->name }}</div>
                            </div>
                            <div style="text-align:right;">
                                @php
                                    $balColor = match($meter->balance_status) {
                                        'danger'  => '#dc2626',
                                        'warning' => '#d97706',
                                        default   => '#16a34a',
                                    };
                                @endphp
                                <div style="font-size:1.15rem; font-weight:800; color:{{ $balColor }};">
                                    {{ number_format($meter->cashpower_balance_kwh, 1) }} kWh
                                </div>
                            </div>
                        </div>
                        {{-- Balance bar --}}
                        @php $pct = min(100, ($meter->cashpower_balance_kwh / 100) * 100); @endphp
                        <div style="height:5px; background:#e5e7eb; border-radius:9999px; overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:{{ $balColor }}; border-radius:9999px; transition:width .4s;"></div>
                        </div>
                        <div style="font-size:.7rem; margin-top:5px; font-weight:600; color:{{ $balColor }};">
                            @if($meter->balance_status === 'danger') ⚠ {{ __('messages.balance_low') }}
                            @elseif($meter->balance_status === 'warning') ⚠ {{ __('messages.balance_medium') }}
                            @else ✓ {{ __('messages.balance_high') }} @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; padding:24px 0; color:#9ca3af; font-size:.83rem;">
                        {{ __('messages.cashpower_no_meters') }}
                    </div>
                @endforelse

                @if($cashpowerMeters->isNotEmpty())
                    <a href="{{ route('cashpower.index') }}" class="btn btn-primary" style="justify-content:center; margin-top:4px;">
                        <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        {{ __('messages.cashpower_buy') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Pending bills --}}
    @if($pendingBills->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <h3>{{ __('messages.pending_bills') }}</h3>
            <a href="{{ route('bills.index') }}" class="btn btn-secondary btn-sm">Voir tout →</a>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.bill_number') }}</th>
                        <th>{{ __('messages.contract_name') }}</th>
                        <th>{{ __('messages.bill_due') }}</th>
                        <th>{{ __('messages.bill_total') }}</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingBills->take(5) as $bill)
                    <tr>
                        <td style="font-family:monospace; font-size:.78rem; color:#1d4ed8; font-weight:600;">{{ $bill->bill_number }}</td>
                        <td style="font-weight:600;">{{ $bill->contract->name }}</td>
                        <td style="{{ $bill->isOverdue() ? 'color:#dc2626; font-weight:700;' : '' }}">{{ $bill->due_date->format('d/m/Y') }}</td>
                        <td style="font-weight:700;">{{ number_format($bill->total_cfa, 0, ',', ' ') }} CFA</td>
                        <td>
                            @if($bill->isOverdue())
                                <span class="badge badge-overdue">{{ __('messages.status_overdue') }}</span>
                            @else
                                <span class="badge badge-pending">{{ __('messages.status_pending') }}</span>
                            @endif
                        </td>
                        <td><a href="{{ route('bills.show', $bill) }}" class="btn btn-secondary btn-sm">{{ __('messages.details') }}</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Contracts --}}
    <div class="card">
        <div class="card-header">
            <h3>{{ __('messages.contracts') }}</h3>
            <a href="{{ route('contracts.create') }}" class="btn btn-primary btn-sm">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('messages.add_contract') }}
            </a>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
            @forelse($contracts as $contract)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border:1.5px solid #f0f0f0; border-radius:10px; transition:border-color .15s; hover:border-color:#D32F2F;">
                    <div style="display:flex; align-items:center; gap:14px;">
                        <div style="width:40px; height:40px; border-radius:10px; background:#D32F2F; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.95rem; color:#fff; flex-shrink:0;">
                            {{ strtoupper(substr($contract->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700; color:#111; font-size:.9rem;">{{ $contract->name }}</div>
                            <div style="font-size:.75rem; color:#9ca3af; margin-top:2px; font-family:monospace;">{{ $contract->contract_number }}</div>
                            <div style="display:flex; align-items:center; gap:8px; margin-top:5px;">
                                <span style="font-size:.73rem; color:#6b7280;">
                                    <svg style="width:12px;height:12px;display:inline;margin-right:2px;vertical-align:-1px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $contract->address }}, {{ $contract->city }}
                                </span>
                                <span class="badge badge-active" style="font-size:.65rem;">{{ $contract->meters->count() }} compteur(s)</span>
                                @if($contract->status === 'active')
                                    <span class="badge badge-active">{{ __('messages.status_active') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-secondary btn-sm">{{ __('messages.view') }}</a>
                </div>
            @empty
                <div style="text-align:center; padding:48px 0;">
                    <div style="font-size:3rem; margin-bottom:12px;">📋</div>
                    <p style="color:#6b7280; font-size:.9rem; margin:0 0 16px;">{{ __('messages.no_contracts') }}</p>
                    <a href="{{ route('contracts.create') }}" class="btn btn-primary">+ {{ __('messages.add_contract') }}</a>
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
new Chart(document.getElementById('consumptionChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'kWh',
            data: {!! json_encode($chartData) !!},
            backgroundColor: 'rgba(211,47,47,0.15)',
            borderColor: '#D32F2F',
            borderWidth: 2,
            borderRadius: 4,
            hoverBackgroundColor: 'rgba(211,47,47,0.3)',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} kWh` } }
        },
        scales: {
            x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 10 } } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 10 } } }
        }
    }
});
@endif
</script>
@endpush
