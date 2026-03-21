<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use App\Models\ConferenceMedia;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConferenceMediaController extends Controller
{
    public function __construct(private readonly FileService $service) {}

    /**
     * Upload or replace a logo / cover image.
     * POST /admin/conferences/{conference}/media
     * Body: type=logo|cover, file=<image>
     */
    public function store(Request $request, Conference $conference): JsonResponse
    {
        $this->authorize('manage', $conference);

        $request->validate([
            'type' => ['required', 'in:logo,cover,gallery'],
            'file' => ['required', 'file', 'image', 'mimes:jpeg,png,gif,webp', 'max:5120'],
        ]);

        $type = $request->input('type');
        $disk = 'conferences';

        if ($type === 'gallery') {
            $count = $conference->media()->where('type', 'gallery')->count();
            if ($count >= 10) {
                return response()->json(['message' => 'Maximum of 10 gallery images allowed.'], 422);
            }
        } else {
            $existing = $conference->media()->where('type', $type)->first();
            if ($existing) {
                Storage::disk($disk)->delete($existing->path);
                $existing->delete();
            }
        }

        $path = $this->service->storeConferenceMedia(
            $conference->slug,
            $type,
            $request->file('file')
        );

        $media = $conference->media()->create([
            'type'          => $type,
            'disk'          => $disk,
            'path'          => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
            'mime_type'     => $request->file('file')->getMimeType(),
            'size_bytes'    => $request->file('file')->getSize(),
        ]);

        return response()->json($media, 201);
    }

    /**
     * Delete logo or cover by type.
     */
    public function destroy(Conference $conference, string $type): JsonResponse
    {
        $this->authorize('manage', $conference);

        $media = $conference->media()->where('type', $type)->firstOrFail();
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return response()->json(null, 204);
    }

    /**
     * Delete a specific gallery image by ID.
     * DELETE /admin/conferences/{conference}/media/gallery/{media}
     */
    public function destroyGalleryItem(Conference $conference, ConferenceMedia $media): JsonResponse
    {
        $this->authorize('manage', $conference);

        if ($media->conference_id !== $conference->id || $media->type !== 'gallery') {
            abort(404);
        }

        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return response()->json(null, 204);
    }
}
