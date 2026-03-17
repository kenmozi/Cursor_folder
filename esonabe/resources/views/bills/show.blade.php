@extends('layouts.app')
@section('title', __('messages.bill_number') . ' ' . $bill->bill_number)
@section('header-actions')
    <a href="{{ route('bills.index') }}" class="btn btn-secondary btn-sm">← {{ __('messages.back') }}</a>
    @if($bill->status === 'pending')
        <form action="{{ route('bills.pay', $bill) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ __('messages.bill_pay') }}
            </button>
        </form>
    @endif
@endsection

@section('content')
<div style="max-width:680px;">
    <div class="card" style="overflow:hidden;">

        {{-- Header gradient --}}
        <div style="background:linear-gradient(120deg, #1a1a1a, #2E7D32); padding:28px 32px; display:flex; align-items:flex-start; justify-content:space-between; gap:16px;">
            <div style="display:flex; align-items:center; gap:16px;">
                <img src="/images/sonabel-logo.svg" alt="SONABEL" style="width:64px; background:#fff; border-radius:8px; padding:4px; flex-shrink:0;">
                <div>
                    <div style="font-size:.75rem; color:rgba(255,255,255,.55); letter-spacing:.5px; text-transform:uppercase;">e-SONABE · Facture</div>
                    <div style="font-size:1.3rem; font-weight:800; color:#fff; font-family:monospace; letter-spacing:1px; margin-top:4px;">{{ $bill->bill_number }}</div>
                    <div style="font-size:.78rem; color:#FFD600; margin-top:4px;">{{ $bill->contract->name }}</div>
                </div>
            </div>
            <div>
                @if($bill->status === 'paid')
                    <span class="badge" style="background:rgba(255,255,255,.2); color:#fff; font-size:.8rem; padding:5px 12px; border:1px solid rgba(255,255,255,.3);">✓ {{ __('messages.status_paid') }}</span>
                @elseif($bill->isOverdue())
                    <span class="badge" style="background:rgba(239,68,68,.3); color:#fca5a5; font-size:.8rem; padding:5px 12px; border:1px solid rgba(239,68,68,.4);">⚠ {{ __('messages.status_overdue') }}</span>
                @else
                    <span class="badge" style="background:rgba(250,204,21,.2); color:#fef08a; font-size:.8rem; padding:5px 12px; border:1px solid rgba(250,204,21,.3);">⏳ {{ __('messages.status_pending') }}</span>
                @endif
            </div>
        </div>

        <div style="padding:28px 32px; display:flex; flex-direction:column; gap:24px;">

            {{-- Contract + Meter --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <div style="font-size:.7rem; text-transform:uppercase; letter-spacing:.6px; color:#9ca3af; font-weight:700; margin-bottom:6px;">Contrat</div>
                    <div style="font-weight:700; color:#111; font-size:.95rem;">{{ $bill->contract->name }}</div>
                    <div style="font-family:monospace; font-size:.78rem; color:#9ca3af; margin-top:2px;">{{ $bill->contract->contract_number }}</div>
                    <div style="font-size:.8rem; color:#6b7280; margin-top:4px;">{{ $bill->contract->address }}, {{ $bill->contract->city }}</div>
                </div>
                <div>
                    <div style="font-size:.7rem; text-transform:uppercase; letter-spacing:.6px; color:#9ca3af; font-weight:700; margin-bottom:6px;">Compteur</div>
                    <div style="font-family:monospace; font-weight:700; color:#111; font-size:.95rem;">{{ $bill->meter->meter_number }}</div>
                    <div style="font-size:.8rem; color:#6b7280; margin-top:2px;">{{ $bill->meter->class_label }}</div>
                </div>
            </div>

            {{-- Period --}}
            <div style="background:#fafafa; border-radius:10px; padding:16px; display:grid; grid-template-columns:repeat(3,1fr); gap:12px; border:1px solid #f0f0f0;">
                <div>
                    <div style="font-size:.7rem; color:#9ca3af; font-weight:600; text-transform:uppercase;">{{ __('messages.from') }}</div>
                    <div style="font-weight:700; color:#111; margin-top:4px;">{{ $bill->period_start->format('d/m/Y') }}</div>
                </div>
                <div>
                    <div style="font-size:.7rem; color:#9ca3af; font-weight:600; text-transform:uppercase;">{{ __('messages.to') }}</div>
                    <div style="font-weight:700; color:#111; margin-top:4px;">{{ $bill->period_end->format('d/m/Y') }}</div>
                </div>
                <div>
                    <div style="font-size:.7rem; color:#9ca3af; font-weight:600; text-transform:uppercase;">{{ __('messages.bill_due') }}</div>
                    <div style="font-weight:700; margin-top:4px; {{ $bill->isOverdue() ? 'color:#dc2626;' : 'color:#111;' }}">{{ $bill->due_date->format('d/m/Y') }}</div>
                </div>
            </div>

            {{-- Readings --}}
            <div>
                <div style="font-size:.75rem; font-weight:700; text-transform:uppercase; color:#9ca3af; letter-spacing:.5px; margin-bottom:10px;">Relevés du compteur</div>
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px;">
                    <div style="background:#eff6ff; border-radius:8px; padding:12px 14px;">
                        <div style="font-size:.7rem; color:#3b82f6; font-weight:600;">{{ __('messages.reading_start') }}</div>
                        <div style="font-family:monospace; font-weight:800; color:#1d4ed8; font-size:1rem; margin-top:3px;">{{ $bill->reading_start }} kWh</div>
                    </div>
                    <div style="background:#eff6ff; border-radius:8px; padding:12px 14px;">
                        <div style="font-size:.7rem; color:#3b82f6; font-weight:600;">{{ __('messages.reading_end') }}</div>
                        <div style="font-family:monospace; font-weight:800; color:#1d4ed8; font-size:1rem; margin-top:3px;">{{ $bill->reading_end }} kWh</div>
                    </div>
                    <div style="background:#fef2f2; border-radius:8px; padding:12px 14px;">
                        <div style="font-size:.7rem; color:#D32F2F; font-weight:600;">{{ __('messages.bill_consumption') }}</div>
                        <div style="font-family:monospace; font-weight:800; color:#D32F2F; font-size:1rem; margin-top:3px;">{{ $bill->consumption_kwh }} kWh</div>
                    </div>
                </div>
            </div>

            {{-- Amount breakdown --}}
            <div style="border-top:1px solid #f0f0f0; padding-top:18px;">
                <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; font-size:.88rem;">
                    <span style="color:#6b7280;">{{ __('messages.bill_amount') }}</span>
                    <span style="font-weight:600; color:#111;">{{ number_format($bill->amount_cfa, 0, ',', ' ') }} CFA</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; font-size:.88rem; border-bottom:1px solid #f0f0f0;">
                    <span style="color:#6b7280;">{{ __('messages.bill_taxes') }} (18.5%)</span>
                    <span style="font-weight:600; color:#111;">{{ number_format($bill->taxes_cfa, 0, ',', ' ') }} CFA</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 0 0;">
                    <span style="font-size:1rem; font-weight:800; color:#111;">{{ __('messages.bill_total') }}</span>
                    <span style="font-size:1.5rem; font-weight:800; color:#D32F2F;">{{ number_format($bill->total_cfa, 0, ',', ' ') }} <span style="font-size:.9rem; font-weight:600; color:#9ca3af;">CFA</span></span>
                </div>
            </div>

            {{-- Paid notice --}}
            @if($bill->status === 'paid' && $bill->paid_at)
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:12px;">
                <svg style="width:24px;height:24px;color:#16a34a;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <div style="font-weight:700; color:#166534;">{{ __('messages.status_paid') }}</div>
                    <div style="font-size:.8rem; color:#4ade80;">{{ __('messages.bill_paid_at') }}: {{ $bill->paid_at->format('d/m/Y à H:i') }}</div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
