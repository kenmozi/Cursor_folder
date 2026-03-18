<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        // Authors (non-admins) see only the public-facing recommendation
        // but NOT the reviewer identity (handled in SubmissionResource).
        // Chair / admin get everything.
        $conference   = $this->assignment->submission->conference ?? null;
        $isPrivileged = $conference && $user?->isConferenceAdmin($conference);

        return [
            'id'             => $this->id,
            'status'         => $this->status,
            'overall_score'  => $this->overall_score,
            'recommendation' => $this->recommendation,
            'submitted_at'   => $this->submitted_at,

            // Comments visible to authors (once decision made public)
            'comments_to_authors' => $this->when(
                $isPrivileged || $this->isSubmitted(),
                $this->comments_to_authors
            ),

            // Only chair / admin see private comments
            'comments_to_chair' => $this->when($isPrivileged, $this->comments_to_chair),

            // Reviewer identity — admin only (double-blind enforcement)
            'reviewer' => $this->when($isPrivileged, fn() =>
                $this->whenLoaded('reviewer', fn() => [
                    'id'    => $this->reviewer->id,
                    'name'  => $this->reviewer->name,
                    'email' => $this->reviewer->email,
                ])
            ),

            'assignment_id' => $this->assignment_id,
            'created_at'    => $this->created_at,
        ];
    }
}
