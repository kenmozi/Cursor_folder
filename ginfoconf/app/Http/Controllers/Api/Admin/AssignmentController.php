<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\AssignmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewAssignmentResource;
use App\Models\Conference;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AssignmentController extends Controller
{
    /**
     * List assignments for a submission or for the whole conference.
     */
    public function index(Request $request, Conference $conference): AnonymousResourceCollection
    {
        $this->authorize('manage', $conference);

        $query = ReviewAssignment::query()
            ->whereHas('submission', fn($q) => $q->where('conference_id', $conference->id))
            ->with(['submission:id,title,status', 'reviewer:id,name,email', 'review:id,status,submitted_at']);

        if ($request->filled('submission_id')) {
            $query->where('submission_id', $request->submission_id);
        }

        return ReviewAssignmentResource::collection($query->paginate(50));
    }

    /**
     * Assign a reviewer to a submission.
     */
    public function store(Request $request, Conference $conference, Submission $submission): JsonResponse
    {
        $this->authorize('manage', $conference);

        abort_unless($submission->conference_id === $conference->id, 404);

        $data = $request->validate([
            'reviewer_id' => ['required', 'integer', 'exists:users,id'],
            'due_date'    => ['nullable', 'date'],
        ]);

        $reviewer = User::findOrFail($data['reviewer_id']);

        // Reviewer must have the reviewer role in this conference
        abort_unless(
            $reviewer->hasConferenceRole('reviewer', $conference),
            422,
            'User is not a reviewer for this conference.'
        );

        // Idempotent: re-assign if previously declined/expired
        $assignment = ReviewAssignment::firstOrCreate(
            ['submission_id' => $submission->id, 'reviewer_id' => $reviewer->id],
            [
                'status'      => AssignmentStatus::Pending,
                'assigned_by' => $request->user()->id,
                'due_date'    => $data['due_date'] ?? null,
            ]
        );

        if (!$assignment->wasRecentlyCreated) {
            // Re-open if it was declined / expired
            $assignment->update([
                'status'      => AssignmentStatus::Pending,
                'assigned_by' => $request->user()->id,
                'due_date'    => $data['due_date'] ?? $assignment->due_date,
            ]);
        }

        return response()->json(new ReviewAssignmentResource($assignment->load(['reviewer:id,name,email'])), 201);
    }

    /**
     * Unassign a reviewer (delete the assignment if review not yet submitted).
     */
    public function destroy(Conference $conference, Submission $submission, ReviewAssignment $assignment): JsonResponse
    {
        $this->authorize('manage', $conference);

        abort_unless($submission->conference_id === $conference->id, 404);
        abort_unless($assignment->submission_id === $submission->id, 404);

        if ($assignment->review?->isSubmitted()) {
            abort(422, 'Cannot unassign a reviewer who has already submitted a review.');
        }

        $assignment->delete();

        return response()->json(null, 204);
    }
}
