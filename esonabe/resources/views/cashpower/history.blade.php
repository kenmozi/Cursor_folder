@extends('layouts.app')
@section('title', __('messages.cashpower_history'))
@section('subtitle', $meter->meter_number . ' — ' . $meter->contract->name)
@section('header-actions')
    <a href="{{ route('cashpower.index') }}" class="btn btn-secondary btn-sm">← {{ __('messages.back') }}</a>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

    {{-- Balance card --}}
    <div class="card">
        <div style="height:3px; background:linear-gradient(90deg,#D32F2F,#FFD600,#2E7D32);"></div>
        <div class="card-body" style="display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:.78rem; color:#6b7280; font-weight:600;">{{ __('messages.cashpower_balance') }}</div>
                @php $bc = match($meter->balance_status) { 'danger'=>'#dc2626','warning'=>'#d97706',default=>'#16a34a' }; @endphp
                <div style="font-size:2.5rem; font-weight:800; color:{{ $bc }}; margin-top:4px; line-height:1.1;">
                    {{ number_format($meter->cashpower_balance_kwh, 2) }} <span style="font-size:1rem; font-weight:500; color:#9ca3af;">kWh</span>
                </div>
                <div style="font-size:.73rem; color:#6b7280; margin-top:4px;">{{ $meter->class_label }} · {{ $meter->amperage }}A</div>
            </div>
            <a href="{{ route('cashpower.index') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                {{ __('messages.cashpower_buy') }}
            </a>
        </div>
    </div>

    {{-- Transactions --}}
    <div class="card">
        <div class="card-header">
            <h3>{{ __('messages.cashpower_history') }}</h3>
            @if($transactions->total() > 0)
                <span style="font-size:.73rem; color:#9ca3af; background:#f9f9f9; padding:3px 9px; border-radius:20px; border:1px solid #ebebeb;">
                    {{ $transactions->total() }} {{ $transactions->total() > 1 ? 'transactions' : 'transaction' }}
                </span>
            @endif
        </div>

        @if($transactions->isEmpty())
        <div class="card-body" style="text-align:center; padding:64px 24px;">
            <div style="font-size:3rem; margin-bottom:12px;">📭</div>
            <p style="color:#9ca3af; font-size:.88rem; margin:0;">{{ __('messages.no_data') }}</p>
        </div>
        @else
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.transaction_ref') }}</th>
                        <th>{{ __('messages.purchase_date') }}</th>
                        <th>{{ __('messages.amount') }}</th>
                        <th>{{ __('messages.cashpower_kwh') }}</th>
                        <th>{{ __('messages.cashpower_token') }}</th>
                        <th>Avant</th>
                        <th>Après</th>
                        <th>{{ __('messages.bill_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                    <tr>
                        <td style="font-family:monospace; font-size:.78rem; color:#1d4ed8; font-weight:600;">{{ $tx->transaction_ref }}</td>
                        <td style="font-size:.83rem; white-space:nowrap;">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                        <td style="font-weight:700;">{{ number_format($tx->amount_cfa, 0, ',', ' ') }} <span style="font-size:.75rem; color:#9ca3af; font-weight:500;">CFA</span></td>
                        <td style="font-weight:700; color:#16a34a;">+{{ $tx->kwh_purchased }} kWh</td>
                        <td style="font-family:monospace; font-size:.76rem; color:#374151; letter-spacing:.5px;">{{ $tx->token_code }}</td>
                        <td style="font-size:.8rem; color:#9ca3af;">{{ $tx->balance_before_kwh }} kWh</td>
                        <td style="font-size:.83rem; font-weight:600; color:#1d4ed8;">{{ $tx->balance_after_kwh }} kWh</td>
                        <td>
                            @if($tx->status === 'completed')
                                <span class="badge badge-paid">{{ __('messages.status_completed') }}</span>
                            @else
                                <span class="badge badge-overdue">{{ __('messages.status_failed') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:14px 20px; border-top:1px solid #f0f0f0;">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
