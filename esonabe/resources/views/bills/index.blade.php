@extends('layouts.app')
@section('title', __('messages.bills'))

@section('content')
<div style="display:flex; flex-direction:column; gap:16px;">

    {{-- Filter --}}
    <div class="card">
        <div class="card-body" style="padding:14px 20px;">
            <form method="GET" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <svg style="width:16px;height:16px;color:#9ca3af;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <label style="font-size:.83rem; font-weight:600; color:#374151; white-space:nowrap;">{{ __('messages.filter_status') }}:</label>
                </div>
                <select name="status" class="form-input" style="width:180px;" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_statuses') }}</option>
                    @foreach(['pending','paid','overdue','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ __('messages.status_'.$s) }}
                        </option>
                    @endforeach
                </select>
                @if(request('status'))
                    <a href="{{ route('bills.index') }}" class="btn btn-secondary btn-sm">✕ Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        @if($bills->isEmpty())
        <div class="card-body" style="text-align:center; padding:64px 24px;">
            <div style="font-size:3.5rem; margin-bottom:16px;">🧾</div>
            <h3 style="margin:0 0 8px; color:#374151;">{{ __('messages.no_bills') }}</h3>
            <p style="color:#9ca3af; font-size:.88rem; margin:0;">Aucune facture correspondant à votre recherche.</p>
        </div>
        @else
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.bill_number') }}</th>
                        <th>{{ __('messages.contract_name') }}</th>
                        <th>Compteur</th>
                        <th>{{ __('messages.bill_period') }}</th>
                        <th>{{ __('messages.bill_consumption') }}</th>
                        <th>{{ __('messages.bill_total') }}</th>
                        <th>{{ __('messages.bill_due') }}</th>
                        <th>Statut</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bills as $bill)
                    <tr>
                        <td style="font-family:monospace; font-size:.78rem; color:#1d4ed8; font-weight:700;">{{ $bill->bill_number }}</td>
                        <td>
                            <div style="font-weight:600; font-size:.85rem;">{{ $bill->contract->name }}</div>
                        </td>
                        <td style="font-family:monospace; font-size:.76rem; color:#9ca3af;">{{ $bill->meter->meter_number }}</td>
                        <td style="font-size:.8rem; white-space:nowrap; color:#6b7280;">
                            {{ $bill->period_start->format('d/m/Y') }}<br>{{ $bill->period_end->format('d/m/Y') }}
                        </td>
                        <td style="font-weight:600;">{{ $bill->consumption_kwh }} kWh</td>
                        <td style="font-weight:800; color:#111;">{{ number_format($bill->total_cfa, 0, ',', ' ') }} <span style="font-size:.75rem; font-weight:500; color:#9ca3af;">CFA</span></td>
                        <td style="{{ $bill->isOverdue() ? 'color:#dc2626; font-weight:700;' : 'color:#6b7280;' }} font-size:.82rem; white-space:nowrap;">
                            {{ $bill->due_date->format('d/m/Y') }}
                        </td>
                        <td>
                            @if($bill->isOverdue())
                                <span class="badge badge-overdue">{{ __('messages.status_overdue') }}</span>
                            @else
                                <span class="badge badge-{{ $bill->status }}">{{ __('messages.status_'.$bill->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <a href="{{ route('bills.show', $bill) }}" class="btn btn-secondary btn-sm">{{ __('messages.details') }}</a>
                                @if($bill->status === 'pending')
                                    <form action="{{ route('bills.pay', $bill) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">✓ {{ __('messages.bill_pay') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:14px 20px; border-top:1px solid #f0f0f0;">
            {{ $bills->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
