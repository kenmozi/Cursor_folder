@extends('layouts.app')
@section('title', 'Cash-Power')
@section('subtitle', __('messages.cashpower_buy') . ' · 1 kWh = 131 CFA')

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

@if($meters->isEmpty())
    <div class="card">
        <div class="card-body" style="text-align:center; padding:64px 24px;">
            <div style="font-size:3.5rem; margin-bottom:16px;">⚡</div>
            <h3 style="margin:0 0 8px; color:#374151;">{{ __('messages.cashpower_no_meters') }}</h3>
            <p style="color:#9ca3af; font-size:.88rem; margin:0 0 20px;">Enregistrez un compteur Cash-Power depuis un contrat.</p>
            <a href="{{ route('contracts.index') }}" class="btn btn-primary">{{ __('messages.contracts') }}</a>
        </div>
    </div>
@else

<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start;">

    {{-- Purchase form --}}
    <div class="card">
        <div style="height:3px; background:linear-gradient(90deg,#D32F2F,#FFD600,#2E7D32);"></div>
        <div class="card-header">
            <h3 style="display:flex; align-items:center; gap:8px;">
                <svg style="width:18px;height:18px;color:#D32F2F;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                {{ __('messages.cashpower_buy') }}
            </h3>
            <span style="font-size:.73rem; background:#fff3cd; color:#92400e; padding:4px 10px; border-radius:20px; font-weight:700; border:1px solid #fde68a;">1 kWh = 131 CFA</span>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:20px;">

            {{-- Meter selection --}}
            <div>
                <label class="form-label">{{ __('messages.select_cashpower') }} *</label>
                <select id="meter_select" class="form-input" onchange="updateBalance()">
                    <option value="">— {{ __('messages.select_cashpower') }} —</option>
                    @foreach($meters as $meter)
                    <option value="{{ $meter->id }}"
                            data-balance="{{ $meter->cashpower_balance_kwh }}"
                            data-status="{{ $meter->balance_status }}"
                            data-contract="{{ $meter->contract->name }}"
                            data-number="{{ $meter->meter_number }}">
                        {{ $meter->meter_number }} — {{ $meter->contract->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Balance display --}}
            <div id="balance_display" style="display:none; border-radius:12px; padding:16px 20px; border:2px solid #e5e7eb; transition:border-color .2s;">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div style="font-size:.73rem; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">{{ __('messages.cashpower_balance') }}</div>
                        <div id="balance_value" style="font-size:2.2rem; font-weight:800; margin-top:4px; line-height:1;">0 kWh</div>
                        <div id="balance_label" style="font-size:.78rem; font-weight:700; margin-top:5px;"></div>
                    </div>
                    <div id="balance_gauge" style="width:60px; height:60px; position:relative;">
                        <svg viewBox="0 0 60 60" style="width:100%;height:100%;transform:rotate(-90deg);">
                            <circle cx="30" cy="30" r="24" fill="none" stroke="#f0f0f0" stroke-width="6"/>
                            <circle id="gauge_circle" cx="30" cy="30" r="24" fill="none" stroke="#16a34a" stroke-width="6"
                                    stroke-dasharray="150.8" stroke-dashoffset="150.8" stroke-linecap="round" style="transition:stroke-dashoffset .5s ease;"/>
                        </svg>
                        <div id="gauge_icon" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:1.1rem;">🔋</div>
                    </div>
                </div>
            </div>

            {{-- Amount input --}}
            <div>
                <label class="form-label">{{ __('messages.cashpower_amount') }} *</label>
                <div style="position:relative;">
                    <input type="number" id="amount_input" min="500" max="500000" step="100"
                           class="form-input" style="padding-right:50px; font-size:1.1rem; font-weight:700;"
                           placeholder="500" oninput="computeKwh()">
                    <span style="position:absolute; right:14px; top:50%; transform:translateY(-50%); font-weight:700; color:#9ca3af; font-size:.85rem;">CFA</span>
                </div>
                <p style="font-size:.73rem; color:#9ca3af; margin:5px 0 0; display:flex; align-items:center; gap:4px;">
                    <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('messages.cashpower_min') }} · {{ __('messages.cashpower_max') }}
                </p>
            </div>

            {{-- Quick amounts --}}
            <div>
                <div style="font-size:.75rem; color:#9ca3af; font-weight:600; margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Montants rapides</div>
                <div style="display:flex; flex-wrap:wrap; gap:8px;">
                    @foreach([500, 1000, 2000, 5000, 10000, 25000, 50000] as $amt)
                    <button type="button" onclick="setAmount({{ $amt }})"
                            style="padding:6px 12px; font-size:.78rem; font-weight:700; border:1.5px solid #e5e7eb; border-radius:8px; background:#fff; color:#374151; cursor:pointer; transition:all .15s;"
                            onmouseover="this.style.borderColor='#D32F2F';this.style.color='#D32F2F';this.style.background='#fef2f2';"
                            onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151';this.style.background='#fff';">
                        {{ number_format($amt, 0, ',', ' ') }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- KWh preview --}}
            <div id="kwh_preview" style="display:none; background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1.5px solid #86efac; border-radius:12px; padding:16px 20px;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <div style="font-size:.7rem; color:#16a34a; font-weight:700; text-transform:uppercase; letter-spacing:.4px;">Vous obtiendrez</div>
                        <div id="kwh_value" style="font-size:1.6rem; font-weight:800; color:#15803d; margin-top:3px;"></div>
                    </div>
                    <div>
                        <div style="font-size:.7rem; color:#16a34a; font-weight:700; text-transform:uppercase; letter-spacing:.4px;">Nouveau solde</div>
                        <div id="new_balance" style="font-size:1.4rem; font-weight:800; color:#15803d; margin-top:3px;"></div>
                    </div>
                </div>
            </div>

            {{-- Buy button --}}
            <button id="buy_btn" onclick="purchaseCashpower()"
                    class="btn btn-primary btn-lg" style="justify-content:center; opacity:.5; cursor:not-allowed;" disabled>
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                {{ __('messages.cashpower_buy') }}
            </button>
        </div>
    </div>

    {{-- Meters list --}}
    <div style="display:flex; flex-direction:column; gap:12px;">
        <h3 style="margin:0; font-size:.88rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.4px;">Vos compteurs Cash-Power</h3>
        @foreach($meters as $meter)
        @php $bc = match($meter->balance_status) { 'danger'=>'#dc2626','warning'=>'#d97706',default=>'#16a34a' }; @endphp
        <div class="card" id="meter-card-{{ $meter->id }}">
            <div class="card-body" style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                    <div style="min-width:0;">
                        <div style="font-family:monospace; font-size:.73rem; color:#9ca3af;">{{ $meter->meter_number }}</div>
                        <div style="font-weight:700; color:#111; font-size:.9rem; margin-top:2px;">{{ $meter->contract->name }}</div>
                        <div style="font-size:.75rem; color:#6b7280; margin-top:2px;">{{ $meter->class_label }} · {{ $meter->amperage }}A</div>
                    </div>
                    <div style="text-align:right; flex-shrink:0; margin-left:8px;">
                        <div id="balance-{{ $meter->id }}" style="font-size:1.35rem; font-weight:800; color:{{ $bc }}; line-height:1.1;">
                            {{ number_format($meter->cashpower_balance_kwh, 2) }}
                        </div>
                        <div style="font-size:.72rem; color:{{ $bc }}; font-weight:600;">kWh</div>
                    </div>
                </div>

                {{-- Progress bar --}}
                @php $pct = min(100, ($meter->cashpower_balance_kwh / 100) * 100); @endphp
                <div style="height:6px; background:#f0f0f0; border-radius:9999px; overflow:hidden;">
                    <div style="height:100%; width:{{ $pct }}%; background:{{ $bc }}; border-radius:9999px; transition:width .4s;"></div>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <span style="font-size:.73rem; font-weight:700; color:{{ $bc }};">
                        @if($meter->balance_status === 'danger') ⚠ {{ __('messages.balance_low') }}
                        @elseif($meter->balance_status === 'warning') ⚠ {{ __('messages.balance_medium') }}
                        @else ✓ {{ __('messages.balance_high') }} @endif
                    </span>
                    <a href="{{ route('cashpower.history', $meter) }}" style="font-size:.73rem; color:#6b7280; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                        <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Historique
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Success modal --}}
<div id="success_modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); backdrop-filter:blur(4px); display:none; align-items:center; justify-content:center; z-index:9999; padding:20px;">
    <div style="background:#fff; border-radius:20px; padding:40px 36px; max-width:420px; width:100%; box-shadow:0 25px 80px rgba(0,0,0,.3); text-align:center; position:relative;">
        <div style="width:72px; height:72px; background:#f0fdf4; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; border:3px solid #86efac;">
            <svg style="width:36px;height:36px;color:#16a34a;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h2 style="margin:0 0 6px; font-size:1.3rem; font-weight:800; color:#111;">{{ __('messages.cashpower_purchased') }}</h2>
        <p style="margin:0 0 24px; font-size:.85rem; color:#6b7280;">Votre solde a été mis à jour avec succès.</p>

        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px; margin-bottom:20px;">
            <div style="font-size:.72rem; color:#16a34a; font-weight:700; text-transform:uppercase;">{{ __('messages.cashpower_kwh') }}</div>
            <div id="modal_kwh" style="font-size:2rem; font-weight:800; color:#15803d; margin-top:4px;"></div>
        </div>

        <div style="background:#fafafa; border-radius:10px; padding:14px 16px; margin-bottom:20px; text-align:left;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <span style="font-size:.78rem; color:#9ca3af;">{{ __('messages.cashpower_token') }}</span>
                <span id="modal_token" style="font-family:monospace; font-weight:800; color:#1d4ed8; font-size:.82rem; letter-spacing:1px;"></span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <span style="font-size:.78rem; color:#9ca3af;">{{ __('messages.transaction_ref') }}</span>
                <span id="modal_ref" style="font-family:monospace; font-size:.75rem; color:#6b7280;"></span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #f0f0f0; padding-top:8px;">
                <span style="font-size:.78rem; color:#9ca3af; font-weight:600;">{{ __('messages.cashpower_balance') }}</span>
                <span id="modal_balance" style="font-weight:800; color:#111;"></span>
            </div>
        </div>

        <button onclick="closeModal()" class="btn btn-primary btn-lg" style="width:100%; justify-content:center;">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ __('messages.confirm') }}
        </button>
    </div>
</div>

@endif
</div>
@endsection

@push('scripts')
<script>
const RATE = 131;
let selectedMeterId = null;
let selectedBalance = 0;

function updateBalance() {
    const sel    = document.getElementById('meter_select');
    const opt    = sel.options[sel.selectedIndex];
    const display= document.getElementById('balance_display');

    if (!opt.value) {
        display.style.display = 'none';
        selectedMeterId = null;
        setBtn(false);
        return;
    }

    selectedMeterId = opt.value;
    selectedBalance = parseFloat(opt.dataset.balance);
    const status    = opt.dataset.status;

    const colors = { danger:'#dc2626', warning:'#d97706', success:'#16a34a' };
    const labels = {
        danger:  '⚠ {{ __("messages.balance_low") }}',
        warning: '⚠ {{ __("messages.balance_medium") }}',
        success: '✓ {{ __("messages.balance_high") }}'
    };
    const color = colors[status] || '#16a34a';

    display.style.display = 'block';
    display.style.borderColor = color;
    document.getElementById('balance_value').style.color = color;
    document.getElementById('balance_value').textContent = selectedBalance.toFixed(2) + ' kWh';
    document.getElementById('balance_label').style.color = color;
    document.getElementById('balance_label').textContent = labels[status];

    // Gauge
    const pct = Math.min(100, (selectedBalance / 100) * 100);
    const circ = 150.8;
    document.getElementById('gauge_circle').style.strokeDashoffset = circ - (circ * pct / 100);
    document.getElementById('gauge_circle').style.stroke = color;
    document.getElementById('gauge_icon').textContent =
        status === 'danger' ? '🪫' : (status === 'warning' ? '🔋' : '🔋');

    computeKwh();
}

function setAmount(amt) {
    document.getElementById('amount_input').value = amt;
    computeKwh();
}

function computeKwh() {
    const amt     = parseFloat(document.getElementById('amount_input').value);
    const preview = document.getElementById('kwh_preview');

    if (!amt || amt < 500 || !selectedMeterId) {
        preview.style.display = 'none';
        setBtn(false);
        return;
    }

    const kwh    = amt / RATE;
    const newBal = selectedBalance + kwh;

    document.getElementById('kwh_value').textContent  = kwh.toFixed(3) + ' kWh';
    document.getElementById('new_balance').textContent = newBal.toFixed(3) + ' kWh';
    preview.style.display = 'block';
    setBtn(true);
}

function setBtn(enabled) {
    const btn = document.getElementById('buy_btn');
    btn.disabled = !enabled;
    btn.style.opacity = enabled ? '1' : '.5';
    btn.style.cursor  = enabled ? 'pointer' : 'not-allowed';
}

function purchaseCashpower() {
    const amt = parseFloat(document.getElementById('amount_input').value);
    const btn = document.getElementById('buy_btn');
    if (!selectedMeterId || !amt || amt < 500) return;

    btn.disabled = true;
    btn.style.opacity = '.7';
    btn.innerHTML = '<svg style="width:16px;height:16px;animation:spin 1s linear infinite;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> {{ __("messages.loading") }}';

    fetch('{{ route("cashpower.purchase") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ meter_id: selectedMeterId, amount_cfa: amt })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            selectedBalance = data.balance_after;
            document.getElementById('balance_value').textContent = selectedBalance.toFixed(2) + ' kWh';
            document.getElementById('balance-' + selectedMeterId).textContent = selectedBalance.toFixed(2);

            document.getElementById('modal_kwh').textContent    = data.kwh_purchased.toFixed(3) + ' kWh';
            document.getElementById('modal_token').textContent  = data.token_code;
            document.getElementById('modal_ref').textContent    = data.transaction_ref;
            document.getElementById('modal_balance').textContent= data.balance_after.toFixed(3) + ' kWh';
            document.getElementById('success_modal').style.display = 'flex';

            document.getElementById('amount_input').value = '';
            document.getElementById('kwh_preview').style.display = 'none';
        } else {
            alert(data.message);
        }
    })
    .catch(() => alert('{{ __("messages.cashpower_failed") }}'))
    .finally(() => {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.innerHTML = '<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> {{ __("messages.cashpower_buy") }}';
    });
}

function closeModal() {
    document.getElementById('success_modal').style.display = 'none';
    location.reload();
}
</script>
<style>
@keyframes spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
</style>
@endpush
