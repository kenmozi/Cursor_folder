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
            'type' => ['required', 'in:logo,cover'],
            'file' => ['required', 'file', 'image', 'mimes:jpeg,png,webp', 'max:5120'], // 5 MB
        ]);

        $type = $request->input('type');
        $disk = 'conferences';

        // Delete old media of same type
        $existing = $conference->media()->where('type', $type)->first();
        if ($existing) {
            Storage::disk($disk)->delete($existing->path);
            $existing->delete();
        }

        $path = $this->service->storeConferenceMedia(
            $conference->slug,
            $type,
            $request->file('file')
        );

        $media = $conference->media()->create([
            'type' => $type,
            'disk' => $disk,
            'path' => $path,
            'url'  => Storage::disk($disk)->url($path),
        ]);

        return response()->json($media, 201);
    }

    /**
     * Delete a media asset by type.
     */
    public function destroy(Conference $conference, string $type): JsonResponse
    {
        $this->authorize('manage', $conference);

        $media = $conference->media()->where('type', $type)->firstOrFail();
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return response()->json(null, 204);
    }
}
