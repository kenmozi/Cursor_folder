@extends('layouts.app')
@section('title', __('messages.add_meter'))
@section('subtitle', $contract->name)
@section('header-actions')
    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-secondary btn-sm">← {{ __('messages.back') }}</a>
@endsection

@section('content')
<div style="max-width:580px;">
    <div class="card">
        <div style="height:3px; background:linear-gradient(90deg,#D32F2F,#FFD600,#2E7D32);"></div>
        <div class="card-header">
            <h3>{{ __('messages.add_meter') }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('meters.store', $contract) }}" method="POST" style="display:flex; flex-direction:column; gap:20px;">
                @csrf

                {{-- Meter type selector --}}
                <div>
                    <label class="form-label">{{ __('messages.meter_type') }} *</label>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:2px;">
                        <label style="cursor:pointer;">
                            <input type="radio" name="type" value="NORMAL" class="sr-only"
                                   {{ old('type','NORMAL') === 'NORMAL' ? 'checked' : '' }}
                                   onchange="this.closest('form').querySelectorAll('.type-card').forEach(c=>c.classList.remove('selected')); this.closest('label').querySelector('.type-card').classList.add('selected')">
                            <div class="type-card {{ old('type','NORMAL') === 'NORMAL' ? 'selected' : '' }}"
                                 style="border:2px solid #e5e7eb; border-radius:12px; padding:16px; text-align:center; transition:border-color .15s, background .15s;">
                                <div style="font-size:2rem; margin-bottom:6px;">📊</div>
                                <div style="font-weight:700; font-size:.9rem; color:#111;">Normal</div>
                                <div style="font-size:.73rem; color:#9ca3af; margin-top:3px;">{{ __('messages.normal_meter') }}</div>
                            </div>
                        </label>
                        <label style="cursor:pointer;">
                            <input type="radio" name="type" value="CASHPOWER" class="sr-only"
                                   {{ old('type') === 'CASHPOWER' ? 'checked' : '' }}
                                   onchange="this.closest('form').querySelectorAll('.type-card').forEach(c=>c.classList.remove('selected')); this.closest('label').querySelector('.type-card').classList.add('selected')">
                            <div class="type-card {{ old('type') === 'CASHPOWER' ? 'selected' : '' }}"
                                 style="border:2px solid #e5e7eb; border-radius:12px; padding:16px; text-align:center; transition:border-color .15s, background .15s;">
                                <div style="font-size:2rem; margin-bottom:6px;">⚡</div>
                                <div style="font-weight:700; font-size:.9rem; color:#111;">Cash-Power</div>
                                <div style="font-size:.73rem; color:#9ca3af; margin-top:3px;">{{ __('messages.cashpower_meter') }}</div>
                            </div>
                        </label>
                    </div>
                    @error('type') <p style="color:#dc2626;font-size:.76rem;margin:4px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div>
                        <label class="form-label">{{ __('messages.meter_class') }} *</label>
                        <select name="meter_class" id="meter_class" required class="form-input" onchange="updateAmperage()">
                            <option value="">{{ __('messages.select_meter_class') }}</option>
                            @foreach($classes as $key => $info)
                            <option value="{{ $key }}" {{ old('meter_class') === $key ? 'selected' : '' }}>
                                Type {{ $key }} — {{ $info['label'] }}
                            </option>
                            @endforeach
                        </select>
                        <p style="font-size:.72rem; color:#9ca3af; margin:4px 0 0;">A/B1/B2/C1/C2</p>
                        @error('meter_class') <p style="color:#dc2626;font-size:.76rem;margin:4px 0 0;">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">{{ __('messages.meter_amperage') }} *</label>
                        <select name="amperage" id="amperage" required class="form-input">
                            <option value="">{{ __('messages.select_amperage') }}</option>
                        </select>
                        @error('amperage') <p style="color:#dc2626;font-size:.76rem;margin:4px 0 0;">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Class info display --}}
                <div id="class_info" style="display:none; background:#fafafa; border:1px solid #f0f0f0; border-radius:10px; padding:12px 14px; font-size:.82rem; color:#374151;">
                    <strong>Informations sur la classe sélectionnée</strong>
                    <div id="class_info_text" style="margin-top:4px; color:#6b7280;"></div>
                </div>

                <div>
                    <label class="form-label">{{ __('messages.serial_number') }}</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" class="form-input" placeholder="Ex: SN-202400001">
                    @error('serial_number') <p style="color:#dc2626;font-size:.76rem;margin:4px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div id="classes-data" data-classes="{{ json_encode($classes) }}" style="display:none;"></div>

                <div style="display:flex; gap:10px; padding-top:4px; border-top:1px solid #f0f0f0;">
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ __('messages.save') }}
                    </button>
                    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.type-card.selected { border-color: #D32F2F !important; background: #fef2f2; }
.sr-only { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
</style>

@push('scripts')
<script>
const classes = JSON.parse(document.getElementById('classes-data').dataset.classes);
const classLabels = {
    'A':  'Monophasé social — usage domestique simple',
    'B1': 'Monophasé normal — usage résidentiel standard',
    'B2': 'Monophasé spécial — usage résidentiel renforcé',
    'C1': 'Triphasé normal — usage commercial/industriel',
    'C2': 'Triphasé spécial — usage industriel renforcé',
};

function updateAmperage() {
    const classKey = document.getElementById('meter_class').value;
    const select   = document.getElementById('amperage');
    const info     = document.getElementById('class_info');
    const infoText = document.getElementById('class_info_text');
    const oldVal   = "{{ old('amperage') }}";

    select.innerHTML = '<option value="">{{ __("messages.select_amperage") }}</option>';

    if (!classKey || !classes[classKey]) {
        info.style.display = 'none';
        return;
    }

    const { min_amp, max_amp } = classes[classKey];
    for (let a = min_amp; a <= max_amp; a++) {
        const opt = document.createElement('option');
        opt.value = a;
        opt.textContent = a + 'A';
        if (String(a) === oldVal) opt.selected = true;
        select.appendChild(opt);
    }

    info.style.display = 'block';
    infoText.textContent = classLabels[classKey] + ` · Plage ampérage: ${min_amp}A–${max_amp}A`;
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('meter_class').value) updateAmperage();
});
</script>
@endpush
@endsection
