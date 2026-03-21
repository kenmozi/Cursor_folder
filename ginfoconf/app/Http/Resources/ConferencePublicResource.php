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
            'website_url'       => $this->website_url,
            'location'          => $this->location,
            'city'              => $this->city,
            'contact_name'      => $this->contact_name,
            'contact_email'     => $this->contact_email,
            'contact_phone'     => $this->contact_phone,
            'contact_address'   => $this->contact_address,
            'submission_open'   => $this->submission_open,
            'submission_close'  => $this->submission_close,
            'notification_date' => $this->notification_date,
            'camera_ready_date' => $this->camera_ready_date,
            'is_submission_open' => $this->isSubmissionOpen(),

            // Localized content
            'title'                  => $t?->title,
            'subtitle'               => $t?->subtitle,
            'description'            => $t?->description,
            'cfp_text'               => $t?->cfp_text,
            'publication_guidelines' => $t?->publication_guidelines,

            // Media
            'logo'    => $this->whenLoaded('media', fn() =>
                $this->media->firstWhere('type', 'logo')?->url
            ),
            'cover'   => $this->whenLoaded('media', fn() =>
                $this->media->firstWhere('type', 'cover')?->url
            ),
            'gallery' => $this->whenLoaded('media', fn() =>
                $this->media->where('type', 'gallery')->values()->map(fn($m) => [
                    'id'  => $m->id,
                    'url' => $m->url,
                ])
            ),

            // Dates: named workflow dates + custom conference_dates
            'dates' => $this->whenLoaded('dates', fn() =>
                $this->dates->map(fn($date) => [
                    'id'    => $date->id,
                    'date'  => $date->date,
                    'label' => $date->translation($locale)?->label ?? '',
                ])
            ),

            // Tracks
            'tracks' => $this->whenLoaded('tracks', fn() =>
                $this->tracks->map(fn($track) => [
                    'id'          => $track->id,
                    'name'        => $track->translation($locale)?->name ?? $track->slug,
                    'slug'        => $track->slug,
                    'description' => $track->translation($locale)?->description,
                ])
            ),

            // Committee members grouped by committee type
            'committees' => $this->whenLoaded('committeeMembers', fn() =>
                $this->committeeMembers
                    ->groupBy('committee')
                    ->map(fn($members) => $members->map(fn($m) => [
                        'name'        => $m->name,
                        'email'       => $m->email,
                        'affiliation' => $m->affiliation,
                        'country'     => $m->country,
                        'role'        => $m->role,
                    ]))
            ),
        ];
    }
}
