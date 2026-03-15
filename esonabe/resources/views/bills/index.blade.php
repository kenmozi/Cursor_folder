@extends('layouts.app')
@section('title', __('messages.bills'))

@section('content')
<div class="space-y-4">
    {{-- Filter --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" class="flex items-center gap-3">
                <label class="text-sm font-medium text-gray-700">{{ __('messages.filter_status') }}:</label>
                <select name="status" class="form-input w-48" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_statuses') }}</option>
                    @foreach(['pending','paid','overdue','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ __('messages.status_'.$s) }}</option>
                    @endforeach
                </select>
                @if(request('status'))
                    <a href="{{ route('bills.index') }}" class="text-sm text-blue-600 hover:underline">✕ Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Bills table --}}
    <div class="card">
        @if($bills->isEmpty())
        <div class="card-body text-center py-16">
            <div class="text-4xl mb-3">🧾</div>
            <p class="text-gray-500">{{ __('messages.no_bills') }}</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="th">{{ __('messages.bill_number') }}</th>
                        <th class="th">{{ __('messages.contract_name') }}</th>
                        <th class="th">{{ __('messages.bill_period') }}</th>
                        <th class="th">{{ __('messages.bill_consumption') }}</th>
                        <th class="th">{{ __('messages.bill_total') }}</th>
                        <th class="th">{{ __('messages.bill_due') }}</th>
                        <th class="th">{{ __('messages.bill_status') }}</th>
                        <th class="th">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($bills as $bill)
                    <tr class="hover:bg-gray-50">
                        <td class="td font-mono text-xs text-blue-700">{{ $bill->bill_number }}</td>
                        <td class="td">
                            <div class="font-medium text-sm">{{ $bill->contract->name }}</div>
                            <div class="text-xs text-gray-400">{{ $bill->meter->meter_number }}</div>
                        </td>
                        <td class="td text-xs">
                            {{ $bill->period_start->format('d/m/Y') }}<br>{{ $bill->period_end->format('d/m/Y') }}
                        </td>
                        <td class="td text-sm">{{ $bill->consumption_kwh }} kWh</td>
                        <td class="td font-semibold">{{ number_format($bill->total_cfa, 0, ',', ' ') }} CFA</td>
                        <td class="td text-sm {{ $bill->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                            {{ $bill->due_date->format('d/m/Y') }}
                        </td>
                        <td class="td">
                            <span class="{{ $bill->status_badge_class }} inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                {{ __('messages.status_'.$bill->status) }}
                            </span>
                        </td>
                        <td class="td">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('bills.show', $bill) }}" class="text-blue-600 hover:underline text-xs">{{ __('messages.details') }}</a>
                                @if($bill->status === 'pending')
                                    <form action="{{ route('bills.pay', $bill) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:underline text-xs font-medium">{{ __('messages.bill_pay') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $bills->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
