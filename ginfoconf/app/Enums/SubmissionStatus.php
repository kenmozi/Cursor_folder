<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Draft            = 'draft';
    case Submitted        = 'submitted';
    case UnderReview      = 'under_review';
    case Accepted         = 'accepted';
    case Rejected         = 'rejected';
    case RevisionRequired = 'revision_required';
    case Withdrawn        = 'withdrawn';
    case CameraReady      = 'camera_ready';

    public function label(): string
    {
        return match($this) {
            self::Draft            => 'Draft',
            self::Submitted        => 'Submitted',
            self::UnderReview      => 'Under Review',
            self::Accepted         => 'Accepted',
            self::Rejected         => 'Rejected',
            self::RevisionRequired => 'Revision Required',
            self::Withdrawn        => 'Withdrawn',
            self::CameraReady      => 'Camera Ready',
        };
    }

    /** Statuses visible to the author (no blind reveal of review details until decided) */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Accepted,
            self::Rejected,
            self::Withdrawn,
            self::CameraReady,
        ]);
    }

    public function canTransitionTo(self $next): bool
    {
        return match($this) {
            self::Draft            => in_array($next, [self::Submitted, self::Withdrawn]),
            self::Submitted        => in_array($next, [self::UnderReview, self::Withdrawn]),
            self::UnderReview      => in_array($next, [self::Accepted, self::Rejected, self::RevisionRequired]),
            self::RevisionRequired => in_array($next, [self::Submitted, self::Withdrawn]),
            self::Accepted         => [self::CameraReady],
            default                => false,
        };
    }
}
