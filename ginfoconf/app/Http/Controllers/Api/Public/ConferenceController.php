<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConferencePublicResource;
use App\Models\Conference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConferenceController extends Controller
{
    /**
     * List active/upcoming conferences (public, no auth).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $locale = app()->getLocale();

        $conferences = Conference::query()
            ->whereIn('status', ['active', 'open', 'closed', 'archived'])
            ->with([
                'translations',
                'dates.translations',
            ])
            ->latest('created_at')
            ->paginate(20);

        return ConferencePublicResource::collection($conferences)->additional([
            'locale' => $locale,
        ]);
    }

    /**
     * Show a single conference by slug (public).
     */
    public function show(Conference $conference): JsonResponse
    {
        $conference->load([
            'translations',
            'dates.translations',
            'media',
            'committeeMembers',
            'tracks.translations',
            'tracks.topics.translations',
        ]);

        return response()->json(new ConferencePublicResource($conference));
    }
}
