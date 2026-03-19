<?php

namespace App\Http\Controllers\Api\Author;

use App\Enums\FileType;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionFileController extends Controller
{
    public function __construct(private readonly FileService $service) {}

    /**
     * Upload a new manuscript version.
     */
    public function storeManuscript(Request $request, Submission $submission): JsonResponse
    {
        $this->authorize('update', $submission);

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:51200'], // 50 MB in KB
        ]);

        $file = $this->service->storeManuscript($submission, $request->file('file'), $request->user());

        return response()->json([
            'id'            => $file->id,
            'type'          => $file->type,
            'version'       => $file->version,
            'original_name' => $file->original_name,
            'size_bytes'    => $file->size_bytes,
            'uploaded_at'   => $file->uploaded_at,
        ], 201);
    }

    /**
     * Upload a supplementary file.
     */
    public function storeSupplementary(Request $request, Submission $submission): JsonResponse
    {
        $this->authorize('update', $submission);

        $request->validate([
            'file' => ['required', 'file', 'max:102400'], // 100 MB in KB
        ]);

        $file = $this->service->storeSupplementary($submission, $request->file('file'), $request->user());

        return response()->json([
            'id'            => $file->id,
            'type'          => $file->type,
            'version'       => $file->version,
            'original_name' => $file->original_name,
            'size_bytes'    => $file->size_bytes,
            'uploaded_at'   => $file->uploaded_at,
        ], 201);
    }

    /**
     * Delete a supplementary file (not manuscripts — those are versioned).
     */
    public function destroy(Request $request, Submission $submission, SubmissionFile $file): JsonResponse
    {
        $this->authorize('update', $submission);

        abort_unless($file->submission_id === $submission->id, 404);
        abort_if($file->type === FileType::Manuscript->value, 422, 'Manuscripts cannot be deleted; upload a new version instead.');

        $this->service->delete($file);

        return response()->json(null, 204);
    }

    /**
     * Generate a temporary download URL for a file.
     */
    public function download(Request $request, Submission $submission, SubmissionFile $file): JsonResponse
    {
        $this->authorize('view', $submission);

        abort_unless($file->submission_id === $submission->id, 404);

        return response()->json([
            'url'        => $file->temporaryUrl(30),
            'expires_in' => 1800,
        ]);
    }
}
