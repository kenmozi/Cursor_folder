<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumptionRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_id',
        'recorded_date',
        'kwh_consumed',
        'meter_reading',
        'source',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'kwh_consumed'  => 'decimal:3',
        'meter_reading' => 'decimal:3',
    ];

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }
}
