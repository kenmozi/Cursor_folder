<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;
use App\Enums\SubmissionStatus;

class SubmissionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null;
    }

    public function view(User $user, Submission $submission): bool
    {
        // Author, conference admin, PC member, or assigned reviewer
        return $submission->isOwnedBy($user)
            || $user->isConferenceAdmin($submission->conference)
            || $user->isPcMemberOf($submission->conference)
            || $submission->reviewAssignments()
                ->where('reviewer_id', $user->id)
                ->exists();
    }

    public function create(User $user): bool
    {
        return true; // Auth check ensures authenticated; conference open check is in service
    }

    public function update(User $user, Submission $submission): bool
    {
        return $submission->submitter_id === $user->id
            && $submission->canBeEdited();
    }

    public function uploadFile(User $user, Submission $submission): bool
    {
        return $submission->submitter_id === $user->id
            && in_array($submission->status, [
                SubmissionStatus::Draft,
                SubmissionStatus::RevisionRequired,
            ]);
    }

    public function submit(User $user, Submission $submission): bool
    {
        return $submission->submitter_id === $user->id
            && in_array($submission->status, [
                SubmissionStatus::Draft,
                SubmissionStatus::RevisionRequired,
            ])
            && $submission->hasActiveManuscript();
    }

    public function withdraw(User $user, Submission $submission): bool
    {
        return $submission->submitter_id === $user->id
            && !$submission->status->isTerminal()
            && $submission->status !== SubmissionStatus::Withdrawn;
    }
}
