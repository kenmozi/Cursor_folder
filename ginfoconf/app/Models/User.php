<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'affiliation',
        'country',
        'bio',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'is_super_admin',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_super_admin'    => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function conferenceRoles(): HasMany
    {
        return $this->hasMany(ConferenceRole::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'submitter_id');
    }

    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class, 'reviewer_id');
    }

    public function ownedConferences(): HasMany
    {
        return $this->hasMany(Conference::class, 'owner_id');
    }

    // ── Authorization helpers ──────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function hasConferenceRole(string|array $role, Conference $conference): bool
    {
        return $this->conferenceRoles()
            ->where('conference_id', $conference->id)
            ->whereIn('role', (array) $role)
            ->exists();
    }

    public function isConferenceAdmin(Conference $conference): bool
    {
        return $this->isSuperAdmin()
            || $this->hasConferenceRole('admin', $conference);
    }

    public function isReviewerFor(Conference $conference): bool
    {
        return $this->hasConferenceRole(['reviewer', 'pc_member'], $conference);
    }

    public function isPcMemberOf(Conference $conference): bool
    {
        return $this->hasConferenceRole('pc_member', $conference);
    }
}
