<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'slug'              => $this->slug,
            'status'            => $this->status,
            'blind_mode'        => $this->blind_mode,
            'timezone'          => $this->timezone,
            'location'          => $this->location,
            'website_url'       => $this->website_url,
            'submission_open'   => $this->submission_open,
            'submission_close'  => $this->submission_close,
            'review_open'       => $this->review_open,
            'review_close'      => $this->review_close,
            'notification_date' => $this->notification_date,
            'camera_ready_date' => $this->camera_ready_date,
            'is_submission_open' => $this->isSubmissionOpen(),
            'created_at'        => $this->created_at,

            // All translations
            'translations' => $this->whenLoaded('translations'),

            // Media
            'media' => $this->whenLoaded('media', fn() =>
                $this->media->mapWithKeys(fn($m) => [$m->type => $m->url])
            ),

            // Dates with all translations
            'dates' => $this->whenLoaded('dates', fn() =>
                $this->dates->map(fn($date) => [
                    'id'           => $date->id,
                    'key'          => $date->key,
                    'date'         => $date->date,
                    'translations' => $date->translations,
                ])
            ),

            // Tracks with topics
            'tracks' => $this->whenLoaded('tracks', fn() =>
                $this->tracks->map(fn($track) => [
                    'id'           => $track->id,
                    'slug'         => $track->slug,
                    'translations' => $track->whenLoaded('translations'),
                    'topics'       => $track->whenLoaded('topics'),
                ])
            ),

            // Committee
            'committee_members' => $this->whenLoaded('committeeMembers'),
        ];
    }
}
