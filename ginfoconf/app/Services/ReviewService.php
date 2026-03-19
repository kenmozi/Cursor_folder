<?php

namespace App\Services;

use App\Models\Review;
use App\Models\ReviewAssignment;
use App\Models\User;
use App\Enums\AssignmentStatus;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    /**
     * Save or update a review draft. Creates the review record if not yet existing.
     */
    public function saveDraft(ReviewAssignment $assignment, User $reviewer, array $data): Review
    {
        return DB::transaction(function () use ($assignment, $reviewer, $data) {
            $review = $assignment->review ?? $assignment->review()->create([
                'submission_id' => $assignment->submission_id,
                'reviewer_id'   => $reviewer->id,
                'status'        => 'draft',
            ]);

            $review->update([
                'overall_score'       => $data['overall_score'] ?? $review->overall_score,
                'recommendation'      => $data['recommendation'] ?? $review->recommendation,
                'comments_to_authors' => $data['comments_to_authors'] ?? $review->comments_to_authors,
                'comments_to_chair'   => $data['comments_to_chair'] ?? $review->comments_to_chair,
            ]);

            // Move assignment to in_progress once the reviewer starts editing
            if ($assignment->status === AssignmentStatus::Accepted) {
                $assignment->update(['status' => AssignmentStatus::InProgress]);
            }

            return $review->fresh();
        });
    }

    /**
     * Submit a completed review (locks it — only chair can unlock).
     */
    public function submitReview(Review $review): Review
    {
        if ($review->isSubmitted()) {
            abort(422, 'Review is already submitted.');
        }

        if (!$review->recommendation) {
            abort(422, 'A recommendation is required to submit the review.');
        }

        DB::transaction(function () use ($review) {
            $review->update([
                'status'       => 'submitted',
                'submitted_at' => now(),
            ]);

            $review->assignment->update([
                'status'       => AssignmentStatus::Completed,
                'completed_at' => now(),
            ]);
        });

        return $review->fresh();
    }

    /**
     * Respond to a review assignment: accept or decline.
     */
    public function respondToAssignment(ReviewAssignment $assignment, string $action, ?string $reason = null): ReviewAssignment
    {
        abort_unless(in_array($action, ['accept', 'decline']), 422, 'Invalid action.');
        abort_unless($assignment->status === AssignmentStatus::Pending, 422, 'Assignment is not pending.');

        $assignment->update([
            'status'       => $action === 'accept' ? AssignmentStatus::Accepted : AssignmentStatus::Declined,
            'responded_at' => now(),
        ]);

        return $assignment->fresh();
    }
}
