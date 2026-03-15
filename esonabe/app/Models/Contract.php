<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contract_number',
        'name',
        'address',
        'city',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meters()
    {
        return $this->hasMany(Meter::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public static function generateContractNumber(): string
    {
        do {
            $number = 'CTR-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('contract_number', $number)->exists());

        return $number;
    }
}
