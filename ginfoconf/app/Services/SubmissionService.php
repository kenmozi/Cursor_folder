<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Models\Conference;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\SubmissionReceivedNotification;
use Illuminate\Support\Facades\DB;

class SubmissionService
{
    /**
     * Create a draft submission. Does not require open submission window.
     */
    public function createDraft(User $user, array $data): Submission
    {
        /** @var Conference $conference */
        $conference = Conference::where('slug', $data['conference_slug'])->firstOrFail();

        return DB::transaction(function () use ($user, $conference, $data) {
            /** @var Submission $submission */
            $submission = $conference->submissions()->create([
                'submitter_id' => $user->id,
                'track_id'     => $data['track_id'] ?? null,
                'title'        => $data['title'],
                'abstract'     => $data['abstract'],
                'keywords'     => $data['keywords'] ?? null,
                'status'       => SubmissionStatus::Draft,
            ]);

            $this->syncAuthors($submission, $user, $data['authors'] ?? []);
            $this->syncTopics($submission, $data['topic_ids'] ?? []);

            return $submission->load(['authors', 'topics.translations', 'files']);
        });
    }

    /**
     * Update a draft submission's metadata.
     */
    public function update(Submission $submission, array $data): Submission
    {
        DB::transaction(function () use ($submission, $data) {
            $submission->update(array_filter([
                'title'    => $data['title'] ?? null,
                'abstract' => $data['abstract'] ?? null,
                'keywords' => $data['keywords'] ?? null,
                'track_id' => $data['track_id'] ?? null,
            ], fn($v) => $v !== null));

            if (isset($data['authors'])) {
                $this->syncAuthors($submission, $submission->submitter, $data['authors']);
            }

            if (isset($data['topic_ids'])) {
                $this->syncTopics($submission, $data['topic_ids']);
            }
        });

        return $submission->fresh(['authors', 'topics', 'files']);
    }

    /**
     * Finalize and submit the paper. Requires open submission window.
     */
    public function submit(Submission $submission): Submission
    {
        if (!$submission->conference->isSubmissionOpen()) {
            abort(422, 'Submission period is closed.');
        }

        if (!$submission->hasActiveManuscript()) {
            abort(422, 'A manuscript file must be uploaded before submitting.');
        }

        DB::transaction(function () use ($submission) {
            $submission->update([
                'status'       => SubmissionStatus::Submitted,
                'submitted_at' => now(),
            ]);
        });

        // Queue notification email to the submitter
        $submission->submitter->notify(new SubmissionReceivedNotification($submission));

        return $submission->fresh();
    }

    /**
     * Withdraw a submission.
     */
    public function withdraw(Submission $submission): Submission
    {
        $submission->update(['status' => SubmissionStatus::Withdrawn]);
        return $submission->fresh();
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function syncAuthors(Submission $submission, User $submitter, array $coAuthors): void
    {
        // Delete and re-insert to preserve a clean ordered list
        $submission->authors()->delete();

        // Always insert the submitter as author 0
        $submission->authors()->create([
            'user_id'          => $submitter->id,
            'name'             => $submitter->name,
            'email'            => $submitter->email,
            'affiliation'      => $submitter->affiliation,
            'country'          => $submitter->country,
            'is_corresponding' => true,
            'sort_order'       => 0,
        ]);

        foreach ($coAuthors as $idx => $author) {
            // Try to find existing platform user by email
            $user = User::where('email', $author['email'])->first();

            $submission->authors()->create([
                'user_id'          => $user?->id,
                'name'             => $author['name'],
                'email'            => $author['email'],
                'affiliation'      => $author['affiliation'] ?? null,
                'country'          => $author['country'] ?? null,
                'is_corresponding' => $author['is_corresponding'] ?? false,
                'sort_order'       => $idx + 1,
            ]);
        }
    }

    private function syncTopics(Submission $submission, array $topicIds): void
    {
        // Validate topics belong to same conference
        if (!empty($topicIds)) {
            $validIds = $submission->conference->topics()->whereIn('id', $topicIds)->pluck('id')->toArray();
            $submission->topics()->sync($validIds);
        } else {
            $submission->topics()->detach();
        }
    }
}
