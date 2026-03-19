<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topic extends Model
{
    protected $fillable = ['conference_id', 'track_id', 'sort_order'];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TopicTranslation::class);
    }

    public function submissions(): BelongsToMany
    {
        return $this->belongsToMany(Submission::class, 'submission_topics');
    }

    public function translation(string $locale = 'en'): ?TopicTranslation
    {
        $loaded = $this->translations;
        return $loaded->firstWhere('locale', $locale)
            ?? $loaded->firstWhere('locale', 'en')
            ?? $loaded->first();
    }
}
