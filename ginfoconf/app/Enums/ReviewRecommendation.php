<?php

namespace App\Enums;

enum ReviewRecommendation: string
{
    case StrongAccept = 'strong_accept';
    case Accept       = 'accept';
    case WeakAccept   = 'weak_accept';
    case Borderline   = 'borderline';
    case WeakReject   = 'weak_reject';
    case Reject       = 'reject';
    case StrongReject = 'strong_reject';

    public function label(): string
    {
        return match($this) {
            self::StrongAccept => 'Strong Accept',
            self::Accept       => 'Accept',
            self::WeakAccept   => 'Weak Accept',
            self::Borderline   => 'Borderline',
            self::WeakReject   => 'Weak Reject',
            self::Reject       => 'Reject',
            self::StrongReject => 'Strong Reject',
        };
    }

    public function numericScore(): int
    {
        return match($this) {
            self::StrongAccept => 7,
            self::Accept       => 6,
            self::WeakAccept   => 5,
            self::Borderline   => 4,
            self::WeakReject   => 3,
            self::Reject       => 2,
            self::StrongReject => 1,
        };
    }
}
