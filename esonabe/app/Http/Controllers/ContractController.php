<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Auth::user()->contracts()->with(['meters', 'bills'])->latest()->get();
        return view('contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('contracts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'address'    => 'required|string|max:500',
            'city'       => 'required|string|max:100',
            'start_date' => 'required|date',
        ]);

        $contract = Auth::user()->contracts()->create([
            'contract_number' => Contract::generateContractNumber(),
            'name'            => $request->name,
            'address'         => $request->address,
            'city'            => $request->city,
            'start_date'      => $request->start_date,
            'status'          => 'active',
        ]);

        return redirect()->route('contracts.show', $contract)
            ->with('success', __('messages.contract_created'));
    }

    public function show(Contract $contract)
    {
        $this->authorize('view', $contract);
        $contract->load(['meters', 'bills' => fn($q) => $q->orderBy('created_at', 'desc')->limit(10)]);
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $this->authorize('update', $contract);
        return view('contracts.edit', compact('contract'));
    }

    public function update(Request $request, Contract $contract)
    {
        $this->authorize('update', $contract);

        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city'    => 'required|string|max:100',
        ]);

        $contract->update($request->only(['name', 'address', 'city']));

        return redirect()->route('contracts.show', $contract)
            ->with('success', __('messages.contract_updated'));
    }

    public function destroy(Contract $contract)
    {
        $this->authorize('delete', $contract);
        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', __('messages.contract_deleted'));
    }
}
