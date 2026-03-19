<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'affiliation' => ['nullable', 'string', 'max:500'],
            'country'     => ['nullable', 'string', 'size:2'],
            'locale'      => ['nullable', 'string', 'in:en,fr,ja'],
        ];
    }
}
