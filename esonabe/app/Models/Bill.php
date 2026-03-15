<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'meter_id',
        'bill_number',
        'period_start',
        'period_end',
        'due_date',
        'consumption_kwh',
        'amount_cfa',
        'taxes_cfa',
        'total_cfa',
        'status',
        'paid_at',
        'reading_start',
        'reading_end',
    ];

    protected $casts = [
        'period_start'    => 'date',
        'period_end'      => 'date',
        'due_date'        => 'date',
        'paid_at'         => 'datetime',
        'consumption_kwh' => 'decimal:3',
        'amount_cfa'      => 'decimal:2',
        'taxes_cfa'       => 'decimal:2',
        'total_cfa'       => 'decimal:2',
        'reading_start'   => 'decimal:3',
        'reading_end'     => 'decimal:3',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'paid'      => 'bg-green-100 text-green-800',
            'pending'   => 'bg-yellow-100 text-yellow-800',
            'overdue'   => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-600',
            default     => 'bg-gray-100 text-gray-800',
        };
    }

    public static function generateBillNumber(): string
    {
        do {
            $number = 'BILL-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        } while (self::where('bill_number', $number)->exists());

        return $number;
    }
}
