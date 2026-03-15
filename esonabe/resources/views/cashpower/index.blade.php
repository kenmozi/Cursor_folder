@extends('layouts.app')
@section('title', 'Cash-Power')
@section('subtitle', __('messages.cashpower_buy'))

@section('content')
<div class="space-y-6">

    @if($meters->isEmpty())
    <div class="card">
        <div class="card-body text-center py-16">
            <div class="text-5xl mb-4">⚡</div>
            <h3 class="text-lg font-medium text-gray-700 mb-2">{{ __('messages.cashpower_no_meters') }}</h3>
            <p class="text-sm text-gray-500 mb-4">{{ __('messages.select_cashpower') }}</p>
            <a href="{{ route('contracts.index') }}" class="btn-primary inline-flex">{{ __('messages.contracts') }}</a>
        </div>
    </div>
    @else

    {{-- Purchase form --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Purchase card --}}
        <div class="lg:col-span-3 card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-800">⚡ {{ __('messages.cashpower_buy') }}</h3>
                <span class="text-xs text-gray-400 bg-orange-50 text-orange-700 px-2 py-1 rounded-full">{{ __('messages.kwh_rate') }}</span>
            </div>
            <div class="card-body space-y-5">
                {{-- Meter selection --}}
                <div>
                    <label class="form-label">{{ __('messages.select_cashpower') }} *</label>
                    <select id="meter_select" class="form-input" onchange="updateBalance()">
                        <option value="">-- {{ __('messages.select_cashpower') }} --</option>
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
                <div id="balance_display" class="hidden bg-gray-50 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-500">{{ __('messages.cashpower_balance') }}</div>
                            <div id="balance_value" class="text-3xl font-bold mt-1">0 kWh</div>
                        </div>
                        <div id="balance_icon" class="text-4xl">🔋</div>
                    </div>
                    <div id="balance_label" class="text-sm font-medium mt-2"></div>
                </div>

                {{-- Amount --}}
                <div>
                    <label class="form-label">{{ __('messages.cashpower_amount') }} *</label>
                    <div class="relative">
                        <input type="number" id="amount_input" min="500" max="500000" step="500"
                            class="form-input pr-16" placeholder="500"
                            oninput="computeKwh()">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-500">CFA</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ __('messages.cashpower_min') }} · {{ __('messages.cashpower_max') }}</p>
                    @error('amount_cfa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Quick amounts --}}
                <div>
                    <div class="text-xs text-gray-500 mb-2">Montants rapides:</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach([500, 1000, 2000, 5000, 10000, 25000] as $amt)
                        <button type="button" onclick="setAmount({{ $amt }})"
                            class="px-3 py-1.5 text-xs font-medium border border-gray-200 rounded-lg hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700 transition-all">
                            {{ number_format($amt, 0, ',', ' ') }} CFA
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- KWh equivalent --}}
                <div id="kwh_preview" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-blue-600">Vous obtiendrez</div>
                            <div id="kwh_value" class="text-2xl font-bold text-blue-800 mt-1"></div>
                        </div>
                        <div>
                            <div class="text-xs text-blue-600">Nouveau solde</div>
                            <div id="new_balance" class="text-xl font-bold text-blue-700 mt-1"></div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <button id="buy_btn" onclick="purchaseCashpower()"
                    class="btn-primary w-full justify-center text-base py-3 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    ⚡ {{ __('messages.cashpower_buy') }}
                </button>
            </div>
        </div>

        {{-- Meters overview --}}
        <div class="lg:col-span-2 space-y-4">
            <h3 class="font-semibold text-gray-700">{{ __('messages.meters') }} Cash-Power</h3>
            @foreach($meters as $meter)
            <div class="card" id="meter-card-{{ $meter->id }}">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-mono text-xs text-gray-400">{{ $meter->meter_number }}</div>
                            <div class="font-semibold text-gray-900">{{ $meter->contract->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $meter->class_label }} · {{ $meter->amperage }}A</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-xl {{ $meter->balance_color_class }}" id="balance-{{ $meter->id }}">
                                {{ number_format($meter->cashpower_balance_kwh, 2) }} kWh
                            </div>
                        </div>
                    </div>

                    {{-- Balance bar --}}
                    @php $pct = min(100, ($meter->cashpower_balance_kwh / 100) * 100); @endphp
                    <div class="mt-3 h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all
                            @if($meter->balance_status === 'danger') bg-red-500
                            @elseif($meter->balance_status === 'warning') bg-orange-400
                            @else bg-green-500 @endif"
                            style="width: {{ $pct }}%"></div>
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <div class="text-xs font-medium
                            @if($meter->balance_status === 'danger') text-red-600
                            @elseif($meter->balance_status === 'warning') text-orange-500
                            @else text-green-600 @endif">
                            @if($meter->balance_status === 'danger') ⚠ {{ __('messages.balance_low') }}
                            @elseif($meter->balance_status === 'warning') ⚠ {{ __('messages.balance_medium') }}
                            @else ✓ {{ __('messages.balance_high') }} @endif
                        </div>
                        <a href="{{ route('cashpower.history', $meter) }}" class="text-xs text-blue-600 hover:underline">{{ __('messages.cashpower_history') }}</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Success modal --}}
    <div id="success_modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
            <div class="text-center">
                <div class="text-6xl mb-4">✅</div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('messages.cashpower_purchased') }}</h3>
                <div class="bg-green-50 rounded-xl p-4 mb-4">
                    <div class="text-sm text-gray-600">{{ __('messages.cashpower_kwh') }}</div>
                    <div id="modal_kwh" class="text-3xl font-bold text-green-700 mt-1"></div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 mb-4 text-left">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">{{ __('messages.cashpower_token') }}</span>
                        <span id="modal_token" class="font-mono font-bold text-blue-700 text-xs"></span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">{{ __('messages.transaction_ref') }}</span>
                        <span id="modal_ref" class="font-mono text-xs text-gray-600"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">{{ __('messages.cashpower_balance') }}</span>
                        <span id="modal_balance" class="font-bold"></span>
                    </div>
                </div>
                <button onclick="closeModal()" class="btn-primary w-full justify-center">
                    {{ __('messages.confirm') }}
                </button>
            </div>
        </div>
    </div>

    @endif
</div>
@endsection

@push('scripts')
<script>
const RATE = 131; // 1 kWh = 131 CFA
let selectedMeterId   = null;
let selectedBalance   = 0;

function updateBalance() {
    const sel = document.getElementById('meter_select');
    const opt = sel.options[sel.selectedIndex];
    const display = document.getElementById('balance_display');

    if (!opt.value) {
        display.classList.add('hidden');
        selectedMeterId = null;
        document.getElementById('buy_btn').disabled = true;
        return;
    }

    selectedMeterId = opt.value;
    selectedBalance = parseFloat(opt.dataset.balance);
    const status    = opt.dataset.status;

    display.classList.remove('hidden');
    document.getElementById('balance_value').textContent = selectedBalance.toFixed(2) + ' kWh';

    const colorMap = { danger: 'text-red-600', warning: 'text-orange-500', success: 'text-green-600' };
    const labelMap = {
        danger:  '⚠ {{ __("messages.balance_low") }}',
        warning: '⚠ {{ __("messages.balance_medium") }}',
        success: '✓ {{ __("messages.balance_high") }}'
    };
    document.getElementById('balance_value').className = 'text-3xl font-bold mt-1 ' + colorMap[status];
    document.getElementById('balance_label').textContent = labelMap[status];
    document.getElementById('balance_label').className = 'text-sm font-medium mt-2 ' + colorMap[status];

    computeKwh();
}

function setAmount(amt) {
    document.getElementById('amount_input').value = amt;
    computeKwh();
}

function computeKwh() {
    const amt = parseFloat(document.getElementById('amount_input').value);
    const btn = document.getElementById('buy_btn');
    const preview = document.getElementById('kwh_preview');

    if (!amt || amt < 500 || !selectedMeterId) {
        preview.classList.add('hidden');
        btn.disabled = true;
        return;
    }

    const kwh = (amt / RATE);
    const newBal = selectedBalance + kwh;

    document.getElementById('kwh_value').textContent = kwh.toFixed(3) + ' kWh';
    document.getElementById('new_balance').textContent = newBal.toFixed(3) + ' kWh';
    preview.classList.remove('hidden');
    btn.disabled = false;
}

function purchaseCashpower() {
    const amt = parseFloat(document.getElementById('amount_input').value);
    const btn = document.getElementById('buy_btn');

    if (!selectedMeterId || !amt || amt < 500) return;

    btn.disabled = true;
    btn.textContent = '{{ __("messages.loading") }}';

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
            // Update balance display
            selectedBalance = data.balance_after;
            document.getElementById('balance_value').textContent = selectedBalance.toFixed(2) + ' kWh';
            document.getElementById('balance-' + selectedMeterId).textContent = selectedBalance.toFixed(2) + ' kWh';

            // Show modal
            document.getElementById('modal_kwh').textContent = data.kwh_purchased.toFixed(3) + ' kWh';
            document.getElementById('modal_token').textContent = data.token_code;
            document.getElementById('modal_ref').textContent = data.transaction_ref;
            document.getElementById('modal_balance').textContent = data.balance_after.toFixed(3) + ' kWh';
            document.getElementById('success_modal').classList.remove('hidden');

            document.getElementById('amount_input').value = '';
            document.getElementById('kwh_preview').classList.add('hidden');
        } else {
            alert(data.message);
        }
    })
    .catch(() => alert('{{ __("messages.cashpower_failed") }}'))
    .finally(() => {
        btn.disabled = false;
        btn.textContent = '⚡ {{ __("messages.cashpower_buy") }}';
    });
}

function closeModal() {
    document.getElementById('success_modal').classList.add('hidden');
    location.reload();
}
</script>
@endpush
