<?php

namespace App\Http\Controllers;

use App\Models\CashpowerTransaction;
use App\Models\Meter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashpowerController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $contracts = $user->contracts()->pluck('id');

        $meters = Meter::whereIn('contract_id', $contracts)
            ->where('type', 'CASHPOWER')
            ->with('contract')
            ->get();

        return view('cashpower.index', compact('meters'));
    }

    public function purchase(Request $request)
    {
        $user      = Auth::user();
        $contracts = $user->contracts()->pluck('id');

        $request->validate([
            'meter_id'   => 'required|exists:meters,id',
            'amount_cfa' => 'required|numeric|min:' . Meter::MIN_PURCHASE_CFA . '|max:' . Meter::MAX_PURCHASE_CFA,
        ]);

        $meter = Meter::whereIn('contract_id', $contracts)
            ->where('type', 'CASHPOWER')
            ->findOrFail($request->meter_id);

        $amountCfa   = (float) $request->amount_cfa;
        $kwhPurchased = round($amountCfa / Meter::KWH_RATE_CFA, 3);

        DB::beginTransaction();
        try {
            $balanceBefore = (float) $meter->cashpower_balance_kwh;
            $balanceAfter  = round($balanceBefore + $kwhPurchased, 3);

            // Simulate API call delay (would be a real payment gateway in production)
            $transaction = CashpowerTransaction::create([
                'meter_id'           => $meter->id,
                'user_id'            => $user->id,
                'transaction_ref'    => CashpowerTransaction::generateRef(),
                'amount_cfa'         => $amountCfa,
                'kwh_purchased'      => $kwhPurchased,
                'balance_before_kwh' => $balanceBefore,
                'balance_after_kwh'  => $balanceAfter,
                'status'             => 'completed',
                'payment_method'     => 'simulated',
                'token_code'         => CashpowerTransaction::generateToken(),
                'completed_at'       => now(),
            ]);

            $meter->update(['cashpower_balance_kwh' => $balanceAfter]);

            DB::commit();

            return response()->json([
                'success'         => true,
                'message'         => __('messages.cashpower_purchased'),
                'kwh_purchased'   => $kwhPurchased,
                'balance_after'   => $balanceAfter,
                'token_code'      => $transaction->token_code,
                'transaction_ref' => $transaction->transaction_ref,
                'balance_status'  => $meter->fresh()->balance_status,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('messages.cashpower_failed'),
            ], 500);
        }
    }

    public function history(Meter $meter)
    {
        $this->authorize('view', $meter->contract);

        $transactions = $meter->cashpowerTransactions()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('cashpower.history', compact('meter', 'transactions'));
    }
}
