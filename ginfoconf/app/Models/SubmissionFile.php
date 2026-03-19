<?php

namespace App\Models;

use App\Enums\FileType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SubmissionFile extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'uploaded_by',
        'version',
        'type',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size_bytes',
        'is_active',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'type'        => FileType::class,
            'is_active'   => 'boolean',
            'uploaded_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Generate a temporary signed URL for file download.
     * Works with both local and S3 disks.
     */
    public function temporaryUrl(int $minutes = 30): string
    {
        $disk = Storage::disk($this->disk);

        if (method_exists($disk, 'temporaryUrl')) {
            return $disk->temporaryUrl($this->path, now()->addMinutes($minutes));
        }

        // Fallback for local disk via signed route
        return route('files.download', ['file' => $this->id, 'expires' => now()->addMinutes($minutes)->timestamp]);
    }
}
