<?php

namespace App\Http\Requests\Submission;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                      => ['sometimes', 'required', 'string', 'max:500'],
            'abstract'                   => ['sometimes', 'required', 'string', 'min:100'],
            'keywords'                   => ['nullable', 'array', 'max:10'],
            'keywords.*'                 => ['string', 'max:50'],
            'track_id'                   => ['nullable', 'integer', 'exists:tracks,id'],
            'topic_ids'                  => ['nullable', 'array'],
            'topic_ids.*'                => ['integer', 'exists:topics,id'],
            'authors'                    => ['nullable', 'array'],
            'authors.*.name'             => ['required_with:authors', 'string', 'max:255'],
            'authors.*.email'            => ['required_with:authors', 'email:rfc', 'max:255'],
            'authors.*.affiliation'      => ['nullable', 'string', 'max:500'],
            'authors.*.country'          => ['nullable', 'string', 'size:2'],
            'authors.*.is_corresponding' => ['nullable', 'boolean'],
        ];
    }
}
