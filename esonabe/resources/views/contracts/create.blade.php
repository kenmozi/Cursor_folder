@extends('layouts.app')
@section('title', __('messages.add_contract'))

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">{{ __('messages.add_contract') }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('contracts.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">{{ __('messages.contract_name') }} *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="Ex: Résidence Principale">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('messages.contract_address') }} *</label>
                    <input type="text" name="address" value="{{ old('address') }}" required class="form-input" placeholder="123 Rue de l'Indépendance">
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('messages.contract_city') }} *</label>
                    <input type="text" name="city" value="{{ old('city') }}" required class="form-input" placeholder="Yaoundé">
                    @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('messages.contract_start') }} *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="form-input">
                    @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">{{ __('messages.save') }}</button>
                    <a href="{{ route('contracts.index') }}" class="btn-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
