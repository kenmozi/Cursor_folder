@extends('layouts.app')
@section('title', __('messages.add_contract'))
@section('header-actions')
    <a href="{{ route('contracts.index') }}" class="btn btn-secondary btn-sm">← {{ __('messages.back') }}</a>
@endsection

@section('content')
<div style="max-width:560px;">
    <div class="card">
        <div style="height:3px; background:linear-gradient(90deg,#D32F2F,#FFD600,#2E7D32);"></div>
        <div class="card-header">
            <h3>{{ __('messages.add_contract') }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('contracts.store') }}" method="POST" style="display:flex; flex-direction:column; gap:16px;">
                @csrf

                <div>
                    <label class="form-label">{{ __('messages.contract_name') }} *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Ex: Résidence Principale">
                    @error('name') <p style="color:#dc2626;font-size:.76rem;margin:4px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('messages.contract_address') }} *</label>
                    <input type="text" name="address" value="{{ old('address') }}" required class="form-input" placeholder="123 Rue de l'Indépendance">
                    @error('address') <p style="color:#dc2626;font-size:.76rem;margin:4px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('messages.contract_city') }} *</label>
                    <input type="text" name="city" value="{{ old('city') }}" required class="form-input" placeholder="Ouagadougou">
                    @error('city') <p style="color:#dc2626;font-size:.76rem;margin:4px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('messages.contract_start') }} *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="form-input">
                    @error('start_date') <p style="color:#dc2626;font-size:.76rem;margin:4px 0 0;">{{ $message }}</p> @enderror
                </div>

                <div style="display:flex; gap:10px; padding-top:4px;">
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ __('messages.save') }}
                    </button>
                    <a href="{{ route('contracts.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
