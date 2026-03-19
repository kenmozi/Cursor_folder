<?php

namespace App\Models;

use App\Enums\ReviewRecommendation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'assignment_id',
        'submission_id',
        'reviewer_id',
        'status',
        'overall_score',
        'recommendation',
        'comments_to_authors',
        'comments_to_chair',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'recommendation' => ReviewRecommendation::class,
            'submitted_at'   => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ReviewAssignment::class, 'assignment_id');
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }
}
