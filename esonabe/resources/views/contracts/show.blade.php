@extends('layouts.app')
@section('title', $contract->name)
@section('subtitle', $contract->contract_number . ' · ' . $contract->city)
@section('header-actions')
    <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-secondary btn-sm">{{ __('messages.edit') }}</a>
    <a href="{{ route('meters.create', $contract) }}" class="btn btn-primary btn-sm">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        {{ __('messages.add_meter') }}
    </a>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:18px;">

    {{-- Info card --}}
    <div class="card">
        <div style="height:3px; background:linear-gradient(90deg,#D32F2F,#FFD600,#2E7D32);"></div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:20px;">
                <div>
                    <div style="font-size:.7rem; text-transform:uppercase; letter-spacing:.6px; color:#9ca3af; font-weight:700; margin-bottom:5px;">{{ __('messages.contract_number') }}</div>
                    <div style="font-family:monospace; font-weight:700; color:#111; font-size:.95rem;">{{ $contract->contract_number }}</div>
                </div>
                <div>
                    <div style="font-size:.7rem; text-transform:uppercase; letter-spacing:.6px; color:#9ca3af; font-weight:700; margin-bottom:5px;">{{ __('messages.contract_address') }}</div>
                    <div style="font-weight:600; color:#111;">{{ $contract->address }}</div>
                </div>
                <div>
                    <div style="font-size:.7rem; text-transform:uppercase; letter-spacing:.6px; color:#9ca3af; font-weight:700; margin-bottom:5px;">{{ __('messages.contract_city') }}</div>
                    <div style="font-weight:600; color:#111;">{{ $contract->city }}</div>
                </div>
                <div>
                    <div style="font-size:.7rem; text-transform:uppercase; letter-spacing:.6px; color:#9ca3af; font-weight:700; margin-bottom:5px;">{{ __('messages.contract_status') }}</div>
                    @if($contract->status === 'active')
                        <span class="badge badge-active">{{ __('messages.status_active') }}</span>
                    @else
                        <span class="badge badge-inactive">{{ __('messages.status_'.$contract->status) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Meters --}}
    <div class="card">
        <div class="card-header">
            <h3>{{ __('messages.meters') }}</h3>
            <a href="{{ route('meters.create', $contract) }}" class="btn btn-primary btn-sm">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('messages.add_meter') }}
            </a>
        </div>
        @if($contract->meters->isEmpty())
        <div class="card-body" style="text-align:center; padding:48px;">
            <div style="font-size:3rem; margin-bottom:12px;">🔌</div>
            <p style="color:#9ca3af; margin:0 0 16px;">{{ __('messages.no_meters') }}</p>
            <a href="{{ route('meters.create', $contract) }}" class="btn btn-primary">+ {{ __('messages.add_meter') }}</a>
        </div>
        @else
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.meter_number') }}</th>
                        <th>{{ __('messages.meter_type') }}</th>
                        <th>{{ __('messages.meter_class') }}</th>
                        <th>{{ __('messages.meter_amperage') }}</th>
                        <th>Balance / Relevé</th>
                        <th>{{ __('messages.meter_status') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->meters as $meter)
                    <tr>
                        <td style="font-family:monospace; font-size:.8rem; color:#1d4ed8; font-weight:600;">{{ $meter->meter_number }}</td>
                        <td>
                            @if($meter->isCashpower())
                                <span class="badge" style="background:#fff3cd; color:#92400e;">⚡ Cash-Power</span>
                            @else
                                <span class="badge badge-active">📊 Normal</span>
                            @endif
                        </td>
                        <td style="font-size:.78rem; color:#6b7280;">{{ $meter->class_label }}</td>
                        <td style="font-weight:600;">{{ $meter->amperage }}A</td>
                        <td>
                            @if($meter->isCashpower())
                                @php $bc = match($meter->balance_status) { 'danger'=>'#dc2626','warning'=>'#d97706',default=>'#16a34a' }; @endphp
                                <span style="font-weight:800; color:{{ $bc }};">{{ number_format($meter->cashpower_balance_kwh,1) }} kWh</span>
                            @else
                                <span style="color:#6b7280; font-size:.83rem;">{{ number_format($meter->last_reading_kwh,1) }} kWh</span>
                            @endif
                        </td>
                        <td>
                            @if($meter->status === 'active')
                                <span class="badge badge-active">{{ __('messages.status_active') }}</span>
                            @else
                                <span class="badge badge-inactive">{{ __('messages.status_inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <a href="{{ route('meters.show', $meter) }}" class="btn btn-secondary btn-sm">{{ __('messages.view') }}</a>
                                @if($meter->isCashpower())
                                    <a href="{{ route('cashpower.index') }}" class="btn btn-sm" style="background:#fff3cd;color:#92400e;">⚡</a>
                                @endif
                                <form action="{{ route('meters.destroy', $meter) }}" method="POST"
                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button>
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

    {{-- Bills --}}
    <div class="card">
        <div class="card-header">
            <h3>{{ __('messages.bills') }}</h3>
            <a href="{{ route('bills.index') }}" class="btn btn-secondary btn-sm">Voir tout →</a>
        </div>
        @if($contract->bills->isEmpty())
        <div class="card-body" style="text-align:center; padding:32px; color:#9ca3af; font-size:.85rem;">
            {{ __('messages.no_bills') }}
        </div>
        @else
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.bill_number') }}</th>
                        <th>{{ __('messages.bill_period') }}</th>
                        <th>{{ __('messages.bill_consumption') }}</th>
                        <th>{{ __('messages.bill_total') }}</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->bills as $bill)
                    <tr>
                        <td style="font-family:monospace; font-size:.78rem; color:#1d4ed8; font-weight:600;">{{ $bill->bill_number }}</td>
                        <td style="font-size:.8rem; color:#6b7280;">{{ $bill->period_start->format('d/m/Y') }} – {{ $bill->period_end->format('d/m/Y') }}</td>
                        <td>{{ $bill->consumption_kwh }} kWh</td>
                        <td style="font-weight:700;">{{ number_format($bill->total_cfa, 0, ',', ' ') }} CFA</td>
                        <td>
                            <span class="badge badge-{{ $bill->status }}">{{ __('messages.status_'.$bill->status) }}</span>
                        </td>
                        <td><a href="{{ route('bills.show', $bill) }}" class="btn btn-secondary btn-sm">{{ __('messages.details') }}</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
