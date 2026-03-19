<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\ReviewAssignment;
use App\Models\User;

class ReviewPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null;
    }

    public function view(User $user, Review $review): bool
    {
        // Reviewer can view their own review
        if ($review->reviewer_id === $user->id) {
            return true;
        }

        $conference = $review->submission->conference;

        // Admin and PC members can view all reviews (but reviewer identity is
        // hidden from authors via the Resource layer, not the policy)
        return $user->isConferenceAdmin($conference)
            || $user->isPcMemberOf($conference);
    }

    public function create(User $user, ReviewAssignment $assignment): bool
    {
        // Only the assigned reviewer can create a review
        return $assignment->reviewer_id === $user->id
            && $assignment->isActive();
    }

    public function update(User $user, Review $review): bool
    {
        // Only the reviewer can edit, and only while it's a draft
        return $review->reviewer_id === $user->id
            && $review->isDraft();
    }

    public function submit(User $user, Review $review): bool
    {
        return $review->reviewer_id === $user->id
            && $review->isDraft()
            && $review->recommendation !== null;
    }
}
