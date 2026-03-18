<?php

namespace App\Enums;

enum AssignmentStatus: string
{
    case Pending    = 'pending';
    case Accepted   = 'accepted';
    case Declined   = 'declined';
    case InProgress = 'in_progress';
    case Completed  = 'completed';
    case Reassigned = 'reassigned';
}
