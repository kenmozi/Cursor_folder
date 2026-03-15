<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\ConsumptionRecord;
use App\Models\Meter;
use App\Services\ForecastService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(ForecastService $forecastService)
    {
        $user = Auth::user();
        $contracts = $user->contracts()->with(['meters', 'bills'])->get();

        $totalContracts = $contracts->count();
        $totalMeters    = $contracts->sum(fn($c) => $c->meters->count());

        $pendingBills = Bill::whereIn('contract_id', $contracts->pluck('id'))
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get();

        $totalPending = $pendingBills->sum('total_cfa');

        // Collect all meters for consumption overview
        $allMeterIds = $contracts->flatMap(fn($c) => $c->meters->pluck('id'));

        $last30Days = ConsumptionRecord::whereIn('meter_id', $allMeterIds)
            ->where('recorded_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('recorded_date')
            ->get()
            ->groupBy(fn($r) => $r->recorded_date->format('Y-m-d'));

        $chartLabels = [];
        $chartData   = [];
        for ($i = 29; $i >= 0; $i--) {
            $date            = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[]   = Carbon::parse($date)->format('d/m');
            $chartData[]     = $last30Days->get($date)?->sum('kwh_consumed') ?? 0;
        }

        // Cashpower meters summary
        $cashpowerMeters = Meter::whereIn('contract_id', $contracts->pluck('id'))
            ->where('type', 'CASHPOWER')
            ->get();

        return view('dashboard', compact(
            'contracts',
            'totalContracts',
            'totalMeters',
            'pendingBills',
            'totalPending',
            'chartLabels',
            'chartData',
            'cashpowerMeters'
        ));
    }
}
