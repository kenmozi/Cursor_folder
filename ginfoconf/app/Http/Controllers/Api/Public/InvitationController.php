<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\ReviewerInvitation;
use App\Services\InvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function __construct(private readonly InvitationService $service) {}

    /**
     * Show invitation details by token (public — used by the accept/decline page).
     */
    public function show(string $token): JsonResponse
    {
        $invitation = ReviewerInvitation::where('token', $token)->firstOrFail();

        return response()->json([
            'id'              => $invitation->id,
            'email'           => $invitation->email,
            'status'          => $invitation->status,
            'is_expired'      => $invitation->isExpired(),
            'conference_slug' => $invitation->conference->slug,
            'conference_name' => $invitation->conference->translation()?->title ?? '',
            'expires_at'      => $invitation->expires_at,
            'message'         => $invitation->message,
        ]);
    }

    /**
     * Accept an invitation. Requires authentication (user must be logged in).
     */
    public function accept(Request $request, string $token): JsonResponse
    {
        $invitation = ReviewerInvitation::where('token', $token)->firstOrFail();

        $invitation = $this->service->accept($invitation, $request->user());

        return response()->json(['status' => $invitation->status]);
    }

    /**
     * Decline an invitation. No authentication required.
     */
    public function decline(Request $request, string $token): JsonResponse
    {
        $invitation = ReviewerInvitation::where('token', $token)->firstOrFail();

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $invitation = $this->service->decline($invitation, $data['reason'] ?? null);

        return response()->json(['status' => $invitation->status]);
    }
}
