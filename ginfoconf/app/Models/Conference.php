<?php

namespace App\Models;

use App\Enums\ConferenceStatus;
use App\Enums\BlindMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'slug',
        'acronym',
        'edition',
        'timezone',
        'status',
        'blind_mode',
        'website_url',
        'location',
        'city',
        'contact_name',
        'contact_email',
        'contact_phone',
        'contact_address',
        'min_reviewers',
        'max_reviewers',
        'max_pages',
        'submission_open',
        'submission_close',
        'review_open',
        'review_close',
        'notification_date',
        'camera_ready_date',
    ];

    protected function casts(): array
    {
        return [
            'status'                 => ConferenceStatus::class,
            'blind_mode'             => BlindMode::class,
            'submission_open'        => 'datetime',
            'submission_close'       => 'datetime',
            'review_open'            => 'datetime',
            'review_close'           => 'datetime',
            'notification_date'      => 'datetime',
            'camera_ready_date'      => 'datetime',
        ];
    }

    // Use slug as the route key for URL binding: /conferences/{slug}
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ConferenceTranslation::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ConferenceMedia::class);
    }

    public function dates(): HasMany
    {
        return $this->hasMany(ConferenceDate::class)->orderBy('sort_order')->orderBy('date');
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class)->orderBy('sort_order');
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class)->orderBy('sort_order');
    }

    public function committeeMembers(): HasMany
    {
        return $this->hasMany(CommitteeMember::class)->orderBy('sort_order');
    }

    public function conferenceRoles(): HasMany
    {
        return $this->hasMany(ConferenceRole::class);
    }

    public function reviewerInvitations(): HasMany
    {
        return $this->hasMany(ReviewerInvitation::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    // ── Convenience media accessors ───────────────────────────────────────────

    public function logo(): HasOne
    {
        return $this->hasOne(ConferenceMedia::class)->where('type', 'logo')->latestOfMany('id');
    }

    public function cover(): HasOne
    {
        return $this->hasOne(ConferenceMedia::class)->where('type', 'cover')->latestOfMany('id');
    }

    // ── Translation helper ────────────────────────────────────────────────────

    /**
     * Returns the translation for the given locale,
     * falling back to English, then to the first available locale.
     */
    public function translation(string $locale = 'en'): ?ConferenceTranslation
    {
        $loaded = $this->translations;
        return $loaded->firstWhere('locale', $locale)
            ?? $loaded->firstWhere('locale', 'en')
            ?? $loaded->first();
    }

    // ── Domain helpers ────────────────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === ConferenceStatus::Active;
    }

    public function isSubmissionOpen(): bool
    {
        $now = now();
        return $this->isPublished()
            && ($this->submission_open === null || $now->gte($this->submission_open))
            && ($this->submission_close === null || $now->lte($this->submission_close));
    }

    public function isReviewOpen(): bool
    {
        $now = now();
        return ($this->review_open === null || $now->gte($this->review_open))
            && ($this->review_close === null || $now->lte($this->review_close));
    }
}
