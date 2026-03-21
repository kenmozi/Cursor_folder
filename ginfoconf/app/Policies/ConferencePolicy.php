<?php

namespace App\Policies;

use App\Models\Conference;
use App\Models\User;

class ConferencePolicy
{
    /**
     * Super admins bypass all policy checks via the before() method.
     * This is called before any other method.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null; // Let individual methods decide for non-super-admins
    }

    public function viewAny(User $user): bool
    {
        // Any authenticated user can list their managed conferences
        return true;
    }

    public function view(User $user, Conference $conference): bool
    {
        return $conference->isPublished() || $user->isConferenceAdmin($conference);
    }

    public function create(User $user): bool
    {
        // Any authenticated user can propose a conference (super admin approves later)
        return true;
    }

    public function manage(User $user, Conference $conference): bool
    {
        return $user->isConferenceAdmin($conference);
    }

    public function update(User $user, Conference $conference): bool
    {
        return $user->isConferenceAdmin($conference);
    }

    public function delete(User $user, Conference $conference): bool
    {
        return $user->isConferenceAdmin($conference)
            && $conference->submissions()->count() === 0;
    }

    public function manageReviewers(User $user, Conference $conference): bool
    {
        return $user->isConferenceAdmin($conference);
    }

    public function manageCommittee(User $user, Conference $conference): bool
    {
        return $user->isConferenceAdmin($conference);
    }

    public function viewSubmissions(User $user, Conference $conference): bool
    {
        return $user->isConferenceAdmin($conference)
            || $user->isPcMemberOf($conference);
    }

    public function assignReviewers(User $user, Conference $conference): bool
    {
        return $user->isConferenceAdmin($conference);
    }

    public function makeDecision(User $user, Conference $conference): bool
    {
        return $user->isConferenceAdmin($conference);
    }
}
