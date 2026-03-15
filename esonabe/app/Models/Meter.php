<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meter extends Model
{
    use HasFactory;

    // Meter class definitions
    const CLASSES = [
        'A'  => ['label' => 'Type A (monophasé social)',  'min_amp' => 1,  'max_amp' => 3],
        'B1' => ['label' => 'Type B1 (monophasé normal)', 'min_amp' => 5,  'max_amp' => 30],
        'B2' => ['label' => 'Type B2 (monophasé spécial)','min_amp' => 5,  'max_amp' => 30],
        'C1' => ['label' => 'Type C1 (triphasé normal)',  'min_amp' => 10, 'max_amp' => 30],
        'C2' => ['label' => 'Type C2 (triphasé spécial)', 'min_amp' => 10, 'max_amp' => 30],
    ];

    const KWH_RATE_CFA = 131; // 1 KWh = 131 CFA
    const MIN_PURCHASE_CFA = 500;
    const MAX_PURCHASE_CFA = 500000;

    // Balance thresholds in KWh
    const BALANCE_LOW    = 20;  // below -> red
    const BALANCE_MEDIUM = 50;  // below -> orange, above -> green

    protected $fillable = [
        'contract_id',
        'meter_number',
        'serial_number',
        'type',
        'meter_class',
        'amperage',
        'cashpower_balance_kwh',
        'status',
        'last_reading_at',
        'last_reading_kwh',
    ];

    protected $casts = [
        'cashpower_balance_kwh' => 'decimal:3',
        'last_reading_kwh'      => 'decimal:3',
        'last_reading_at'       => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function consumptionRecords()
    {
        return $this->hasMany(ConsumptionRecord::class)->orderBy('recorded_date', 'desc');
    }

    public function cashpowerTransactions()
    {
        return $this->hasMany(CashpowerTransaction::class)->orderBy('created_at', 'desc');
    }

    public function isCashpower(): bool
    {
        return $this->type === 'CASHPOWER';
    }

    public function getBalanceStatusAttribute(): string
    {
        if (!$this->isCashpower()) {
            return 'normal';
        }
        $balance = (float) $this->cashpower_balance_kwh;
        if ($balance < self::BALANCE_LOW) {
            return 'danger';
        }
        if ($balance < self::BALANCE_MEDIUM) {
            return 'warning';
        }
        return 'success';
    }

    public function getBalanceColorClassAttribute(): string
    {
        return match ($this->balance_status) {
            'danger'  => 'text-red-600',
            'warning' => 'text-orange-500',
            default   => 'text-green-600',
        };
    }

    public function getClassLabelAttribute(): string
    {
        return self::CLASSES[$this->meter_class]['label'] ?? $this->meter_class;
    }

    public static function generateMeterNumber(): string
    {
        do {
            $number = 'MTR-' . strtoupper(substr(md5(uniqid()), 0, 10));
        } while (self::where('meter_number', $number)->exists());

        return $number;
    }

    public static function amperageOptions(string $class): array
    {
        $info = self::CLASSES[$class] ?? null;
        if (!$info) {
            return [];
        }
        return range($info['min_amp'], $info['max_amp']);
    }
}
