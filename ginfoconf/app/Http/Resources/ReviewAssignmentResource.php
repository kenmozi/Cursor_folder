<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        // Only show reviewer identity to admins (double-blind)
        $conference   = $this->submission?->conference;
        $isPrivileged = $conference && $user?->isConferenceAdmin($conference);
        $isOwner      = $user?->id === $this->reviewer_id;

        return [
            'id'           => $this->id,
            'status'       => $this->status,
            'due_date'     => $this->due_date,
            'responded_at' => $this->responded_at,
            'completed_at' => $this->completed_at,
            'created_at'   => $this->created_at,

            'submission' => $this->whenLoaded('submission', fn() => [
                'id'     => $this->submission->id,
                'title'  => $this->submission->title,
                'status' => $this->submission->status,
            ]),

            'reviewer' => $this->when($isPrivileged || $isOwner, fn() =>
                $this->whenLoaded('reviewer', fn() => [
                    'id'    => $this->reviewer->id,
                    'name'  => $this->reviewer->name,
                    'email' => $this->reviewer->email,
                ])
            ),

            'review' => $this->whenLoaded('review', fn() =>
                $this->review ? new ReviewResource($this->review) : null
            ),
        ];
    }
}
