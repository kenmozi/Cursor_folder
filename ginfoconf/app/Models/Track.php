<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Track extends Model
{
    protected $fillable = ['conference_id', 'slug', 'sort_order'];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TrackTranslation::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class)->orderBy('sort_order');
    }

    public function translation(string $locale = 'en'): ?TrackTranslation
    {
        $loaded = $this->translations;
        return $loaded->firstWhere('locale', $locale)
            ?? $loaded->firstWhere('locale', 'en')
            ?? $loaded->first();
    }
}
