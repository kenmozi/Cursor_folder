<?php

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Gate handled in controller via policy
    }

    public function rules(): array
    {
        return [
            'slug'                  => ['required', 'string', 'max:100', 'unique:conferences,slug', 'regex:/^[a-z0-9\-]+$/'],
            'city'                  => ['nullable', 'string', 'max:200'],
            'website_url'           => ['nullable', 'url', 'max:500'],
            'location'              => ['nullable', 'string', 'max:500'],
            'contact_name'          => ['nullable', 'string', 'max:255'],
            'contact_email'         => ['nullable', 'email', 'max:255'],
            'contact_phone'         => ['nullable', 'string', 'max:100'],
            'contact_address'       => ['nullable', 'string', 'max:1000'],
            'acronym'               => ['nullable', 'string', 'max:50'],
            'edition'               => ['nullable', 'string', 'max:50'],
            'timezone'              => ['nullable', 'string', 'max:50'],
            'blind_mode'            => ['nullable', Rule::in(['open', 'single', 'double'])],
            'min_reviewers'         => ['nullable', 'integer', 'min:1', 'max:10'],
            'max_reviewers'         => ['nullable', 'integer', 'min:1', 'max:20'],
            'max_pages'             => ['nullable', 'integer', 'min:1'],
            'submission_open'       => ['nullable', 'date'],
            'submission_close'      => ['nullable', 'date', 'after_or_equal:submission_open'],
            'review_open'           => ['nullable', 'date'],
            'review_close'          => ['nullable', 'date', 'after_or_equal:review_open'],
            'notification_date'     => ['nullable', 'date'],
            'camera_ready_deadline' => ['nullable', 'date'],

            // Initial translation (at least English required)
            'title'                 => ['required', 'string', 'max:500'],
            'subtitle'              => ['nullable', 'string', 'max:500'],
            'description'           => ['nullable', 'string'],
        ];
    }
}
