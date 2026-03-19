<?php

namespace App\Models;

use App\Enums\AssignmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReviewAssignment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'reviewer_id',
        'assigned_by',
        'status',
        'due_date',
        'assigned_at',
        'responded_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status'       => AssignmentStatus::class,
            'due_date'     => 'date',
            'assigned_at'  => 'datetime',
            'responded_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'assignment_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, [
            AssignmentStatus::Pending,
            AssignmentStatus::Accepted,
            AssignmentStatus::InProgress,
        ]);
    }
}
