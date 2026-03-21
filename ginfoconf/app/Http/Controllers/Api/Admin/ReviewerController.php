<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use App\Models\ReviewerInvitation;
use App\Services\InvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewerController extends Controller
{
    public function __construct(private readonly InvitationService $service) {}

    /**
     * List all invitations for this conference.
     */
    public function invitations(Conference $conference): AnonymousResourceCollection
    {
        $this->authorize('manage', $conference);

        $invitations = $conference->reviewerInvitations()
            ->with('inviter:id,name,email')
            ->latest()
            ->paginate(50);

        return \App\Http\Resources\ReviewerInvitationResource::collection($invitations);
    }

    /**
     * Send a new reviewer invitation.
     */
    public function invite(Request $request, Conference $conference): JsonResponse
    {
        $this->authorize('manage', $conference);

        $data = $request->validate([
            'email'   => ['required', 'email'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $invitation = $this->service->invite(
            $conference,
            $request->user(),
            $data['email'],
            $data['message'] ?? null
        );

        return response()->json($invitation, 201);
    }

    /**
     * Cancel / revoke a pending invitation.
     */
    public function cancel(Conference $conference, ReviewerInvitation $invitation): JsonResponse
    {
        $this->authorize('manage', $conference);

        abort_unless($invitation->conference_id === $conference->id, 404);
        abort_unless($invitation->isPending(), 422, 'Only pending invitations can be cancelled.');

        $invitation->update(['status' => \App\Enums\InvitationStatus::Cancelled]);

        return response()->json(null, 204);
    }

    /**
     * List confirmed reviewers for this conference.
     */
    public function reviewers(Conference $conference): JsonResponse
    {
        $this->authorize('manage', $conference);

        $reviewers = $conference->conferenceRoles()
            ->where('role', 'reviewer')
            ->with('user:id,name,email,affiliation,country')
            ->get()
            ->pluck('user');

        return response()->json(['data' => $reviewers]);
    }

    /**
     * Remove a reviewer role from this conference.
     */
    public function removeReviewer(Conference $conference, int $userId): JsonResponse
    {
        $this->authorize('manage', $conference);

        $conference->conferenceRoles()
            ->where('user_id', $userId)
            ->where('role', 'reviewer')
            ->delete();

        return response()->json(null, 204);
    }
}
