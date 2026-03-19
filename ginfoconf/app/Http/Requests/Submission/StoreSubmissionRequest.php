<?php

namespace App\Http\Requests\Submission;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'conference_slug'            => ['required', 'string', 'exists:conferences,slug'],
            'track_id'                   => ['nullable', 'integer', 'exists:tracks,id'],
            'title'                      => ['required', 'string', 'max:500'],
            'abstract'                   => ['required', 'string', 'min:100'],
            'keywords'                   => ['nullable', 'array', 'max:10'],
            'keywords.*'                 => ['string', 'max:50'],
            'topic_ids'                  => ['nullable', 'array'],
            'topic_ids.*'                => ['integer', 'exists:topics,id'],

            // Co-authors (submitter is always added automatically)
            'authors'                    => ['nullable', 'array'],
            'authors.*.name'             => ['required_with:authors', 'string', 'max:255'],
            'authors.*.email'            => ['required_with:authors', 'email:rfc', 'max:255'],
            'authors.*.affiliation'      => ['nullable', 'string', 'max:500'],
            'authors.*.country'          => ['nullable', 'string', 'size:2'],
            'authors.*.is_corresponding' => ['nullable', 'boolean'],
        ];
    }
}
