@extends('layouts.app')
@section('title', $meter->meter_number)
@section('subtitle', $meter->contract->name . ' · ' . $meter->class_label . ' · ' . $meter->amperage . 'A')
@section('header-actions')
    @if($meter->isCashpower())
        <a href="{{ route('cashpower.index') }}" class="btn btn-primary btn-sm">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            {{ __('messages.cashpower_buy') }}
        </a>
    @endif
    <a href="{{ route('contracts.show', $meter->contract) }}" class="btn btn-secondary btn-sm">← {{ __('messages.back') }}</a>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

    {{-- KPI cards --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px;">

        <div class="stat-card">
            <div style="font-size:.78rem; color:#6b7280; font-weight:600;">{{ __('messages.meter_type') }}</div>
            <div style="font-size:1.3rem; font-weight:800; color:#111; margin-top:4px;">
                @if($meter->isCashpower()) ⚡ Cash-Power @else 📊 Normal @endif
            </div>
            <div style="font-size:.73rem; color:#9ca3af;">{{ $meter->class_label }}</div>
        </div>

        <div class="stat-card">
            <div style="font-size:.78rem; color:#6b7280; font-weight:600;">
                @if($meter->isCashpower()) {{ __('messages.cashpower_balance') }} @else {{ __('messages.consumption_30d') }} @endif
            </div>
            @if($meter->isCashpower())
                @php $bc = match($meter->balance_status) { 'danger'=>'#dc2626','warning'=>'#d97706',default=>'#16a34a' }; @endphp
                <div style="font-size:1.8rem; font-weight:800; color:{{ $bc }}; margin-top:4px; line-height:1.1;">
                    {{ number_format($meter->cashpower_balance_kwh, 2) }} <span style="font-size:.9rem; font-weight:500; color:#9ca3af;">kWh</span>
                </div>
                <div style="font-size:.73rem; font-weight:700; color:{{ $bc }}; margin-top:4px;">
                    @if($meter->balance_status === 'danger') ⚠ {{ __('messages.balance_low') }}
                    @elseif($meter->balance_status === 'warning') ⚠ {{ __('messages.balance_medium') }}
                    @else ✓ {{ __('messages.balance_high') }} @endif
                </div>
            @else
                <div style="font-size:1.8rem; font-weight:800; color:#111; margin-top:4px; line-height:1.1;">
                    {{ number_format($stats['total_kwh_30'], 1) }} <span style="font-size:.9rem; font-weight:500; color:#9ca3af;">kWh</span>
                </div>
                <div style="font-size:.73rem; color:#9ca3af;">{{ __('messages.avg_daily') }}: {{ $stats['avg_daily_kwh_30'] }} kWh/j</div>
            @endif
        </div>

        <div class="stat-card">
            <div style="font-size:.78rem; color:#6b7280; font-weight:600;">{{ __('messages.projected_cost') }}</div>
            <div style="font-size:1.5rem; font-weight:800; color:#111; margin-top:4px; line-height:1.2;">
                {{ number_format($stats['projected_monthly_cfa'], 0, ',', ' ') }}
                <span style="font-size:.85rem; font-weight:500; color:#9ca3af;">CFA</span>
            </div>
            <div style="font-size:.73rem; font-weight:700; margin-top:4px; color:{{ $stats['trend_pct'] > 0 ? '#dc2626' : '#16a34a' }};">
                {{ $stats['trend_pct'] > 0 ? '↑' : '↓' }} {{ abs($stats['trend_pct']) }}% ({{ __('messages.trend') }})
            </div>
        </div>
    </div>

    {{-- Stats grid --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px;">
        @foreach([
            [__('messages.avg_daily'),    $stats['avg_daily_kwh_30'] . ' kWh'],
            [__('messages.avg_daily_90'), $stats['avg_daily_kwh_90'] . ' kWh'],
            [__('messages.peak_daily'),   $stats['peak_daily_kwh'] . ' kWh'],
            [__('messages.total_30d'),    $stats['total_kwh_30'] . ' kWh'],
        ] as [$label, $value])
        <div class="stat-card">
            <div style="font-size:.73rem; color:#9ca3af; font-weight:600;">{{ $label }}</div>
            <div style="font-size:1.25rem; font-weight:800; color:#111; margin-top:4px;">{{ $value }}</div>
        </div>
        @endforeach
    </div>

    {{-- AI Analysis --}}
    @if($stats['data_points_30'] > 0)
    <div class="card" style="border-left:4px solid #3b82f6;">
        <div class="card-body" style="display:flex; align-items:flex-start; gap:14px;">
            <div style="font-size:1.8rem; flex-shrink:0;">🤖</div>
            <div>
                <div style="font-weight:700; color:#111; margin-bottom:6px;">{{ __('messages.ai_analysis') }}</div>
                <p style="font-size:.85rem; color:#374151; line-height:1.6; margin:0;">{{ $stats['ai_insight'] }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Charts row --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

        {{-- Historical chart --}}
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.consumption_history') }}</h3>
            </div>
            <div class="card-body">
                @if($records->isNotEmpty())
                    <canvas id="historyChart" style="max-height:220px;"></canvas>
                @else
                    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:48px 0; color:#d1d5db;">
                        <div style="font-size:2.5rem; margin-bottom:12px;">📭</div>
                        <p style="margin:0; font-size:.85rem;">{{ __('messages.no_consumption') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- 7-day forecast --}}
        <div class="card">
            <div class="card-header">
                <h3>{{ __('messages.forecast_7d') }}</h3>
                <span style="font-size:.72rem; background:#eff6ff; color:#1d4ed8; padding:3px 9px; border-radius:20px; font-weight:700; border:1px solid #bfdbfe;">IA</span>
            </div>
            <div class="card-body">
                @if($stats['data_points_30'] > 0)
                    <canvas id="forecastChart" style="max-height:200px;"></canvas>
                    <div style="display:flex; gap:14px; margin-top:12px;">
                        @foreach(['high'=>['#16a34a',__('messages.confidence_high')],'medium'=>['#ca8a04',__('messages.confidence_medium')],'low'=>['#9ca3af',__('messages.confidence_low')]] as $conf=>[$color,$label])
                        <div style="display:flex; align-items:center; gap:5px; font-size:.72rem; color:#6b7280;">
                            <span style="width:8px; height:8px; border-radius:50%; background:{{ $color }}; flex-shrink:0;"></span>
                            {{ $label }}
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:48px 0; color:#d1d5db;">
                        <div style="font-size:2.5rem; margin-bottom:12px;">🔮</div>
                        <p style="margin:0; font-size:.85rem;">{{ __('messages.no_consumption') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 30-day forecast summary --}}
    @if(!empty($forecast30) && array_sum(array_column($forecast30, 'kwh')) > 0)
    @php
        $totalForecast = array_sum(array_column($forecast30, 'kwh'));
        $costForecast  = round($totalForecast * 131);
        $avgForecast   = round($totalForecast / 30, 2);
        $peakForecast  = max(array_column($forecast30, 'kwh'));
    @endphp
    <div class="card">
        <div class="card-header">
            <h3>{{ __('messages.forecast_30d') }}</h3>
            <span style="font-size:.73rem; color:#9ca3af; background:#f9f9f9; padding:3px 9px; border-radius:20px; border:1px solid #ebebeb;">{{ __('messages.ai_insight') }}</span>
        </div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px;">
                <div style="background:#eff6ff; border-radius:10px; padding:14px 16px;">
                    <div style="font-size:.72rem; color:#1d4ed8; font-weight:700; text-transform:uppercase;">{{ __('messages.total_30d') }} (prév.)</div>
                    <div style="font-size:1.3rem; font-weight:800; color:#1e3a5f; margin-top:4px;">{{ round($totalForecast, 1) }} kWh</div>
                </div>
                <div style="background:#fff7ed; border-radius:10px; padding:14px 16px;">
                    <div style="font-size:.72rem; color:#c2410c; font-weight:700; text-transform:uppercase;">{{ __('messages.avg_daily') }} (prév.)</div>
                    <div style="font-size:1.3rem; font-weight:800; color:#7c2d12; margin-top:4px;">{{ $avgForecast }} kWh/j</div>
                </div>
                <div style="background:#fef2f2; border-radius:10px; padding:14px 16px;">
                    <div style="font-size:.72rem; color:#D32F2F; font-weight:700; text-transform:uppercase;">{{ __('messages.peak_daily') }} (prév.)</div>
                    <div style="font-size:1.3rem; font-weight:800; color:#7f1d1d; margin-top:4px;">{{ round($peakForecast, 1) }} kWh</div>
                </div>
                <div style="background:#f0fdf4; border-radius:10px; padding:14px 16px;">
                    <div style="font-size:.72rem; color:#166534; font-weight:700; text-transform:uppercase;">{{ __('messages.projected_cost') }}</div>
                    <div style="font-size:1.3rem; font-weight:800; color:#14532d; margin-top:4px;">{{ number_format($costForecast, 0, ',', ' ') }} CFA</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Consumption history table --}}
    <div class="card">
        <div class="card-header">
            <h3>{{ __('messages.consumption_history') }}</h3>
        </div>
        @if($records->isEmpty())
        <div class="card-body" style="text-align:center; padding:48px; color:#9ca3af; font-size:.85rem;">
            {{ __('messages.no_consumption') }}
        </div>
        @else
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.consumption_date') }}</th>
                        <th>{{ __('messages.consumption_kwh_day') }}</th>
                        <th>{{ __('messages.consumption_reading') }}</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records->sortByDesc('recorded_date')->take(30) as $record)
                    <tr>
                        <td style="font-weight:600;">{{ $record->recorded_date->format('d/m/Y') }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="height:8px; border-radius:9999px; background:#3b82f6; width:{{ min(120, ($record->kwh_consumed / max(0.1, $stats['peak_daily_kwh'])) * 120) }}px; flex-shrink:0;"></div>
                                <span style="font-weight:600;">{{ $record->kwh_consumed }} kWh</span>
                            </div>
                        </td>
                        <td style="font-family:monospace; font-size:.78rem; color:#6b7280;">{{ $record->meter_reading }}</td>
                        <td>
                            <span class="badge" style="background:#f3f4f6; color:#6b7280;">{{ $record->source }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if($records->isNotEmpty())
new Chart(document.getElementById('historyChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'kWh/jour',
            data: {!! json_encode($chartDataKwh) !!},
            borderColor: '#1d4ed8',
            backgroundColor: 'rgba(29,78,216,0.07)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointBackgroundColor: '#1d4ed8',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { maxTicksLimit: 8, font: { size: 10 } } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 10 } } }
        }
    }
});
@endif

@if($stats['data_points_30'] > 0 && !empty($forecastLabels))
const forecastColors = {!! json_encode(collect($forecast7)->pluck('confidence')->map(fn($c) => match($c) {
    'high'   => 'rgba(22,163,74,0.75)',
    'medium' => 'rgba(202,138,4,0.75)',
    default  => 'rgba(156,163,175,0.75)'
})->toArray()) !!};

new Chart(document.getElementById('forecastChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($forecastLabels) !!},
        datasets: [{
            label: 'kWh (prévu)',
            data: {!! json_encode($forecastData) !!},
            backgroundColor: forecastColors,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 10 } } }
        }
    }
});
@endif
</script>
@endpush
