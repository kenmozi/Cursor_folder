@extends('layouts.app')
@section('title', __('messages.edit_contract'))

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <h3 class="font-semibold text-gray-800">{{ __('messages.edit_contract') }}: {{ $contract->name }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('contracts.update', $contract) }}" method="POST" class="space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="form-label">{{ __('messages.contract_name') }} *</label>
                    <input type="text" name="name" value="{{ old('name', $contract->name) }}" required class="form-input">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('messages.contract_address') }} *</label>
                    <input type="text" name="address" value="{{ old('address', $contract->address) }}" required class="form-input">
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('messages.contract_city') }} *</label>
                    <input type="text" name="city" value="{{ old('city', $contract->city) }}" required class="form-input">
                    @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">{{ __('messages.save') }}</button>
                    <a href="{{ route('contracts.show', $contract) }}" class="btn-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
