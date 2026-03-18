<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user       = $request->user();
        $conference = $this->conference;

        // Double-blind: hide author info from reviewers who aren't chair/admin
        $showAuthors = !$user
            || $user->isConferenceAdmin($conference)
            || $user->id === $this->submitter_id;

        return [
            'id'           => $this->id,
            'status'       => $this->status,
            'title'        => $this->title,
            'abstract'     => $this->abstract,
            'keywords'     => $this->keywords,
            'submitted_at' => $this->submitted_at,
            'created_at'   => $this->created_at,

            'conference' => $this->whenLoaded('conference', fn() => [
                'slug'       => $this->conference->slug,
                'blind_mode' => $this->conference->blind_mode,
            ]),

            'track' => $this->whenLoaded('track', fn() => [
                'id'   => $this->track->id,
                'slug' => $this->track->slug,
            ]),

            'topics' => $this->whenLoaded('topics', fn() =>
                $this->topics->map(fn($t) => ['id' => $t->id, 'name' => $t->translation()?->name])
            ),

            'authors' => $this->when($showAuthors, fn() =>
                $this->whenLoaded('authors', fn() => $this->authors)
            ),

            'files' => $this->whenLoaded('files', fn() =>
                $this->files->map(fn($f) => [
                    'id'            => $f->id,
                    'type'          => $f->type,
                    'version'       => $f->version,
                    'original_name' => $f->original_name,
                    'size_bytes'    => $f->size_bytes,
                    'is_active'     => $f->is_active,
                    'uploaded_at'   => $f->uploaded_at,
                ])
            ),

            'assignments' => $this->whenLoaded('assignments', fn() =>
                ReviewAssignmentResource::collection($this->assignments)
            ),
        ];
    }
}
