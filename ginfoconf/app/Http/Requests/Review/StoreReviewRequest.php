<?php

namespace App\Http\Requests\Review;

use App\Enums\ReviewRecommendation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $submitting = $this->boolean('submit', false);

        return [
            'overall_score'       => [$submitting ? 'required' : 'nullable', 'integer', 'min:1', 'max:10'],
            'recommendation'      => [
                $submitting ? 'required' : 'nullable',
                Rule::enum(ReviewRecommendation::class),
            ],
            'comments_to_authors' => [$submitting ? 'required' : 'nullable', 'string', 'min:50'],
            'comments_to_chair'   => ['nullable', 'string'],
            'submit'              => ['boolean'],
        ];
    }
}
