@extends('layouts.app')
@section('title', __('messages.contracts'))
@section('header-actions')
    <a href="{{ route('contracts.create') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        {{ __('messages.add_contract') }}
    </a>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:12px;">
    @forelse($contracts as $contract)
    <div class="card">
        <div class="card-body" style="display:flex; align-items:center; justify-content:space-between; gap:16px;">
            <div style="display:flex; align-items:center; gap:16px; min-width:0;">
                <div style="width:48px; height:48px; background:#D32F2F; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; font-weight:800; color:#fff; flex-shrink:0;">
                    {{ strtoupper(substr($contract->name, 0, 1)) }}
                </div>
                <div style="min-width:0;">
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <h3 style="margin:0; font-size:1rem; font-weight:800; color:#111;">{{ $contract->name }}</h3>
                        @if($contract->status === 'active')
                            <span class="badge badge-active">{{ __('messages.status_active') }}</span>
                        @else
                            <span class="badge badge-inactive">{{ __('messages.status_'.$contract->status) }}</span>
                        @endif
                    </div>
                    <div style="font-size:.76rem; color:#9ca3af; font-family:monospace; margin-top:3px;">{{ $contract->contract_number }}</div>
                    <div style="display:flex; align-items:center; gap:16px; margin-top:8px; flex-wrap:wrap;">
                        <span style="display:inline-flex; align-items:center; gap:5px; font-size:.78rem; color:#6b7280;">
                            <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            {{ $contract->address }}, {{ $contract->city }}
                        </span>
                        <span style="display:inline-flex; align-items:center; gap:5px; font-size:.78rem; color:#6b7280;">
                            <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            {{ $contract->meters->count() }} {{ __('messages.meters') }}
                        </span>
                        <span style="display:inline-flex; align-items:center; gap:5px; font-size:.78rem; color:#6b7280;">
                            <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ __('messages.contract_start') }}: {{ $contract->start_date->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-secondary btn-sm">{{ __('messages.view') }}</a>
                <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-secondary btn-sm">{{ __('messages.edit') }}</a>
                <form action="{{ route('contracts.destroy', $contract) }}" method="POST"
                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body" style="text-align:center; padding:64px 24px;">
            <div style="font-size:3.5rem; margin-bottom:16px;">📋</div>
            <h3 style="margin:0 0 8px; color:#374151;">{{ __('messages.no_contracts') }}</h3>
            <p style="margin:0 0 20px; color:#9ca3af; font-size:.88rem;">Ajoutez votre premier contrat pour commencer</p>
            <a href="{{ route('contracts.create') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                {{ __('messages.add_contract') }}
            </a>
        </div>
    </div>
    @endforelse
</div>
@endsection
