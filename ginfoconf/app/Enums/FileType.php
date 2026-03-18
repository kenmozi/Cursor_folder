<?php

namespace App\Enums;

enum FileType: string
{
    case Manuscript     = 'manuscript';
    case Supplementary  = 'supplementary';
    case CameraReady    = 'camera_ready';

    public function maxSizeBytes(): int
    {
        return match($this) {
            self::Manuscript    => 50 * 1024 * 1024,  // 50 MB
            self::Supplementary => 100 * 1024 * 1024, // 100 MB
            self::CameraReady   => 50 * 1024 * 1024,  // 50 MB
        };
    }

    public function allowedMimes(): array
    {
        return match($this) {
            self::Manuscript, self::CameraReady => ['application/pdf'],
            self::Supplementary                 => ['application/pdf', 'application/zip', 'image/png', 'image/jpeg'],
        };
    }
}
