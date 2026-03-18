<?php

namespace App\Http\Controllers\Api\Author;

use App\Http\Controllers\Controller;
use App\Http\Requests\Submission\StoreSubmissionRequest;
use App\Http\Requests\Submission\UpdateSubmissionRequest;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use App\Services\SubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubmissionController extends Controller
{
    public function __construct(private readonly SubmissionService $service) {}

    /**
     * List the authenticated user's own submissions.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $submissions = Submission::query()
            ->where('submitter_id', $request->user()->id)
            ->with(['conference', 'authors', 'topics', 'files'])
            ->latest()
            ->paginate(20);

        return SubmissionResource::collection($submissions);
    }

    /**
     * Create a new draft submission.
     */
    public function store(StoreSubmissionRequest $request): JsonResponse
    {
        $submission = $this->service->createDraft($request->user(), $request->validated());

        return response()->json(new SubmissionResource($submission), 201);
    }

    /**
     * Show a single submission (policy enforced).
     */
    public function show(Request $request, Submission $submission): JsonResponse
    {
        $this->authorize('view', $submission);

        $submission->load(['conference', 'authors', 'topics', 'files', 'assignments.reviewer']);

        return response()->json(new SubmissionResource($submission));
    }

    /**
     * Update a draft submission's metadata.
     */
    public function update(UpdateSubmissionRequest $request, Submission $submission): JsonResponse
    {
        $this->authorize('update', $submission);

        $submission = $this->service->update($submission, $request->validated());

        return response()->json(new SubmissionResource($submission));
    }

    /**
     * Finalize and submit (requires open window + active manuscript).
     */
    public function submit(Request $request, Submission $submission): JsonResponse
    {
        $this->authorize('update', $submission);

        $submission = $this->service->submit($submission);

        return response()->json(new SubmissionResource($submission));
    }

    /**
     * Withdraw a submission.
     */
    public function withdraw(Request $request, Submission $submission): JsonResponse
    {
        $this->authorize('update', $submission);

        $submission = $this->service->withdraw($submission);

        return response()->json(new SubmissionResource($submission));
    }
}
