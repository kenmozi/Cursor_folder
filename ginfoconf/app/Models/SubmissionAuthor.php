<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionAuthor extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'user_id',
        'name',
        'email',
        'affiliation',
        'country',
        'is_corresponding',
        'is_presenter',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_corresponding' => 'boolean',
            'is_presenter'     => 'boolean',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
