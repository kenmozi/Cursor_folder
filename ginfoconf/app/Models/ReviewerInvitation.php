<?php

namespace App\Models;

use App\Enums\InvitationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewerInvitation extends Model
{
    protected $fillable = [
        'conference_id',
        'inviter_id',
        'user_id',
        'email',
        'token',
        'status',
        'message',
        'decline_reason',
        'expires_at',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'status'       => InvitationStatus::class,
            'expires_at'   => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isPending(): bool
    {
        return $this->status === InvitationStatus::Pending && !$this->isExpired();
    }
}
