<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'track_id',
        'submitter_id',
        'decided_by',
        'title',
        'abstract',
        'keywords',
        'status',
        'decision_note',
        'submitted_at',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'status'       => SubmissionStatus::class,
            'keywords'     => 'array',
            'submitted_at' => 'datetime',
            'decided_at'   => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function authors(): HasMany
    {
        return $this->hasMany(SubmissionAuthor::class)->orderBy('sort_order');
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'submission_topics');
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function activeManuscript(): HasOne
    {
        return $this->hasOne(SubmissionFile::class)
            ->where('type', 'manuscript')
            ->where('is_active', true)
            ->latestOfMany('id');
    }

    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ── Domain helpers ────────────────────────────────────────────────────────

    public function hasActiveManuscript(): bool
    {
        return $this->files()
            ->where('type', 'manuscript')
            ->where('is_active', true)
            ->exists();
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, [SubmissionStatus::Draft, SubmissionStatus::RevisionRequired]);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->submitter_id === $user->id
            || $this->authors()->where('user_id', $user->id)->exists();
    }
}
