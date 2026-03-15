@extends('layouts.app')
@section('title', $contract->name)
@section('subtitle', $contract->contract_number)
@section('header-actions')
    <a href="{{ route('contracts.edit', $contract) }}" class="btn-secondary text-xs">{{ __('messages.edit') }}</a>
    <a href="{{ route('meters.create', $contract) }}" class="btn-primary text-xs">+ {{ __('messages.add_meter') }}</a>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Contract info --}}
    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.contract_number') }}</div>
                    <div class="font-mono font-semibold text-gray-900 mt-1">{{ $contract->contract_number }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.contract_address') }}</div>
                    <div class="font-medium text-gray-900 mt-1">{{ $contract->address }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.contract_city') }}</div>
                    <div class="font-medium text-gray-900 mt-1">{{ $contract->city }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.contract_status') }}</div>
                    <div class="mt-1">
                        @if($contract->status === 'active')
                            <span class="badge-active">{{ __('messages.status_active') }}</span>
                        @else
                            <span class="badge-inactive">{{ __('messages.status_'.$contract->status) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Meters --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">{{ __('messages.meters') }}</h3>
            <a href="{{ route('meters.create', $contract) }}" class="btn-primary text-xs">+ {{ __('messages.add_meter') }}</a>
        </div>
        @if($contract->meters->isEmpty())
        <div class="card-body text-center py-10">
            <div class="text-3xl mb-3">🔌</div>
            <p class="text-gray-500">{{ __('messages.no_meters') }}</p>
            <a href="{{ route('meters.create', $contract) }}" class="btn-primary inline-flex mt-4">+ {{ __('messages.add_meter') }}</a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="th">{{ __('messages.meter_number') }}</th>
                        <th class="th">{{ __('messages.meter_type') }}</th>
                        <th class="th">{{ __('messages.meter_class') }}</th>
                        <th class="th">{{ __('messages.meter_amperage') }}</th>
                        <th class="th">Balance / Relevé</th>
                        <th class="th">{{ __('messages.meter_status') }}</th>
                        <th class="th">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($contract->meters as $meter)
                    <tr class="hover:bg-gray-50">
                        <td class="td font-mono text-blue-700">{{ $meter->meter_number }}</td>
                        <td class="td">
                            @if($meter->isCashpower())
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">⚡ Cash-Power</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">📊 Normal</span>
                            @endif
                        </td>
                        <td class="td text-xs">{{ $meter->class_label }}</td>
                        <td class="td">{{ $meter->amperage }}A</td>
                        <td class="td">
                            @if($meter->isCashpower())
                                <span class="font-bold {{ $meter->balance_color_class }}">
                                    {{ number_format($meter->cashpower_balance_kwh, 1) }} kWh
                                </span>
                            @else
                                <span class="text-gray-500 text-xs">{{ number_format($meter->last_reading_kwh, 1) }} kWh</span>
                            @endif
                        </td>
                        <td class="td">
                            @if($meter->status === 'active')
                                <span class="badge-active">{{ __('messages.status_active') }}</span>
                            @else
                                <span class="badge-inactive">{{ __('messages.status_inactive') }}</span>
                            @endif
                        </td>
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('meters.show', $meter) }}" class="text-blue-600 hover:underline text-xs">{{ __('messages.view') }}</a>
                                @if($meter->isCashpower())
                                    <a href="{{ route('cashpower.index') }}" class="text-orange-600 hover:underline text-xs">⚡ Cash-Power</a>
                                @endif
                                <form action="{{ route('meters.destroy', $meter) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-xs">{{ __('messages.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Recent Bills --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">{{ __('messages.bills') }}</h3>
            <a href="{{ route('bills.index') }}" class="text-xs text-blue-600 hover:underline">{{ __('messages.view') }} →</a>
        </div>
        @if($contract->bills->isEmpty())
        <div class="card-body text-center py-8">
            <p class="text-gray-400 text-sm">{{ __('messages.no_bills') }}</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="th">{{ __('messages.bill_number') }}</th>
                        <th class="th">{{ __('messages.bill_period') }}</th>
                        <th class="th">{{ __('messages.bill_total') }}</th>
                        <th class="th">{{ __('messages.bill_status') }}</th>
                        <th class="th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($contract->bills as $bill)
                    <tr class="hover:bg-gray-50">
                        <td class="td font-mono text-xs text-blue-700">{{ $bill->bill_number }}</td>
                        <td class="td text-xs">{{ $bill->period_start->format('d/m/Y') }} – {{ $bill->period_end->format('d/m/Y') }}</td>
                        <td class="td font-semibold">{{ number_format($bill->total_cfa, 0, ',', ' ') }} CFA</td>
                        <td class="td">
                            <span class="{{ $bill->status_badge_class }} inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                {{ __('messages.status_'.$bill->status) }}
                            </span>
                        </td>
                        <td class="td">
                            <a href="{{ route('bills.show', $bill) }}" class="text-blue-600 hover:underline text-xs">{{ __('messages.details') }}</a>
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
