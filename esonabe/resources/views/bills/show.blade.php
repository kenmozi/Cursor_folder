@extends('layouts.app')
@section('title', __('messages.bill_number') . ': ' . $bill->bill_number)
@section('header-actions')
    <a href="{{ route('bills.index') }}" class="btn-secondary text-xs">← {{ __('messages.back') }}</a>
    @if($bill->status === 'pending')
        <form action="{{ route('bills.pay', $bill) }}" method="POST">
            @csrf
            <button type="submit" class="btn-success">✓ {{ __('messages.bill_pay') }}</button>
        </form>
    @endif
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        {{-- Bill header --}}
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 text-white px-6 py-6 rounded-t-xl">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-blue-200 text-sm">e-SONABE · {{ __('messages.bill_number') }}</div>
                    <div class="font-mono text-xl font-bold mt-1">{{ $bill->bill_number }}</div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($bill->status === 'paid') bg-green-100 text-green-900
                        @elseif($bill->status === 'overdue') bg-red-100 text-red-900
                        @else bg-yellow-100 text-yellow-900 @endif">
                        {{ __('messages.status_'.$bill->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body space-y-6">
            {{-- Contract & meter info --}}
            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-gray-100">
                <div>
                    <div class="text-xs text-gray-500 uppercase">{{ __('messages.contract_name') }}</div>
                    <div class="font-semibold text-gray-900 mt-1">{{ $bill->contract->name }}</div>
                    <div class="text-xs text-gray-400">{{ $bill->contract->contract_number }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase">{{ __('messages.meter_number') }}</div>
                    <div class="font-mono font-semibold text-gray-900 mt-1">{{ $bill->meter->meter_number }}</div>
                    <div class="text-xs text-gray-400">{{ $bill->meter->class_label }}</div>
                </div>
            </div>

            {{-- Period --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <div class="text-xs text-gray-500 uppercase">{{ __('messages.from') }}</div>
                    <div class="font-semibold mt-1">{{ $bill->period_start->format('d/m/Y') }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase">{{ __('messages.to') }}</div>
                    <div class="font-semibold mt-1">{{ $bill->period_end->format('d/m/Y') }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase">{{ __('messages.bill_due') }}</div>
                    <div class="font-semibold mt-1 {{ $bill->isOverdue() ? 'text-red-600' : '' }}">{{ $bill->due_date->format('d/m/Y') }}</div>
                </div>
            </div>

            {{-- Readings --}}
            <div class="bg-gray-50 rounded-xl p-4 grid grid-cols-3 gap-4">
                <div>
                    <div class="text-xs text-gray-500">{{ __('messages.reading_start') }}</div>
                    <div class="font-mono font-bold text-gray-900 mt-1">{{ $bill->reading_start }} kWh</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">{{ __('messages.reading_end') }}</div>
                    <div class="font-mono font-bold text-gray-900 mt-1">{{ $bill->reading_end }} kWh</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">{{ __('messages.bill_consumption') }}</div>
                    <div class="font-mono font-bold text-blue-700 mt-1">{{ $bill->consumption_kwh }} kWh</div>
                </div>
            </div>

            {{-- Amount breakdown --}}
            <div class="space-y-2 border-t border-gray-100 pt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ __('messages.bill_amount') }}</span>
                    <span class="font-medium">{{ number_format($bill->amount_cfa, 0, ',', ' ') }} CFA</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ __('messages.bill_taxes') }}</span>
                    <span class="font-medium">{{ number_format($bill->taxes_cfa, 0, ',', ' ') }} CFA</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                    <span>{{ __('messages.bill_total') }}</span>
                    <span class="text-blue-800">{{ number_format($bill->total_cfa, 0, ',', ' ') }} CFA</span>
                </div>
            </div>

            @if($bill->status === 'paid' && $bill->paid_at)
            <div class="bg-green-50 rounded-xl p-4 flex items-center gap-3">
                <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <div>
                    <div class="font-semibold text-green-800">{{ __('messages.status_paid') }}</div>
                    <div class="text-sm text-green-600">{{ __('messages.bill_paid_at') }}: {{ $bill->paid_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
