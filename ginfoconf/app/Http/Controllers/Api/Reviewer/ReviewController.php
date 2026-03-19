<?php

namespace App\Http\Controllers\Api\Reviewer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Resources\ReviewAssignmentResource;
use App\Http\Resources\ReviewResource;
use App\Models\ReviewAssignment;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $service) {}

    /**
     * List all assignments for the authenticated reviewer.
     */
    public function assignments(Request $request): AnonymousResourceCollection
    {
        $assignments = ReviewAssignment::query()
            ->where('reviewer_id', $request->user()->id)
            ->with(['submission.conference', 'review'])
            ->latest()
            ->paginate(20);

        return ReviewAssignmentResource::collection($assignments);
    }

    /**
     * Show a single assignment (reviewer must own it).
     */
    public function showAssignment(Request $request, ReviewAssignment $assignment): JsonResponse
    {
        abort_unless($assignment->reviewer_id === $request->user()->id, 403);

        $assignment->load(['submission.authors', 'submission.files', 'review']);

        return response()->json(new ReviewAssignmentResource($assignment));
    }

    /**
     * Accept or decline an assignment.
     */
    public function respondToAssignment(Request $request, ReviewAssignment $assignment): JsonResponse
    {
        abort_unless($assignment->reviewer_id === $request->user()->id, 403);

        $request->validate([
            'action' => ['required', 'in:accept,decline'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $assignment = $this->service->respondToAssignment(
            $assignment,
            $request->action,
            $request->reason
        );

        return response()->json(new ReviewAssignmentResource($assignment));
    }

    /**
     * Save or update a review draft (or submit if ?submit=true in body).
     */
    public function storeReview(StoreReviewRequest $request, ReviewAssignment $assignment): JsonResponse
    {
        abort_unless($assignment->reviewer_id === $request->user()->id, 403);

        $review = $this->service->saveDraft($assignment, $request->user(), $request->validated());

        if ($request->boolean('submit')) {
            $review = $this->service->submitReview($review);
        }

        return response()->json(new ReviewResource($review));
    }

    /**
     * Show an existing review.
     */
    public function showReview(Request $request, ReviewAssignment $assignment): JsonResponse
    {
        abort_unless($assignment->reviewer_id === $request->user()->id, 403);
        abort_unless($assignment->review, 404);

        return response()->json(new ReviewResource($assignment->review));
    }
}
