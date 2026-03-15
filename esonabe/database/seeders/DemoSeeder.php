<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\CashpowerTransaction;
use App\Models\ConsumptionRecord;
use App\Models\Contract;
use App\Models\Meter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Demo user
        $user = User::firstOrCreate(
            ['email' => 'demo@esonabe.cm'],
            [
                'name'     => 'Jean-Pierre Kamga',
                'password' => Hash::make('password'),
                'phone'    => '+237 677 123 456',
                'locale'   => 'fr',
            ]
        );

        // Contract 1 – House
        $contract1 = Contract::create([
            'user_id'         => $user->id,
            'contract_number' => 'CTR-DEMO0001',
            'name'            => 'Résidence Principale',
            'address'         => 'Rue des Palmiers, Quartier Bastos',
            'city'            => 'Yaoundé',
            'status'          => 'active',
            'start_date'      => '2022-03-15',
        ]);

        // Normal meter on contract 1
        $normalMeter = Meter::create([
            'contract_id'          => $contract1->id,
            'meter_number'         => 'MTR-NORM0001',
            'serial_number'        => 'SN-2022-B1-001',
            'type'                 => 'NORMAL',
            'meter_class'          => 'B1',
            'amperage'             => 15,
            'cashpower_balance_kwh'=> 0,
            'status'               => 'active',
            'last_reading_kwh'     => 3240.5,
            'last_reading_at'      => now()->subDays(3),
        ]);

        // CashPower meter on contract 1
        $cashpowerMeter = Meter::create([
            'contract_id'          => $contract1->id,
            'meter_number'         => 'MTR-CP000001',
            'serial_number'        => 'SN-2023-A-002',
            'type'                 => 'CASHPOWER',
            'meter_class'          => 'A',
            'amperage'             => 2,
            'cashpower_balance_kwh'=> 35.5,
            'status'               => 'active',
            'last_reading_kwh'     => 0,
            'last_reading_at'      => now()->subDays(1),
        ]);

        // Contract 2 – Shop
        $contract2 = Contract::create([
            'user_id'         => $user->id,
            'contract_number' => 'CTR-DEMO0002',
            'name'            => 'Boutique Marché Central',
            'address'         => 'Place du Marché Central',
            'city'            => 'Douala',
            'status'          => 'active',
            'start_date'      => '2021-07-01',
        ]);

        // C1 meter on contract 2
        $shopMeter = Meter::create([
            'contract_id'          => $contract2->id,
            'meter_number'         => 'MTR-C1-00001',
            'serial_number'        => 'SN-2021-C1-003',
            'type'                 => 'NORMAL',
            'meter_class'          => 'C1',
            'amperage'             => 20,
            'cashpower_balance_kwh'=> 0,
            'status'               => 'active',
            'last_reading_kwh'     => 12480.0,
            'last_reading_at'      => now()->subDays(2),
        ]);

        // CashPower B2 on contract 2
        $shopCashpower = Meter::create([
            'contract_id'          => $contract2->id,
            'meter_number'         => 'MTR-CP000002',
            'serial_number'        => 'SN-2022-B2-004',
            'type'                 => 'CASHPOWER',
            'meter_class'          => 'B2',
            'amperage'             => 10,
            'cashpower_balance_kwh'=> 8.3,  // Low – will be red
            'status'               => 'active',
            'last_reading_kwh'     => 0,
            'last_reading_at'      => now()->subDays(1),
        ]);

        // Generate 90 days of consumption for normal meter
        $this->generateConsumption($normalMeter, 90, 12, 5);
        // Generate consumption for shop meter (higher)
        $this->generateConsumption($shopMeter, 90, 45, 15);
        // Consumption for cashpower meters (tracked separately)
        $this->generateConsumption($cashpowerMeter, 90, 3, 1);
        $this->generateConsumption($shopCashpower, 60, 18, 8);

        // Bills for normal meters
        $this->generateBills($contract1, $normalMeter);
        $this->generateBills($contract2, $shopMeter);

        // CashPower transactions
        $this->generateCashpowerHistory($user, $cashpowerMeter);
        $this->generateCashpowerHistory($user, $shopCashpower, 3);
    }

    private function generateConsumption(Meter $meter, int $days, float $avgKwh, float $variance): void
    {
        $reading = (float) $meter->last_reading_kwh;
        // Go back from today
        $records = [];
        $date    = Carbon::now()->subDays($days);

        while ($date->lte(Carbon::now()->subDays(1))) {
            // Add seasonal/weekday variance
            $dow     = (int) $date->format('N'); // 1=Mon...7=Sun
            $dayMult = in_array($dow, [6, 7]) ? 0.7 : 1.0; // lower on weekends
            $rand    = max(0.1, $avgKwh * $dayMult + mt_rand((int) (-$variance * 10), (int) ($variance * 10)) / 10);

            try {
                ConsumptionRecord::create([
                    'meter_id'      => $meter->id,
                    'recorded_date' => $date->format('Y-m-d'),
                    'kwh_consumed'  => round($rand, 3),
                    'meter_reading' => round($reading + $rand, 3),
                    'source'        => $meter->isCashpower() ? 'smart' : 'manual',
                ]);
            } catch (\Exception $e) {
                // Skip duplicates
            }

            $reading += $rand;
            $date->addDay();
        }

        $meter->update(['last_reading_kwh' => round($reading, 3)]);
    }

    private function generateBills(Contract $contract, Meter $meter): void
    {
        $periods = [
            ['2024-10-01', '2024-10-31', '2024-11-15', 'paid'],
            ['2024-11-01', '2024-11-30', '2024-12-15', 'paid'],
            ['2024-12-01', '2024-12-31', '2025-01-15', 'paid'],
            ['2025-01-01', '2025-01-31', '2025-02-15', 'paid'],
            ['2025-02-01', '2025-02-28', '2025-03-15', 'pending'],
        ];

        $reading = 1000.0;
        foreach ($periods as [$start, $end, $due, $status]) {
            $days        = Carbon::parse($start)->diffInDays(Carbon::parse($end));
            $consumption = round(rand(300, 800) + mt_rand(0, 500) / 10, 2);
            $amount      = round($consumption * 131, 2);
            $taxes       = round($amount * 0.185, 2);
            $total       = $amount + $taxes;

            $bill = Bill::create([
                'contract_id'     => $contract->id,
                'meter_id'        => $meter->id,
                'bill_number'     => Bill::generateBillNumber(),
                'period_start'    => $start,
                'period_end'      => $end,
                'due_date'        => $due,
                'consumption_kwh' => $consumption,
                'amount_cfa'      => $amount,
                'taxes_cfa'       => $taxes,
                'total_cfa'       => $total,
                'status'          => $status,
                'paid_at'         => $status === 'paid' ? Carbon::parse($due)->subDays(rand(1, 10)) : null,
                'reading_start'   => $reading,
                'reading_end'     => round($reading + $consumption, 2),
            ]);

            $reading += $consumption;
        }
    }

    private function generateCashpowerHistory(User $user, Meter $meter, int $count = 5): void
    {
        $balance = 5.0;
        for ($i = $count; $i >= 1; $i--) {
            $amounts   = [500, 1000, 2000, 5000, 10000];
            $amount    = $amounts[array_rand($amounts)];
            $kwh       = round($amount / 131, 3);
            $balAfter  = round($balance + $kwh, 3);

            CashpowerTransaction::create([
                'meter_id'           => $meter->id,
                'user_id'            => $user->id,
                'transaction_ref'    => CashpowerTransaction::generateRef(),
                'amount_cfa'         => $amount,
                'kwh_purchased'      => $kwh,
                'balance_before_kwh' => $balance,
                'balance_after_kwh'  => $balAfter,
                'status'             => 'completed',
                'payment_method'     => 'simulated',
                'token_code'         => CashpowerTransaction::generateToken(),
                'completed_at'       => now()->subDays($i * 5)->subHours(rand(0, 12)),
            ]);

            $balance = $balAfter - rand(2, 8); // simulate consumption
        }
    }
}
