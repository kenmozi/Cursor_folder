<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $user      = Auth::user();
        $contracts = $user->contracts()->pluck('id');

        $query = Bill::whereIn('contract_id', $contracts)
            ->with(['contract', 'meter'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bills = $query->paginate(10);

        return view('bills.index', compact('bills'));
    }

    public function show(Bill $bill)
    {
        $this->authorize('view', $bill->contract);
        $bill->load(['contract', 'meter']);
        return view('bills.show', compact('bill'));
    }

    public function pay(Bill $bill)
    {
        $this->authorize('view', $bill->contract);

        if ($bill->status !== 'pending') {
            return back()->with('error', __('messages.bill_already_paid'));
        }

        $bill->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()->route('bills.show', $bill)
            ->with('success', __('messages.bill_paid'));
    }
}
