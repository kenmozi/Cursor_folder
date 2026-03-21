<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conference\StoreConferenceRequest;
use App\Http\Resources\ConferenceResource;
use App\Models\Conference;
use App\Models\ConferenceTranslation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class ConferenceController extends Controller
{
    /**
     * List conferences this admin manages (or all if super admin).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $query = Conference::query()->with(['translations', 'dates']);

        if (!$user->isSuperAdmin()) {
            $managed = $user->conferenceRoles()
                ->where('role', 'admin')
                ->pluck('conference_id');
            $query->whereIn('id', $managed);
        }

        return ConferenceResource::collection($query->latest()->paginate(20));
    }

    /**
     * Create a new conference (super admin only, or delegated).
     */
    public function store(StoreConferenceRequest $request): JsonResponse
    {
        $this->authorize('create', Conference::class);

        $data = $request->validated();

        $conference = Conference::create([
            'slug'              => Str::slug($data['slug'] ?? $data['title']),
            'owner_id'          => $request->user()->id,
            'status'            => 'draft',
            'blind_mode'        => $data['blind_mode'] ?? 'double',
            'website_url'       => $data['website_url'] ?? null,
            'location'          => $data['location'] ?? null,
            'city'              => $data['city'] ?? null,
            'contact_name'      => $data['contact_name'] ?? null,
            'contact_email'     => $data['contact_email'] ?? null,
            'contact_phone'     => $data['contact_phone'] ?? null,
            'contact_address'   => $data['contact_address'] ?? null,
            'submission_open'   => $data['submission_open'] ?? null,
            'submission_close'  => $data['submission_close'] ?? null,
            'review_open'       => $data['review_open'] ?? null,
            'review_close'      => $data['review_close'] ?? null,
            'notification_date' => $data['notification_date'] ?? null,
            'camera_ready_date' => $data['camera_ready_date'] ?? null,
            'timezone'          => $data['timezone'] ?? 'UTC',
        ]);

        // Seed the initial English translation
        ConferenceTranslation::create([
            'conference_id' => $conference->id,
            'locale'        => 'en',
            'title'         => $data['title'],
            'subtitle'      => $data['subtitle'] ?? null,
            'description'   => $data['description'] ?? null,
        ]);

        // Auto-assign creator as admin
        \App\Models\ConferenceRole::create([
            'conference_id' => $conference->id,
            'user_id'       => $request->user()->id,
            'role'          => 'admin',
            'assigned_by'   => $request->user()->id,
        ]);

        $conference->load(['translations', 'dates']);

        return response()->json(new ConferenceResource($conference), 201);
    }

    /**
     * Show a single conference (admin view with full detail).
     */
    public function show(Request $request, Conference $conference): JsonResponse
    {
        $this->authorize('manage', $conference);

        $conference->load([
            'translations',
            'dates.translations',
            'media',
            'committeeMembers',
            'tracks.translations',
            'tracks.topics.translations',
        ]);

        return response()->json(new ConferenceResource($conference));
    }

    /**
     * Update conference settings (dates, blind mode, status, etc.).
     */
    public function update(Request $request, Conference $conference): JsonResponse
    {
        $this->authorize('manage', $conference);

        $data = $request->validate([
            'status'            => ['sometimes', 'string', 'in:draft,active,open,closed,archived'],
            'blind_mode'        => ['sometimes', 'string', 'in:none,single,double'],
            'submission_open'   => ['sometimes', 'nullable', 'date'],
            'submission_close'  => ['sometimes', 'nullable', 'date'],
            'review_open'       => ['sometimes', 'nullable', 'date'],
            'review_close'      => ['sometimes', 'nullable', 'date'],
            'notification_date' => ['sometimes', 'nullable', 'date'],
            'camera_ready_date' => ['sometimes', 'nullable', 'date'],
            'timezone'        => ['sometimes', 'string', 'max:64'],
            'website_url'     => ['sometimes', 'nullable', 'url'],
            'location'        => ['sometimes', 'nullable', 'string', 'max:500'],
            'city'            => ['sometimes', 'nullable', 'string', 'max:200'],
            'contact_name'    => ['sometimes', 'nullable', 'string', 'max:255'],
            'contact_email'   => ['sometimes', 'nullable', 'email', 'max:255'],
            'contact_phone'   => ['sometimes', 'nullable', 'string', 'max:100'],
            'contact_address' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ]);

        $conference->update($data);

        return response()->json(new ConferenceResource($conference->fresh(['translations', 'dates'])));
    }
}
