<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Meter;
use App\Services\ForecastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeterController extends Controller
{
    public function create(Contract $contract)
    {
        $this->authorize('update', $contract);
        $classes = Meter::CLASSES;
        return view('meters.create', compact('contract', 'classes'));
    }

    public function store(Request $request, Contract $contract)
    {
        $this->authorize('update', $contract);

        $request->validate([
            'type'        => 'required|in:NORMAL,CASHPOWER',
            'meter_class' => 'required|in:A,B1,B2,C1,C2',
            'amperage'    => ['required', 'integer', function ($attr, $value, $fail) use ($request) {
                $class = $request->meter_class;
                $opts  = Meter::amperageOptions($class);
                if (!in_array((int) $value, $opts)) {
                    $fail(__('validation.amperage_invalid'));
                }
            }],
            'serial_number' => 'nullable|string|max:100',
        ]);

        $meter = $contract->meters()->create([
            'meter_number'         => Meter::generateMeterNumber(),
            'serial_number'        => $request->serial_number,
            'type'                 => $request->type,
            'meter_class'          => $request->meter_class,
            'amperage'             => $request->amperage,
            'cashpower_balance_kwh'=> 0,
            'status'               => 'active',
        ]);

        return redirect()->route('contracts.show', $contract)
            ->with('success', __('messages.meter_added'));
    }

    public function show(Meter $meter, ForecastService $forecastService)
    {
        $this->authorize('view', $meter->contract);

        $meter->load(['contract', 'consumptionRecords', 'cashpowerTransactions', 'bills']);

        $records = $meter->consumptionRecords()
            ->orderBy('recorded_date')
            ->get();

        $forecast7  = $forecastService->forecast($meter, 7);
        $forecast30 = $forecastService->forecast($meter, 30);
        $stats      = $forecastService->stats($meter);

        $chartLabels   = $records->pluck('recorded_date')->map(fn($d) => $d->format('d/m/Y'))->toArray();
        $chartDataKwh  = $records->pluck('kwh_consumed')->map(fn($v) => (float) $v)->toArray();

        $forecastLabels = collect($forecast7)->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray();
        $forecastData   = collect($forecast7)->pluck('kwh')->toArray();

        return view('meters.show', compact(
            'meter',
            'records',
            'forecast7',
            'forecast30',
            'stats',
            'chartLabels',
            'chartDataKwh',
            'forecastLabels',
            'forecastData'
        ));
    }

    public function destroy(Meter $meter)
    {
        $this->authorize('update', $meter->contract);
        $meter->delete();

        return redirect()->route('contracts.show', $meter->contract)
            ->with('success', __('messages.meter_deleted'));
    }

    public function amperageOptions(Request $request)
    {
        $class = $request->query('class');
        return response()->json(Meter::amperageOptions($class));
    }
}
