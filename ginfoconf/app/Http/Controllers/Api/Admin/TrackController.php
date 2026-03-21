<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use App\Models\Track;
use App\Models\TrackTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrackController extends Controller
{
    /**
     * GET /admin/conferences/{conference}/tracks
     */
    public function index(Conference $conference): JsonResponse
    {
        $this->authorize('manage', $conference);

        $tracks = $conference->tracks()->with('translations')->get();

        return response()->json($tracks->map(fn($t) => $this->formatTrack($t)));
    }

    /**
     * POST /admin/conferences/{conference}/tracks
     * Body: name (required), description (nullable), slug (nullable)
     */
    public function store(Request $request, Conference $conference): JsonResponse
    {
        $this->authorize('manage', $conference);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'slug'        => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/'],
        ]);

        $slug = $data['slug'] ?? Str::slug($data['name']);
        $sort = $conference->tracks()->max('sort_order') + 1;

        $track = $conference->tracks()->create([
            'slug'       => $slug,
            'sort_order' => $sort,
        ]);

        TrackTranslation::create([
            'track_id'    => $track->id,
            'locale'      => 'en',
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $track->load('translations');

        return response()->json($this->formatTrack($track), 201);
    }

    /**
     * PUT /admin/conferences/{conference}/tracks/{track}
     */
    public function update(Request $request, Conference $conference, Track $track): JsonResponse
    {
        $this->authorize('manage', $conference);

        if ($track->conference_id !== $conference->id) {
            abort(404);
        }

        $data = $request->validate([
            'name'        => ['sometimes', 'required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        if (isset($data['sort_order'])) {
            $track->update(['sort_order' => $data['sort_order']]);
        }

        $translation = $track->translations()->where('locale', 'en')->first();
        if ($translation) {
            $translation->update([
                'name'        => $data['name'] ?? $translation->name,
                'description' => array_key_exists('description', $data) ? $data['description'] : $translation->description,
            ]);
        } else {
            TrackTranslation::create([
                'track_id'    => $track->id,
                'locale'      => 'en',
                'name'        => $data['name'] ?? $track->slug,
                'description' => $data['description'] ?? null,
            ]);
        }

        $track->load('translations');

        return response()->json($this->formatTrack($track));
    }

    /**
     * DELETE /admin/conferences/{conference}/tracks/{track}
     */
    public function destroy(Conference $conference, Track $track): JsonResponse
    {
        $this->authorize('manage', $conference);

        if ($track->conference_id !== $conference->id) {
            abort(404);
        }

        $track->delete();

        return response()->json(null, 204);
    }

    private function formatTrack(Track $track): array
    {
        $en = $track->translations->firstWhere('locale', 'en') ?? $track->translations->first();
        return [
            'id'          => $track->id,
            'slug'        => $track->slug,
            'sort_order'  => $track->sort_order,
            'name'        => $en?->name ?? $track->slug,
            'description' => $en?->description,
        ];
    }
}
