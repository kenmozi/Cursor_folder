<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashpowerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_id',
        'user_id',
        'transaction_ref',
        'amount_cfa',
        'kwh_purchased',
        'balance_before_kwh',
        'balance_after_kwh',
        'status',
        'payment_method',
        'token_code',
        'completed_at',
    ];

    protected $casts = [
        'amount_cfa'        => 'decimal:2',
        'kwh_purchased'     => 'decimal:3',
        'balance_before_kwh'=> 'decimal:3',
        'balance_after_kwh' => 'decimal:3',
        'completed_at'      => 'datetime',
    ];

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateRef(): string
    {
        return 'CP-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 12));
    }

    public static function generateToken(): string
    {
        // Simulate a CashPower token: 20-digit code
        $digits = str_pad((string) random_int(0, PHP_INT_MAX), 20, '0', STR_PAD_LEFT);
        $digits = substr($digits, 0, 20);
        return implode('-', str_split($digits, 4));
    }
}
