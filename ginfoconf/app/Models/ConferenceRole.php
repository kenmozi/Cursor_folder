<?php

namespace App\Models;

use App\Enums\ConferenceRoleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConferenceRole extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'conference_id',
        'user_id',
        'role',
        'assigned_by',
    ];

    protected function casts(): array
    {
        return [
            'role'       => ConferenceRoleType::class,
            'created_at' => 'datetime',
        ];
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
