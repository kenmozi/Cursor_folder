<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conference\UpdateConferenceContentRequest;
use App\Models\Conference;
use App\Models\ConferenceTranslation;
use Illuminate\Http\JsonResponse;

class ConferenceContentController extends Controller
{
    /**
     * Upsert a translation for a specific locale.
     * PUT /admin/conferences/{conference}/content/{locale}
     */
    public function upsert(UpdateConferenceContentRequest $request, Conference $conference, string $locale): JsonResponse
    {
        $this->authorize('manage', $conference);

        $data = $request->validated();

        $translation = ConferenceTranslation::updateOrCreate(
            ['conference_id' => $conference->id, 'locale' => $locale],
            [
                'title'       => $data['title'],
                'subtitle'    => $data['subtitle'] ?? null,
                'description' => $data['description'] ?? null,
                'cfp_text'    => $data['cfp_text'] ?? null,
            ]
        );

        return response()->json($translation);
    }

    /**
     * List all available translations for a conference.
     */
    public function index(Conference $conference): JsonResponse
    {
        $this->authorize('manage', $conference);

        return response()->json($conference->translations()->get(['locale', 'title', 'subtitle']));
    }

    /**
     * Delete a locale translation (cannot delete last one).
     */
    public function destroy(Conference $conference, string $locale): JsonResponse
    {
        $this->authorize('manage', $conference);

        abort_if($conference->translations()->count() <= 1, 422, 'Cannot delete the last translation.');

        $conference->translations()->where('locale', $locale)->delete();

        return response()->json(null, 204);
    }
}
