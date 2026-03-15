@extends('layouts.app')
@section('title', __('messages.add_meter'))
@section('subtitle', $contract->name)

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">{{ __('messages.add_meter') }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('meters.store', $contract) }}" method="POST" class="space-y-5">
                @csrf

                {{-- Meter type --}}
                <div>
                    <label class="form-label">{{ __('messages.meter_type') }} *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="NORMAL" class="sr-only peer" {{ old('type', 'NORMAL') === 'NORMAL' ? 'checked' : '' }}>
                            <div class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300 transition-all">
                                <div class="text-3xl mb-2">📊</div>
                                <div class="font-semibold text-sm">Normal</div>
                                <div class="text-xs text-gray-500 text-center">{{ __('messages.normal_meter') }}</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="CASHPOWER" class="sr-only peer" {{ old('type') === 'CASHPOWER' ? 'checked' : '' }}>
                            <div class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300 transition-all">
                                <div class="text-3xl mb-2">⚡</div>
                                <div class="font-semibold text-sm">Cash-Power</div>
                                <div class="text-xs text-gray-500 text-center">{{ __('messages.cashpower_meter') }}</div>
                            </div>
                        </label>
                    </div>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Meter class --}}
                <div>
                    <label class="form-label">{{ __('messages.meter_class') }} *</label>
                    <select name="meter_class" id="meter_class" required class="form-input" onchange="updateAmperage()">
                        <option value="">{{ __('messages.select_meter_class') }}</option>
                        @foreach($classes as $key => $info)
                        <option value="{{ $key }}" {{ old('meter_class') === $key ? 'selected' : '' }}>
                            {{ $info['label'] }} ({{ $info['min_amp'] }}A – {{ $info['max_amp'] }}A)
                        </option>
                        @endforeach
                    </select>
                    @error('meter_class') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Amperage --}}
                <div>
                    <label class="form-label">{{ __('messages.meter_amperage') }} *</label>
                    <select name="amperage" id="amperage" required class="form-input">
                        <option value="">{{ __('messages.select_amperage') }}</option>
                    </select>
                    @error('amperage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Serial number --}}
                <div>
                    <label class="form-label">{{ __('messages.serial_number') }}</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" class="form-input" placeholder="Ex: SN-202400001">
                    @error('serial_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Classes config for JS --}}
                <div id="classes-data" data-classes="{{ json_encode($classes) }}" class="hidden"></div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">{{ __('messages.save') }}</button>
                    <a href="{{ route('contracts.show', $contract) }}" class="btn-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const classes = JSON.parse(document.getElementById('classes-data').dataset.classes);

function updateAmperage() {
    const classKey = document.getElementById('meter_class').value;
    const select   = document.getElementById('amperage');
    const oldVal   = "{{ old('amperage') }}";

    select.innerHTML = '<option value="">{{ __("messages.select_amperage") }}</option>';

    if (!classKey || !classes[classKey]) return;

    const { min_amp, max_amp } = classes[classKey];
    for (let a = min_amp; a <= max_amp; a++) {
        const opt = document.createElement('option');
        opt.value = a;
        opt.textContent = a + 'A';
        if (String(a) === oldVal) opt.selected = true;
        select.appendChild(opt);
    }
}

// Initialize if old value exists
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('meter_class').value) updateAmperage();
});
</script>
@endpush
