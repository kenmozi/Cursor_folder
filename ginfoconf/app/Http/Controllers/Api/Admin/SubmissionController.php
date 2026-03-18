<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\Conference;
use App\Models\Submission;
use App\Notifications\DecisionNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubmissionController extends Controller
{
    /**
     * List all submissions for this conference (with filtering).
     */
    public function index(Request $request, Conference $conference): AnonymousResourceCollection
    {
        $this->authorize('manage', $conference);

        $query = $conference->submissions()
            ->with(['submitter:id,name,email', 'authors', 'topics', 'assignments.reviewer:id,name'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('track_id')) {
            $query->where('track_id', $request->track_id);
        }

        return SubmissionResource::collection($query->paginate(50));
    }

    /**
     * Show a single submission with full review context.
     */
    public function show(Conference $conference, Submission $submission): JsonResponse
    {
        $this->authorize('manage', $conference);

        abort_unless($submission->conference_id === $conference->id, 404);

        $submission->load([
            'submitter:id,name,email',
            'authors',
            'topics',
            'files',
            'assignments.reviewer:id,name,email',
            'assignments.review',
        ]);

        return response()->json(new SubmissionResource($submission));
    }

    /**
     * Record a decision (accept/reject/revision_required) and optionally notify submitter.
     */
    public function decision(Request $request, Conference $conference, Submission $submission): JsonResponse
    {
        $this->authorize('manage', $conference);

        abort_unless($submission->conference_id === $conference->id, 404);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:accepted,rejected,revision_required'],
            'notify' => ['boolean'],
        ]);

        $newStatus = SubmissionStatus::from($data['status']);

        abort_unless(
            $submission->status->canTransitionTo($newStatus),
            422,
            "Cannot transition from {$submission->status->value} to {$newStatus->value}."
        );

        $submission->update(['status' => $newStatus]);

        if ($request->boolean('notify', true)) {
            $submission->submitter->notify(new DecisionNotification($submission));
        }

        return response()->json(new SubmissionResource($submission->fresh()));
    }
}
