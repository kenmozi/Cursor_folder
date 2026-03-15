@extends('layouts.app')
@section('title', __('messages.contracts'))
@section('header-actions')
    <a href="{{ route('contracts.create') }}" class="btn-primary">+ {{ __('messages.add_contract') }}</a>
@endsection

@section('content')
<div class="space-y-4">
    @forelse($contracts as $contract)
    <div class="card">
        <div class="card-body">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-700 font-bold text-xl">
                        {{ strtoupper(substr($contract->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-900 text-lg">{{ $contract->name }}</h3>
                            @if($contract->status === 'active')
                                <span class="badge-active">{{ __('messages.status_active') }}</span>
                            @else
                                <span class="badge-inactive">{{ __('messages.status_'.$contract->status) }}</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500 mt-0.5">
                            <span class="font-mono">{{ $contract->contract_number }}</span> ·
                            {{ $contract->address }}, {{ $contract->city }}
                        </div>
                        <div class="flex items-center gap-4 mt-3 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">{{ $contract->meters->count() }}</span> {{ __('messages.meters') }}
                            </div>
                            <div>
                                <span class="font-medium">{{ $contract->bills->count() }}</span> {{ __('messages.bills') }}
                            </div>
                            <div>
                                {{ __('messages.contract_start') }}: <span class="font-medium">{{ $contract->start_date->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('contracts.show', $contract) }}" class="btn-secondary text-xs">{{ __('messages.view') }}</a>
                    <a href="{{ route('contracts.edit', $contract) }}" class="btn-secondary text-xs">{{ __('messages.edit') }}</a>
                    <form action="{{ route('contracts.destroy', $contract) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger text-xs">{{ __('messages.delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body text-center py-16">
            <div class="text-5xl mb-4">📋</div>
            <h3 class="text-lg font-medium text-gray-700 mb-2">{{ __('messages.no_contracts') }}</h3>
            <a href="{{ route('contracts.create') }}" class="btn-primary inline-flex">+ {{ __('messages.add_contract') }}</a>
        </div>
    </div>
    @endforelse
</div>
@endsection
