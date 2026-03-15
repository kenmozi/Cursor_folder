@extends('layouts.app')
@section('title', __('messages.cashpower_history'))
@section('subtitle', $meter->meter_number . ' — ' . $meter->contract->name)
@section('header-actions')
    <a href="{{ route('cashpower.index') }}" class="btn-secondary text-xs">← {{ __('messages.back') }}</a>
@endsection

@section('content')
<div class="space-y-4">
    {{-- Balance card --}}
    <div class="card">
        <div class="card-body flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500">{{ __('messages.cashpower_balance') }}</div>
                <div class="text-3xl font-bold {{ $meter->balance_color_class }} mt-1">
                    {{ number_format($meter->cashpower_balance_kwh, 2) }} kWh
                </div>
                <div class="text-xs text-gray-400 mt-1">{{ $meter->class_label }} · {{ $meter->amperage }}A</div>
            </div>
            <a href="{{ route('cashpower.index') }}" class="btn-primary">⚡ {{ __('messages.cashpower_buy') }}</a>
        </div>
    </div>

    {{-- Transactions --}}
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">{{ __('messages.cashpower_history') }}</h3>
        </div>
        @if($transactions->isEmpty())
        <div class="card-body text-center py-12 text-gray-400">
            <div class="text-3xl mb-2">📭</div>
            <p>{{ __('messages.no_data') }}</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="th">{{ __('messages.transaction_ref') }}</th>
                        <th class="th">{{ __('messages.purchase_date') }}</th>
                        <th class="th">{{ __('messages.amount') }}</th>
                        <th class="th">{{ __('messages.cashpower_kwh') }}</th>
                        <th class="th">{{ __('messages.cashpower_token') }}</th>
                        <th class="th">Avant</th>
                        <th class="th">Après</th>
                        <th class="th">{{ __('messages.bill_status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($transactions as $tx)
                    <tr class="hover:bg-gray-50">
                        <td class="td font-mono text-xs text-blue-700">{{ $tx->transaction_ref }}</td>
                        <td class="td text-sm">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="td font-semibold">{{ number_format($tx->amount_cfa, 0, ',', ' ') }} CFA</td>
                        <td class="td text-green-700 font-medium">+{{ $tx->kwh_purchased }} kWh</td>
                        <td class="td font-mono text-xs">{{ $tx->token_code }}</td>
                        <td class="td text-xs text-gray-400">{{ $tx->balance_before_kwh }} kWh</td>
                        <td class="td text-xs font-medium text-blue-700">{{ $tx->balance_after_kwh }} kWh</td>
                        <td class="td">
                            @if($tx->status === 'completed')
                                <span class="badge-paid">{{ __('messages.status_completed') }}</span>
                            @else
                                <span class="badge-overdue">{{ __('messages.status_failed') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
