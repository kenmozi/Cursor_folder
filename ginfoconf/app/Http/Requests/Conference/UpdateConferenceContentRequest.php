<?php

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConferenceContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Checked via policy in controller
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:500'],
            'subtitle'     => ['nullable', 'string', 'max:500'],
            'description'  => ['nullable', 'string'],
            'cfp_text'     => ['nullable', 'string'],
            'venue_text'             => ['nullable', 'string'],
            'contact_text'           => ['nullable', 'string'],
            'publication_guidelines' => ['nullable', 'string'],
        ];
    }
}
