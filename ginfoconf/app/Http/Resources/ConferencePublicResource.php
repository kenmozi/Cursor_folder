<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConferencePublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $t      = $this->translation($locale);

        return [
            'slug'              => $this->slug,
            'status'            => $this->status,
            'blind_mode'        => $this->blind_mode,
            'timezone'          => $this->timezone,
            'location'          => $this->location,
            'website_url'       => $this->website_url,
            'submission_open'   => $this->submission_open,
            'submission_close'  => $this->submission_close,
            'notification_date' => $this->notification_date,
            'camera_ready_date' => $this->camera_ready_date,
            'is_submission_open' => $this->isSubmissionOpen(),

            // Localized content
            'title'       => $t?->title,
            'subtitle'    => $t?->subtitle,
            'description' => $t?->description,

            // Media
            'logo'  => $this->whenLoaded('media', fn() =>
                $this->media->firstWhere('type', 'logo')?->url
            ),
            'cover' => $this->whenLoaded('media', fn() =>
                $this->media->firstWhere('type', 'cover')?->url
            ),

            // Dates (localized)
            'dates' => $this->whenLoaded('dates', fn() =>
                $this->dates->map(fn($date) => [
                    'key'    => $date->key,
                    'date'   => $date->date,
                    'label'  => $date->translation($locale)?->label ?? $date->key,
                ])
            ),

            // Tracks (localized names only)
            'tracks' => $this->whenLoaded('tracks', fn() =>
                $this->tracks->map(fn($track) => [
                    'id'    => $track->id,
                    'name'  => $track->translation($locale)?->name ?? $track->slug,
                    'slug'  => $track->slug,
                ])
            ),
        ];
    }
}
