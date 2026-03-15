@extends('layouts.app')
@section('title', $meter->meter_number)
@section('subtitle', $meter->contract->name . ' · ' . $meter->class_label . ' · ' . $meter->amperage . 'A')
@section('header-actions')
    @if($meter->isCashpower())
        <a href="{{ route('cashpower.index') }}" class="btn-primary text-xs">⚡ {{ __('messages.cashpower_buy') }}</a>
    @endif
    <a href="{{ route('contracts.show', $meter->contract) }}" class="btn-secondary text-xs">← {{ __('messages.back') }}</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Meter overview card --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="text-xs text-gray-500">{{ __('messages.meter_type') }}</div>
            <div class="text-xl font-bold text-gray-900">
                @if($meter->isCashpower()) ⚡ Cash-Power @else 📊 Normal @endif
            </div>
            <div class="text-xs text-gray-400">{{ $meter->class_label }}</div>
        </div>
        <div class="stat-card">
            <div class="text-xs text-gray-500">
                @if($meter->isCashpower()) {{ __('messages.cashpower_balance') }} @else {{ __('messages.consumption_30d') }} @endif
            </div>
            @if($meter->isCashpower())
                <div class="text-2xl font-bold {{ $meter->balance_color_class }}">
                    {{ number_format($meter->cashpower_balance_kwh, 2) }} kWh
                </div>
                <div class="text-xs font-medium
                    @if($meter->balance_status === 'danger') text-red-500
                    @elseif($meter->balance_status === 'warning') text-orange-500
                    @else text-green-500 @endif">
                    @if($meter->balance_status === 'danger') ⚠ {{ __('messages.balance_low') }}
                    @elseif($meter->balance_status === 'warning') ⚠ {{ __('messages.balance_medium') }}
                    @else ✓ {{ __('messages.balance_high') }} @endif
                </div>
            @else
                <div class="text-2xl font-bold text-gray-900">
                    {{ number_format($stats['total_kwh_30'], 1) }} kWh
                </div>
                <div class="text-xs text-gray-400">{{ __('messages.avg_daily') }}: {{ $stats['avg_daily_kwh_30'] }} kWh</div>
            @endif
        </div>
        <div class="stat-card">
            <div class="text-xs text-gray-500">{{ __('messages.projected_cost') }}</div>
            <div class="text-2xl font-bold text-gray-900">
                {{ number_format($stats['projected_monthly_cfa'], 0, ',', ' ') }} <span class="text-base font-normal text-gray-400">CFA</span>
            </div>
            <div class="text-xs @if($stats['trend_pct'] > 0) text-red-500 @else text-green-500 @endif font-medium">
                {{ $stats['trend_pct'] > 0 ? '↑' : '↓' }} {{ abs($stats['trend_pct']) }}% ({{ __('messages.trend') }})
            </div>
        </div>
    </div>

    {{-- AI Analysis --}}
    @if($stats['data_points_30'] > 0)
    <div class="card border-l-4 border-l-blue-500">
        <div class="card-body">
            <div class="flex items-start gap-3">
                <div class="text-2xl">🤖</div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-1">{{ __('messages.ai_analysis') }}</h4>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $stats['ai_insight'] }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <div class="text-xs text-gray-500">{{ __('messages.avg_daily') }}</div>
            <div class="text-xl font-bold text-gray-900 mt-1">{{ $stats['avg_daily_kwh_30'] }} kWh</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <div class="text-xs text-gray-500">{{ __('messages.avg_daily_90') }}</div>
            <div class="text-xl font-bold text-gray-900 mt-1">{{ $stats['avg_daily_kwh_90'] }} kWh</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <div class="text-xs text-gray-500">{{ __('messages.peak_daily') }}</div>
            <div class="text-xl font-bold text-gray-900 mt-1">{{ $stats['peak_daily_kwh'] }} kWh</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <div class="text-xs text-gray-500">{{ __('messages.total_30d') }}</div>
            <div class="text-xl font-bold text-gray-900 mt-1">{{ $stats['total_kwh_30'] }} kWh</div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Historical chart --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-800">{{ __('messages.consumption_history') }}</h3>
            </div>
            <div class="card-body">
                @if($records->isNotEmpty())
                    <canvas id="historyChart" height="220"></canvas>
                @else
                    <div class="text-center py-10 text-gray-400">
                        <div class="text-3xl mb-2">📭</div>
                        <p>{{ __('messages.no_consumption') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- 7-day forecast chart --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-800">{{ __('messages.forecast_7d') }}</h3>
                <span class="text-xs text-gray-400 bg-blue-50 px-2 py-1 rounded-full">IA</span>
            </div>
            <div class="card-body">
                @if($stats['data_points_30'] > 0)
                    <canvas id="forecastChart" height="220"></canvas>
                    <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                        @foreach(['high' => 'bg-green-100 text-green-800', 'medium' => 'bg-yellow-100 text-yellow-800', 'low' => 'bg-gray-100 text-gray-600'] as $conf => $cls)
                        <div class="flex items-center gap-1">
                            <span class="inline-block w-2 h-2 rounded-full {{ str_contains($cls, 'green') ? 'bg-green-500' : (str_contains($cls, 'yellow') ? 'bg-yellow-500' : 'bg-gray-400') }}"></span>
                            <span class="text-gray-500">{{ __('messages.confidence_'.$conf) }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-gray-400">
                        <div class="text-3xl mb-2">🔮</div>
                        <p>{{ __('messages.no_consumption') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 30-day forecast summary --}}
    @if(!empty($forecast30) && array_sum(array_column($forecast30, 'kwh')) > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">{{ __('messages.forecast_30d') }}</h3>
            <span class="text-xs text-gray-400">{{ __('messages.ai_insight') }}</span>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $totalForecast   = array_sum(array_column($forecast30, 'kwh'));
                    $costForecast    = round($totalForecast * 131);
                    $avgForecast     = round($totalForecast / 30, 2);
                    $peakForecast    = max(array_column($forecast30, 'kwh'));
                @endphp
                <div class="bg-blue-50 rounded-xl p-4">
                    <div class="text-xs text-blue-600">{{ __('messages.total_30d') }} (prév.)</div>
                    <div class="text-xl font-bold text-blue-900 mt-1">{{ round($totalForecast, 1) }} kWh</div>
                </div>
                <div class="bg-orange-50 rounded-xl p-4">
                    <div class="text-xs text-orange-600">{{ __('messages.avg_daily') }} (prév.)</div>
                    <div class="text-xl font-bold text-orange-900 mt-1">{{ $avgForecast }} kWh/j</div>
                </div>
                <div class="bg-red-50 rounded-xl p-4">
                    <div class="text-xs text-red-600">{{ __('messages.peak_daily') }} (prév.)</div>
                    <div class="text-xl font-bold text-red-900 mt-1">{{ round($peakForecast, 1) }} kWh</div>
                </div>
                <div class="bg-green-50 rounded-xl p-4">
                    <div class="text-xs text-green-600">{{ __('messages.projected_cost') }}</div>
                    <div class="text-xl font-bold text-green-900 mt-1">{{ number_format($costForecast, 0, ',', ' ') }} CFA</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Consumption history table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">{{ __('messages.consumption_history') }}</h3>
        </div>
        @if($records->isEmpty())
        <div class="card-body text-center py-8 text-gray-400">{{ __('messages.no_consumption') }}</div>
        @else
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="th">{{ __('messages.consumption_date') }}</th>
                        <th class="th">{{ __('messages.consumption_kwh_day') }}</th>
                        <th class="th">{{ __('messages.consumption_reading') }}</th>
                        <th class="th">Source</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($records->sortByDesc('recorded_date')->take(30) as $record)
                    <tr class="hover:bg-gray-50">
                        <td class="td font-medium">{{ $record->recorded_date->format('d/m/Y') }}</td>
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <div class="h-2 rounded-full bg-blue-500" style="width: {{ min(100, ($record->kwh_consumed / max(0.1, $stats['peak_daily_kwh'])) * 80) }}px"></div>
                                <span>{{ $record->kwh_consumed }} kWh</span>
                            </div>
                        </td>
                        <td class="td font-mono text-xs">{{ $record->meter_reading }}</td>
                        <td class="td text-xs text-gray-400">{{ $record->source }}</td>
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
            borderColor: '#0d47a1',
            backgroundColor: 'rgba(13,71,161,0.08)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { maxTicksLimit: 8, font: { size: 10 } } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } }
        }
    }
});
@endif

@if($stats['data_points_30'] > 0 && !empty($forecastLabels))
const forecastColors = {!! json_encode(collect($forecast7)->pluck('confidence')->map(fn($c) => match($c) {
    'high'   => 'rgba(22,163,74,0.7)',
    'medium' => 'rgba(234,179,8,0.7)',
    default  => 'rgba(156,163,175,0.7)'
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
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } }
        }
    }
});
@endif
</script>
@endpush
