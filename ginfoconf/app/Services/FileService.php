<?php

namespace App\Services;

use App\Enums\FileType;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    private const DISK = 'submissions'; // Defined in config/filesystems.php

    /**
     * Store an uploaded manuscript. Marks the previous active manuscript as inactive.
     */
    public function storeManuscript(Submission $submission, UploadedFile $file, User $uploader): SubmissionFile
    {
        $this->validateFile($file, FileType::Manuscript);

        // Calculate next version number
        $version = $submission->files()
            ->where('type', FileType::Manuscript->value)
            ->max('version') + 1;

        // Deactivate previous manuscript
        $submission->files()
            ->where('type', FileType::Manuscript->value)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $path = $this->store($file, "submissions/{$submission->id}/manuscripts");

        return $submission->files()->create([
            'uploaded_by'   => $uploader->id,
            'version'       => $version,
            'type'          => FileType::Manuscript->value,
            'disk'          => self::DISK,
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size_bytes'    => $file->getSize(),
            'is_active'     => true,
            'uploaded_at'   => now(),
        ]);
    }

    /**
     * Store supplementary file. Multiple allowed.
     */
    public function storeSupplementary(Submission $submission, UploadedFile $file, User $uploader): SubmissionFile
    {
        $this->validateFile($file, FileType::Supplementary);

        $path = $this->store($file, "submissions/{$submission->id}/supplementary");

        return $submission->files()->create([
            'uploaded_by'   => $uploader->id,
            'version'       => 1,
            'type'          => FileType::Supplementary->value,
            'disk'          => self::DISK,
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size_bytes'    => $file->getSize(),
            'is_active'     => true,
            'uploaded_at'   => now(),
        ]);
    }

    /**
     * Delete a file from storage and remove its database record.
     */
    public function delete(SubmissionFile $submissionFile): void
    {
        Storage::disk(self::DISK)->delete($submissionFile->path);
        $submissionFile->delete();
    }

    /**
     * Store a conference media asset (logo or cover image).
     * Returns the stored path.
     */
    public function storeConferenceMedia(string $conferenceSlug, string $type, UploadedFile $file): string
    {
        $disk      = 'conferences';
        $extension = $file->getClientOriginalExtension();
        $path      = "conferences/{$conferenceSlug}/{$type}/" . Str::uuid() . ".{$extension}";

        Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function store(UploadedFile $file, string $directory): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename  = Str::uuid() . '.' . $extension;
        $path      = $directory . '/' . $filename;

        Storage::disk(self::DISK)->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }

    private function validateFile(UploadedFile $file, FileType $type): void
    {
        if ($file->getSize() > $type->maxSizeBytes()) {
            abort(422, "File exceeds maximum allowed size for {$type->value}.");
        }

        if (!in_array($file->getMimeType(), $type->allowedMimes())) {
            abort(422, "File type {$file->getMimeType()} is not allowed for {$type->value}.");
        }
    }
}
